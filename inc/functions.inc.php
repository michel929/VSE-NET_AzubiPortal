<?php
/**
 * A complete login script with registration and members area.
 *
 * @author: Nils Reimers / http://www.php-einfach.de/experte/php-codebeispiele/loginscript/
 * @license: GNU GPLv3
 */
include_once("password.inc.php");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
require_once 'assets/phpmailer/src/PHPMailer.php';
require_once 'assets/phpmailer/src/Exception.php';
require_once 'assets/phpmailer/src/SMTP.php';

/**
 * Checks that the user is logged in. 
 * @return Returns the row of the logged in user
 */

function getAbteilung($id){
    global $pdo;
    $name = "Fehler";

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $pdo->prepare("SELECT * FROM Abteilungen");
    $stmt->execute();

    foreach($stmt->fetchAll() as $k) {
        if ($k["ID"] == $id) {
            $name = $k["Name"];
        }
    }
    return $name;
}

function getAzubiEmail($id){
    global $pdo;
    $name = "Fehler";

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $pdo->prepare("SELECT * FROM users");
    $stmt->execute();

    foreach($stmt->fetchAll() as $k) {
        if ($k["id"] == $id) {
            $name = $k["email"];
        }
    }
    return $name;
}

function getAzubiName($id){
    global $pdo;
    $name = "Fehler";

    $stmt = $pdo->prepare("SELECT * FROM users");
    $stmt->execute();

    foreach($stmt->fetchAll() as $k) {
        if ($k["id"] == $id) {
            $name = $k["vorname"]." ".$k["nachname"];
        }
    }
    return $name;
}

function getColor($id){
    global $pdo;
    $name = "#fff";

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $pdo->prepare("SELECT * FROM Abteilungen");
    $stmt->execute();

    foreach($stmt->fetchAll() as $k) {
        if ($k["ID"] == $id) {
            $name = $k["color"];
        }
    }
    return $name;
}

function check_user() {
	global $pdo;
	
	if(!isset($_SESSION['userid']) && isset($_COOKIE['identifier']) && isset($_COOKIE['securitytoken'])) {
		$identifier = $_COOKIE['identifier'];
		$securitytoken = $_COOKIE['securitytoken'];
		
		$statement = $pdo->prepare("SELECT * FROM securitytokens WHERE identifier = ?");
		$result = $statement->execute(array($identifier));
		$securitytoken_row = $statement->fetch();
	
		if(sha1($securitytoken) !== $securitytoken_row['securitytoken']) {
			//Vermutlich wurde der Security Token gestohlen
			//Hier ggf. eine Warnung o.ä. anzeigen
			
		} else { //Token war korrekt
			//Setze neuen Token
			$neuer_securitytoken = random_string();
			$insert = $pdo->prepare("UPDATE securitytokens SET securitytoken = :securitytoken WHERE identifier = :identifier");
			$insert->execute(array('securitytoken' => sha1($neuer_securitytoken), 'identifier' => $identifier));
			setcookie("identifier",$identifier,time()+(3600*24*365)); //1 Jahr Gültigkeit
			setcookie("securitytoken",$neuer_securitytoken,time()+(3600*24*365)); //1 Jahr Gültigkeit
	
			//Logge den Benutzer ein
			$_SESSION['userid'] = $securitytoken_row['user_id'];
		}
	}
	
	
	if(!isset($_SESSION['userid'])) {
		die('Bitte zuerst <a href="login.php">einloggen</a>');
	}
	

	$statement = $pdo->prepare("SELECT * FROM users WHERE id = :id");
	$result = $statement->execute(array('id' => $_SESSION['userid']));
	$user = $statement->fetch();
	return $user;
}

/**
 * Returns true when the user is checked in, else false
 */
function is_checked_in() {
	return isset($_SESSION['userid']);
}
 
/**
 * Returns a random string
 */
function random_string() {
	if(function_exists('openssl_random_pseudo_bytes')) {
		$bytes = openssl_random_pseudo_bytes(16);
		$str = bin2hex($bytes); 
	} else if(function_exists('mcrypt_create_iv')) {
		$bytes = mcrypt_create_iv(16, MCRYPT_DEV_URANDOM);
		$str = bin2hex($bytes); 
	} else {
		//Replace your_secret_string with a string of your choice (>12 characters)
		$str = md5(uniqid('your_secret_string', true));
	}	
	return $str;
}

/**
 * Returns the URL to the site without the script name
 */
function getSiteURL() {
	$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
	return $protocol.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'/';
}

/**
 * Outputs an error message and stops the further exectution of the script.
 */
function error($error_msg) {
	include("templates/header.inc.php");
	include("templates/error.inc.php");
	include("templates/footer.inc.php");
	exit();
}

function getUserFromID($id){
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM users");
    $stmt->execute();

    foreach($stmt->fetchAll() as $k) {
        if($k["id"] == $id){
            return $k;
        }
    }
}

function getAbteilungsID($name){
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM Abteilungen");
    $stmt->execute();

    foreach($stmt->fetchAll() as $k) {
        if($k["Name"] == $name){
            return $k["ID"];
        }
    }
}

function getAbteilungsName($name){
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM Abteilungen");
    $stmt->execute();

    foreach($stmt->fetchAll() as $k) {
        if($k["ID"] == $name){
            return $k["Name"];
        }
    }
}

function generateRandomString($length) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function sendMail($empf, $Abteilung, $start, $end, $name, $location){
    global $emailpasswort;
    $mail = new PHPMailer(true);

    $start = str_replace("-", "", $start);
    $end = str_replace("-", "", $end);
    $startTime          = '0730';
    $endTime            = '1530';
    $subject            = getAbteilungsName($Abteilung);
    $desc               = 'Für diesen Zeitraum bist du in folgender Abteilung: '.$subject;

    $organizer          = 'AzubiDashboard';
    $organizer_email    = 'azubis@artelis.net';

    $text = "BEGIN:VCALENDAR\r\n
    VERSION:2.0\r\n
    PRODID:-//Deathstar-mailer//theforce/NONSGML v1.0//EN\r\n
    METHOD:REQUEST\r\n
    BEGIN:VEVENT\r\n
    UID:" . md5(uniqid(mt_rand(), true)) . "example.com\r\n
    DTSTAMP:" . gmdate('Ymd').'T'. gmdate('His') . "Z\r\n
    DTSTART:".$start."T".$startTime."00Z\r\n
    DTEND:".$end."T".$endTime."00Z\r\n
    SUMMARY:".$subject."\r\n
    ORGANIZER;CN=".$organizer.":mailto:".$organizer_email."\r\n
    LOCATION:".$location."\r\n
    DESCRIPTION:".$desc."\r\n
    ATTENDEE;CUTYPE=INDIVIDUAL;ROLE=REQ-PARTICIPANT;PARTSTAT=NEEDS-ACTION;RSVP=TRUE;CN".$name.";X-NUM-GUESTS=0:MAILTO:".$empf."\r\n
    END:VEVENT\r\n
    END:VCALENDAR\r\n";

    try {
        //Server settings         //Enable verbose debug output
        $mail->isSMTP();                                            //Send using SMTP
        $mail->Host = '10.15.1.90';                     //Set the SMTP server to send through
        $mail->SMTPAuth = true;                                    //Enable SMTP authentication
        $mail->Username = 'azubis_artelis';                     //SMTP username
        $mail->Password = $emailpasswort;                               //SMTP password
        $mail->SMTPSecure = "tls";
        $mail->Port = 587;
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );

        $mail->setFrom('azubis@artelis.net', 'AzubiDashboard');
        $mail->addAddress($empf);

        //Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = 'Für diesen Zeitraum bist du in folgender Abteilung: '.$subject;
        $mail->AltBody = 'Für diesen Zeitraum bist du in folgender Abteilung: '.$subject;
        $mail->Ical = $text;
        $mail->send();
    } catch (Exception $e) {
        echo "<script>alert('Message could not be sent. Mailer Error: {$mail->ErrorInfo}');";
    }
}
function sendMailReg($empf, $vorname, $nachname, $passwort){
    global $emailpasswort;
    $mail = new PHPMailer(true);

    $subject            = "Dein Account für das AzubiDashboard wurde erstellt.";

    try {
        //Server settings           //Enable verbose debug output
        $mail->isSMTP();                                            //Send using SMTP
        $mail->Host = '10.15.1.90';                     //Set the SMTP server to send through
        $mail->SMTPAuth = true;                                    //Enable SMTP authentication
        $mail->Username = 'azubis_artelis';                     //SMTP username
        $mail->Password = $emailpasswort;                               //SMTP password
        $mail->SMTPSecure = "tls";
        $mail->Port = 587;
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );

        $mail->setFrom('azubis@artelis.net', 'AzubiDashboard');
        $mail->addAddress($empf);

        //Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->CharSet   = 'UTF-8';
        $mail->Encoding  = 'base64';
        $mail->Body    = '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
<!--[if (gte mso 9)|(IE)]>
  <xml>
    <o:OfficeDocumentSettings>
    <o:AllowPNG/>
    <o:PixelsPerInch>96</o:PixelsPerInch>
  </o:OfficeDocumentSettings>
</xml>
<![endif]-->
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1"> <!-- So that mobile will display zoomed in -->
<meta http-equiv="X-UA-Compatible" content="IE=edge"> <!-- enable media queries for windows phone 8 -->
<meta name="format-detection" content="telephone=no"> <!-- disable auto telephone linking in iOS -->
<meta name="format-detection" content="date=no"> <!-- disable auto date linking in iOS -->
<meta name="format-detection" content="address=no"> <!-- disable auto address linking in iOS -->
<meta name="format-detection" content="email=no"> <!-- disable auto email linking in iOS -->
<meta name="color-scheme" content="only">
<title></title>

<link rel="preconnect" href="https://fonts.gstatic.com">
<link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">

<style type="text/css">
/*Basics*/
body {margin:0px !important; padding:0px !important; display:block !important; min-width:100% !important; width:100% !important; -webkit-text-size-adjust:none;}
table {border-spacing:0; mso-table-lspace:0pt; mso-table-rspace:0pt;}
table td {border-collapse: collapse;mso-line-height-rule:exactly;}
td img {-ms-interpolation-mode:bicubic; width:auto; max-width:auto; height:auto; margin:auto; display:block!important; border:0px;}
td p {margin:0; padding:0;}
td div {margin:0; padding:0;}
td a {text-decoration:none; color: inherit;} 
/*Outlook*/
.ExternalClass {width: 100%;}
.ExternalClass,.ExternalClass p,.ExternalClass span,.ExternalClass font,.ExternalClass td,.ExternalClass div {line-height:inherit;}
.ReadMsgBody {width:100%; background-color: #ffffff;}
/* iOS BLUE LINKS */
a[x-apple-data-detectors] {color:inherit !important; text-decoration:none !important; font-size:inherit !important; font-family:inherit !important; font-weight:inherit !important; line-height:inherit !important;} 
/*Gmail blue links*/
u + #body a {color:inherit;text-decoration:none;font-size:inherit;font-family:inherit;font-weight:inherit;line-height:inherit;}
/*Buttons fix*/
.undoreset a, .undoreset a:hover {text-decoration:none !important;}
.yshortcuts a {border-bottom:none !important;}
.ios-footer a {color:#aaaaaa !important;text-decoration:none;}
/*Responsive*/
@media screen and (max-width: 639px) {
  table.row {width: 100%!important;max-width: 100%!important;}
  td.row {width: 100%!important;max-width: 100%!important;}
  .img-responsive img {width:100%!important;max-width: 100%!important;height: auto!important;margin: auto;}
  .center-float {float: none!important;margin:auto!important;}
  .center-text{text-align: center!important;}
  .container-padding {width: 100%!important;padding-left: 15px!important;padding-right: 15px!important;}
  .container-padding10 {width: 100%!important;padding-left: 10px!important;padding-right: 10px!important;}
  .container-padding25 {width: 100%!important;padding-left: 25px!important;padding-right: 25px!important;}
  .hide-mobile {display: none!important;}
  .menu-container {text-align: center !important;}
  .autoheight {height: auto!important;}
  .m-padding-10 {margin: 10px 0!important;}
  .m-padding-15 {margin: 15px 0!important;}
  .m-padding-20 {margin: 20px 0!important;}
  .m-padding-30 {margin: 30px 0!important;}
  .m-padding-40 {margin: 40px 0!important;}
  .m-padding-50 {margin: 50px 0!important;}
  .m-padding-60 {margin: 60px 0!important;}
  .m-padding-top10 {margin: 30px 0 0 0!important;}
  .m-padding-top15 {margin: 15px 0 0 0!important;}
  .m-padding-top20 {margin: 20px 0 0 0!important;}
  .m-padding-top30 {margin: 30px 0 0 0!important;}
  .m-padding-top40 {margin: 40px 0 0 0!important;}
  .m-padding-top50 {margin: 50px 0 0 0!important;}
  .m-padding-top60 {margin: 60px 0 0 0!important;}
  .m-height10 {font-size:10px!important;line-height:10px!important;height:10px!important;}
  .m-height15 {font-size:15px!important;line-height:15px!important;height:15px!important;}
  .m-height20 {font-size:20px!important;line-height:20px!important;height:20px!important;}
  .m-height25 {font-size:25px!important;line-height:25px!important;height:25px!important;}
  .m-height30 {font-size:30px!important;line-height:30px!important;height:30px!important;}
  .rwd-on-mobile {display: inline-block!important;padding: 5px!important;}
  .center-on-mobile {text-align: center!important;}
}
</style>
</head>

<body Simpli style="margin-top: 0; margin-bottom: 0; padding-top: 0; padding-bottom: 0; width: 100%; -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%;" bgcolor="#F0F0F0">

<span class="preheader-text" Simpli style="color: transparent; height: 0; max-height: 0; max-width: 0; opacity: 0; overflow: hidden; visibility: hidden; width: 0; display: none; mso-hide: all;"></span>

<div  style="display:none; font-size:0px; line-height:0px; max-height:0px; max-width:0px; opacity:0; overflow:hidden; visibility:hidden; mso-hide:all;"></div>

<table border="0" align="center" cellpadding="0" cellspacing="0" width="100%" style="width:100%;max-width:100%;">
  <tr><!-- Outer Table -->
    <td align="center" Simpli bgcolor="#F0F0F0" data-composer>

<table border="0" align="center" cellpadding="0" cellspacing="0" role="presentation" class="row container-padding" width="640" style="width:640px;max-width:640px;" Simpli>
  <!-- Preheader -->
  <tr>
    <td height="20" style="font-size:20px;line-height:20px;" Simpli>&nbsp;</td>
  </tr>
  <tr>
    <td height="30" style="font-size:30px;line-height:30px;" Simpli>&nbsp;</td>
  </tr>
  <tr>
    <td align="center" class="center-text">
      <img style="width:220px;border:0px;display: inline!important;" src="http://10.15.48.213/assets/images/vsenetlogo.png" width="220" border="0" editable="true" Simpli data-image-edit  Simpli alt="logo">
    </td>
  </tr>
  <tr>
    <td height="30" style="font-size:30px;line-height:30px;" Simpli>&nbsp;</td>
  </tr>
  <!-- Preheader -->
</table>


<table border="0" align="center" cellpadding="0" cellspacing="0" class="row" role="presentation" width="640" style="width:640px;max-width:640px;" Simpli>
  <!-- simpli-header-2 -->
  <tr>
    <td align="center">

<table border="0" align="center" cellpadding="0" cellspacing="0" role="presentation" class="row container-padding25" width="600" style="width:600px;max-width:600px;">
  <!-- basic-info -->
  <tr>
    <td align="center" Simpli bgcolor="#FFFFFF"  style="border-radius:0 0 36px 36px; border-bottom:solid 6px #DDDDDD;">
      <!-- content -->
      <table border="0" align="center" cellpadding="0" cellspacing="0" role="presentation" class="row container-padding" width="520" style="width:520px;max-width:520px;">
       <tr>
          <td height="40" style="font-size:40px;line-height:40px;" Simpli>&nbsp;</td>
        </tr>
        <tr>
          <td class="center-text" Simpli align="center" style="font-family:Arial,Helvetica,sans-serif;font-size:12px;line-height:24px;font-weight:900;font-style:normal;color:#1898c2;text-decoration:none;letter-spacing:2px;">
              <singleline>
                <div mc:edit Simpli>
                  WILLKOMMEN BEIM
                </div>
              </singleline>
          </td>
        </tr>
        <tr>
          <td class="center-text" Simpli align="center" style="font-family:Arial,Helvetica,sans-serif;font-size:42px;line-height:54px;font-weight:700;font-style:normal;color:#333333;text-decoration:none;letter-spacing:0px;">
              <singleline>
                <div mc:edit Simpli>
                  AZUBI DASHBOARD
                </div>
              </singleline>
          </td>
        </tr>
        <tr>
          <td height="15" style="font-size:15px;line-height:15px;" Simpli>&nbsp;</td>
        </tr>
        <tr>
          <td class="center-text" Simpli align="center" style="font-family:Arial,Helvetica,sans-serif;font-size:16px;line-height:26px;font-weight:300;font-style:normal;color:#333333;text-decoration:none;letter-spacing:0px;">
              <singleline>
                <div mc:edit Simpli>
                  Hallo '.$vorname.', dir wurde ein Account im Azubis Dashboard angelegt. Benutze den Button um dich anzumelden. Vergiss aber nicht dein Passwort zu ändern. <br><br> Dein Passwort lautet: <br> <strong>'.$passwort.'</strong>
                </div>
              </singleline>
          </td>
        </tr>
        <tr>
          <td height="25" style="font-size:25px;line-height:25px;" Simpli>&nbsp;</td>
        </tr>
        <tr>
          <td align="center">
            <!-- Button -->
            <table border="0" cellspacing="0" cellpadding="0" role="presentation" align="center" class="center-float">
              <tr>
                <td align="center"  Simpli bgcolor="#ff7775" style="border-radius: 6px;">
            <!--[if (gte mso 9)|(IE)]>
              <table border="0" cellpadding="0" cellspacing="0" align="center">
                <tr>
                  <td align="center" width="35"></td>
                  <td align="center" height="50" style="height:50px;">
                  <![endif]-->
                    <singleline>
                      <a href="http://10.15.48.213/login.php" target="_blank" mc:edit  style="font-family:Arial,Helvetica,sans-serif;font-size:16px;line-height:20px;font-weight:700;font-style:normal;color:#FFFFFF;text-decoration:none;letter-spacing:0px;padding: 15px 35px 15px 35px;display: inline-block;"><span>Zum Login</span></a>
                    </singleline>
                  <!--[if (gte mso 9)|(IE)]>
                  </td>
                  <td align="center" width="35"></td>
                </tr>
              </table>
            <![endif]-->
                </td>
              </tr>
            </table>
            <!-- Buttons -->
          </td>
        </tr>
        <tr>
          <td height="40" style="font-size:40px;line-height:40px;" Simpli>&nbsp;</td>
        </tr>
      </table>
      <!-- content -->
    </td>
  </tr>
  <!-- basic-info -->
</table>

    </td>
  </tr>
  <!-- simpli-header-2 -->
</table>

<table border="0" align="center" cellpadding="0" cellspacing="0" role="presentation" width="100%" style="width:100%;max-width:100%;" Simpli>
  <!-- simpli-footer -->
  <tr>
    <td align="center">
      
<!-- Content -->
<table border="0" align="center" cellpadding="0" cellspacing="0" role="presentation" class="row container-padding" width="520" style="width:520px;max-width:520px;">

  <tr>
    <td height="30" style="font-size:30px;line-height:30px;" Simpli>&nbsp;</td>
  </tr>
  <tr>
    <td align="center" class="center-text">
      <img style="width:80px;border:0px;display: inline!important;" src="http://10.15.48.213/assets/images/footer.png" width="80" border="0" editable="true" Simpli data-image-edit  Simpli alt="logo">
    </td>
  </tr>
  <tr>
    <td height="50" style="font-size:50px;line-height:50px;" Simpli>&nbsp;</td>
  </tr>
</table>
<!-- Content -->

    </td>
  </tr>
  <!-- simpli-footer -->
</table>

    </td>
  </tr><!-- Outer-Table -->
</table>

</body>
</html>
';
        $mail->AltBody = 'Hallo '.$vorname.' '.$nachname.', dir wurde ein AzubiAccount im Azubis Dashboard angelegt benutze folgenden Link um dich anzumelden. -> http://10.15.48.213/login.php  Dein Passwort lautet: '.$passwort .'Vergiss nicht es zu ändern.';
        $mail->send();
    } catch (Exception $e) {
        echo "<script>alert('Message could not be sent. Mailer Error: {$mail->ErrorInfo}');";
    }
}
?>