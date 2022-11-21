<?php
    session_start();
    require_once("inc/config.inc.php");
    require_once("inc/functions.inc.php");

$error_msg = "";
if (isset($_POST['email']) && isset($_POST['passwort'])) {
    $email = $_POST['email'];
    $passwort = $_POST['passwort'];

    $statement = $pdo->prepare("SELECT * FROM users WHERE email = :email");
    $result = $statement->execute(array('email' => $email));
    $user = $statement->fetch();

    //Überprüfung des Passworts
    if ($user !== false && password_verify($passwort, $user['passwort'])) {
        $_SESSION['userid'] = $user['id'];

        //Möchte der Nutzer angemeldet beleiben?
        if (isset($_POST['angemeldet_bleiben'])) {
            $identifier = random_string();
            $securitytoken = random_string();

            $insert = $pdo->prepare("INSERT INTO securitytokens (user_id, identifier, securitytoken) VALUES (:user_id, :identifier, :securitytoken)");
            $insert->execute(array('user_id' => $user['id'], 'identifier' => $identifier, 'securitytoken' => sha1($securitytoken)));
            setcookie("identifier", $identifier, time() + (3600 * 24 * 365)); //Valid for 1 year
            setcookie("securitytoken", $securitytoken, time() + (3600 * 24 * 365)); //Valid for 1 year
        }

        header("Location: internal.php");
        exit;
    } else {
        $error_msg = "E-Mail oder Passwort war ungültig<br><br>";
    }
}

$email_value = "";
if(isset($_POST['email']))
    $email_value = htmlentities($_POST['email']);
?>
<!DOCTYPE html>
<html lang="de">
<?php
include "inc/head.inc.php";
?>
<body>
<div class="app app-auth-sign-in align-content-stretch d-flex flex-wrap justify-content-end">
    <div class="app-auth-background">

    </div>
    <div class="app-auth-container">
        <div class="logo">
            <a href="index.html">VSE NET AzubiPortal</a>
        </div>
        <?php
        if(isset($error_msg) && !empty($error_msg)) {
            echo $error_msg;
        }
        ?>
        <br>
        <form action="login.php" method="post">
            <div class="auth-credentials m-b-xxl">
                <label for="signInEmail" class="form-label">Email address</label>
                <input type="email"name="email" class="form-control m-b-md" id="signInEmail" aria-describedby="signInEmail" placeholder="example@artelis.net" required>

                <label for="signInPassword" class="form-label">Password</label>
                <input type="password" name="passwort" class="form-control" id="signInPassword" aria-describedby="signInPassword" placeholder="&#9679;&#9679;&#9679;&#9679;&#9679;&#9679;&#9679;&#9679;" required>
            </div>

            <div class="auth-submit">
                <button class="btn btn-primary" type="submit">Login</button>
                <a href="#" class="auth-forgot-password float-end">Forgot password?</a>
            </div>
        </form>
    </div>
</div>

<?php include "inc/script.inc.php"; ?>

</body>
</html>