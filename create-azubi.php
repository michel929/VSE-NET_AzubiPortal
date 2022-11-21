<?php
session_start();
require_once("inc/config.inc.php");
require_once("inc/functions.inc.php");

//Überprüfe, dass der User eingeloggt ist
//Der Aufruf von check_user() muss in alle internen Seiten eingebaut sein
$user = check_user();

$errormsg = "";

if(isset($_POST["vorname"])){
    $vorname1 = $_POST["vorname"];
    $nachname1 = $_POST["nachname"];
    $email1 = $_POST["email"];
    $beruf = $_POST["beruf"];
    $passwort = generateRandomString(10);
    $error = false;

    if($beruf == "Choose..."){
        $errormsg = '<div class="alert alert-custom alert-indicator-top indicator-danger" role="alert">
    <div class="alert-content">
        <span class="alert-title">Fehler beim anlegen des Azubis</span>
        <span class="alert-text">Du musst einen Beruf auswählen.</span>
    </div>
</div>';
        $error = true;
    }

    if(!$error) {
        $statement = $pdo->prepare("SELECT * FROM users WHERE email = :email");
        $result = $statement->execute(array('email' => $email1));
        $users = $statement->fetch();

        if($users !== false) {
            $errormsg = '<div class="alert alert-custom alert-indicator-top indicator-danger" role="alert">
    <div class="alert-content">
        <span class="alert-title">Fehler beim anlegen des Azubis</span>
        <span class="alert-text">Diese E-Mail ist bereits vergeben.</span>
    </div>
</div>';
            $error = true;
        }
    }

    if(!$error) {
        $passwort_hash = password_hash($passwort, PASSWORD_DEFAULT);

        $statement = $pdo->prepare("INSERT INTO users (email, passwort, vorname, nachname, abteilung) VALUES (:email, :passwort, :vorname, :nachname, :abteilung)");
        $result = $statement->execute(array('email' => $email1, 'passwort' => $passwort_hash, 'vorname' => $vorname1, 'nachname' => $nachname1, 'abteilung' => $beruf));

        if($result) {
            $errormsg = '
<div class="alert alert-custom alert-indicator-top indicator-success" role="alert">
    <div class="alert-content">
        <span class="alert-title">Erfolgreich angelegt</span>
        <span class="alert-text">Der Azubi wurde erfolgreich angelegt.</span>
    </div>
</div>
';

            sendMailReg($email1, $vorname1, $nachname1, $passwort);
        } else {
            $errormsg =  '<div class="alert alert-custom alert-indicator-top indicator-danger" role="alert">
    <div class="alert-content">
        <span class="alert-title">Fehler beim anlegen des Azubis</span>
        <span class="alert-text">Beim speichern kam es zu einem Fehler.</span>
    </div>
</div>';
        }
    }
}
?>


<!DOCTYPE html>
<html lang="de">
<?php include "inc/head.inc.php"; ?>
<body>
<div class="app align-content-stretch d-flex flex-wrap">
    <?php include "inc/sidebar.inc.php"; ?>
    <div class="app-container">
        <div class="search">
            <form>
                <input class="form-control" type="text" placeholder="Type here..." aria-label="Search">
            </form>
            <a href="#" class="toggle-search"><i class="material-icons">close</i></a>
        </div>
        <div class="app-header">
            <nav class="navbar navbar-light navbar-expand-lg">
                <div class="container-fluid">
                    <div class="navbar-nav" id="navbarNav">
                        <ul class="navbar-nav">
                            <li class="nav-item">
                                <a class="nav-link hide-sidebar-toggle-button" href="#"><i class="material-icons">first_page</i></a>
                            </li>
                            <li class="nav-item dropdown hidden-on-mobile">
                                <a class="nav-link dropdown-toggle" href="#" id="addDropdownLink" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="material-icons">add</i>
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="addDropdownLink">
                                    <li><a class="dropdown-item" href="#">New Workspace</a></li>
                                    <li><a class="dropdown-item" href="#">New Board</a></li>
                                    <li><a class="dropdown-item" href="#">Create Project</a></li>
                                </ul>
                            </li>
                            <li class="nav-item dropdown hidden-on-mobile">
                                <a class="nav-link dropdown-toggle" href="#" id="exploreDropdownLink" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="material-icons-outlined">explore</i>
                                </a>
                                <ul class="dropdown-menu dropdown-lg large-items-menu" aria-labelledby="exploreDropdownLink">
                                    <li>
                                        <h6 class="dropdown-header">Repositories</h6>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="#">
                                            <h5 class="dropdown-item-title">
                                                Neptune iOS
                                                <span class="badge badge-warning">1.0.2</span>
                                                <span class="hidden-helper-text">switch<i class="material-icons">keyboard_arrow_right</i></span>
                                            </h5>
                                            <span class="dropdown-item-description">Lorem Ipsum is simply dummy text of the printing and typesetting industry.</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="#">
                                            <h5 class="dropdown-item-title">
                                                Neptune Android
                                                <span class="badge badge-info">dev</span>
                                                <span class="hidden-helper-text">switch<i class="material-icons">keyboard_arrow_right</i></span>
                                            </h5>
                                            <span class="dropdown-item-description">Lorem Ipsum is simply dummy text of the printing and typesetting industry.</span>
                                        </a>
                                    </li>
                                    <li class="dropdown-btn-item d-grid">
                                        <button class="btn btn-primary">Create new repository</button>
                                    </li>
                                </ul>
                            </li>
                        </ul>

                    </div>
                    <div class="d-flex">
                        <ul class="navbar-nav">
                            <li class="nav-item hidden-on-mobile">
                                <a class="nav-link active" href="#">Applications</a>
                            </li>
                            <li class="nav-item hidden-on-mobile">
                                <a class="nav-link" href="#">Reports</a>
                            </li>
                            <li class="nav-item hidden-on-mobile">
                                <a class="nav-link" href="#">Projects</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link toggle-search" href="#"><i class="material-icons">search</i></a>
                            </li>
                            <li class="nav-item hidden-on-mobile">
                                <a class="nav-link language-dropdown-toggle" href="#" id="languageDropDown" data-bs-toggle="dropdown"><img src="../../assets/images/flags/us.png" alt=""></a>
                                <ul class="dropdown-menu dropdown-menu-end language-dropdown" aria-labelledby="languageDropDown">
                                    <li><a class="dropdown-item" href="#"><img src="../../assets/images/flags/germany.png" alt="">German</a></li>
                                    <li><a class="dropdown-item" href="#"><img src="../../assets/images/flags/italy.png" alt="">Italian</a></li>
                                    <li><a class="dropdown-item" href="#"><img src="../../assets/images/flags/china.png" alt="">Chinese</a></li>
                                </ul>
                            </li>
                            <li class="nav-item hidden-on-mobile">
                                <a class="nav-link nav-notifications-toggle" id="notificationsDropDown" href="#" data-bs-toggle="dropdown">4</a>
                                <div class="dropdown-menu dropdown-menu-end notifications-dropdown" aria-labelledby="notificationsDropDown">
                                    <h6 class="dropdown-header">Notifications</h6>
                                    <div class="notifications-dropdown-list">
                                        <a href="#">
                                            <div class="notifications-dropdown-item">
                                                <div class="notifications-dropdown-item-image">
                                                        <span class="notifications-badge bg-info text-white">
                                                            <i class="material-icons-outlined">campaign</i>
                                                        </span>
                                                </div>
                                                <div class="notifications-dropdown-item-text">
                                                    <p class="bold-notifications-text">Donec tempus nisi sed erat vestibulum, eu suscipit ex laoreet</p>
                                                    <small>19:00</small>
                                                </div>
                                            </div>
                                        </a>
                                        <a href="#">
                                            <div class="notifications-dropdown-item">
                                                <div class="notifications-dropdown-item-image">
                                                        <span class="notifications-badge bg-danger text-white">
                                                            <i class="material-icons-outlined">bolt</i>
                                                        </span>
                                                </div>
                                                <div class="notifications-dropdown-item-text">
                                                    <p class="bold-notifications-text">Quisque ligula dui, tincidunt nec pharetra eu, fringilla quis mauris</p>
                                                    <small>18:00</small>
                                                </div>
                                            </div>
                                        </a>
                                        <a href="#">
                                            <div class="notifications-dropdown-item">
                                                <div class="notifications-dropdown-item-image">
                                                        <span class="notifications-badge bg-success text-white">
                                                            <i class="material-icons-outlined">alternate_email</i>
                                                        </span>
                                                </div>
                                                <div class="notifications-dropdown-item-text">
                                                    <p>Nulla id libero mattis justo euismod congue in et metus</p>
                                                    <small>yesterday</small>
                                                </div>
                                            </div>
                                        </a>
                                        <a href="#">
                                            <div class="notifications-dropdown-item">
                                                <div class="notifications-dropdown-item-image">
                                                        <span class="notifications-badge">
                                                            <img src="../../assets/images/avatars/avatar.png" alt="">
                                                        </span>
                                                </div>
                                                <div class="notifications-dropdown-item-text">
                                                    <p>Praesent sodales lobortis velit ac pellentesque</p>
                                                    <small>yesterday</small>
                                                </div>
                                            </div>
                                        </a>
                                        <a href="#">
                                            <div class="notifications-dropdown-item">
                                                <div class="notifications-dropdown-item-image">
                                                        <span class="notifications-badge">
                                                            <img src="../../assets/images/avatars/avatar.png" alt="">
                                                        </span>
                                                </div>
                                                <div class="notifications-dropdown-item-text">
                                                    <p>Praesent lacinia ante eget tristique mattis. Nam sollicitudin velit sit amet auctor porta</p>
                                                    <small>yesterday</small>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
        </div>
        <div class="app-content">
            <div class="content-wrapper">
                <div class="container">
                    <div class="row"><center><div class="col-md-6"><?php echo $errormsg; ?></div></center></div>
                    <div class="row">
                        <div class="col">
                            <div class="page-description">
                                <h1>Azubi anlegen</h1>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xl-12">
<form class="row g-3" method="post">
    <div class="col-md-6">
        <label for="inputPassword4" class="form-label">Vorname</label>
        <input type="text" name="vorname" class="form-control" id="vorname" onchange="onText()" required>
    </div>
    <div class="col-md-6">
        <label for="inputPassword4" class="form-label">Nachname</label>
        <input type="text" name="nachname" class="form-control" id="nachname" onchange="onText()" required>
    </div>
    <div class="col-md-6">
        <label for="inputEmail4" class="form-label">E-Mail</label>
        <input type="email" name="email" value="@artelis.net" id="email" class="form-control" id="inputEmail4" required>
    </div>
    <div class="col-md-6">
        <label for="inputState" class="form-label">Ausbildungsberuf</label>
        <select name="beruf" class="form-select" required>
            <option selected>Choose...</option>
            <option value="ITSE">ITSE</option>
            <option value="FISI">FISI</option>
            <option value="FAE">FAE</option>
        </select>
    </div>

    <div class="col-12">
        <button type="submit" class="btn btn-primary">Azubi anlegen</button>
    </div>
</form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include "inc/script.inc.php"; ?>

<script>
    function onText(){
        var vorname = document.getElementById('vorname').value;
        vorname = vorname.replace("ü", "ue");
        vorname = vorname.replace("ö", "oe");
        vorname = vorname.replace("ä", "äe");

        var nachname = document.getElementById('nachname').value;
        nachname = nachname.replace("ü", "ue");
        nachname = nachname.replace("ö", "oe");
        nachname = nachname.replace("ä", "ae");

        document.getElementById("vorname").value = vorname;
        document.getElementById("nachname").value = nachname;
        document.getElementById("email").value = vorname.toLowerCase() + "." + nachname.toLowerCase() + "@artelis.net";
    }
</script>
</body>
</html>