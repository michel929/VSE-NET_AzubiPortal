<?php
session_start();
require_once("inc/config.inc.php");
require_once("inc/functions.inc.php");

//Überprüfe, dass der User eingeloggt ist
//Der Aufruf von check_user() muss in alle internen Seiten eingebaut sein
$user = check_user();

if(isset($_GET["id"])){
    $k = getUserFromID($_GET["id"]);
}else {

}

if(isset($_COOKIE["Save"])){
    setcookie("Save","",time()-(3600*24*365));
    for($i = 0; $i < count($_COOKIE) - 3; $i++){
        if($_COOKIE[$i] != null) {
            $id = $_GET["id"];
            $cookie = $_COOKIE[$i];
            $cookie = explode("?", $cookie);
            $start = $cookie[1];
            $end = $cookie[2];
            $titel = $cookie[0];
            $titel = getAbteilungsID($titel);

            $sql = "INSERT INTO Ausbildungsplan (AzubiID, AbteilungsID, StartDate, EndDate) VALUES ('$id', '$titel', '$start', '$end')";
            // use exec() because no results are returned
            $pdo->exec($sql);

            setcookie($i,"",time()-(3600*24*365));

            $email = getAzubiEmail($id);
            $name = getAzubiName($id);
            sendMail($email, $titel, $start, $end, $name, "Büro");
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
                                <a class="nav-link language-dropdown-toggle" href="#" id="languageDropDown" data-bs-toggle="dropdown"><img src="./assets/images/flags/us.png" alt=""></a>
                                <ul class="dropdown-menu dropdown-menu-end language-dropdown" aria-labelledby="languageDropDown">
                                    <li><a class="dropdown-item" href="#"><img src="./assets/images/flags/germany.png" alt="">German</a></li>
                                    <li><a class="dropdown-item" href="#"><img src="./assets/images/flags/italy.png" alt="">Italian</a></li>
                                    <li><a class="dropdown-item" href="#"><img src="./assets/images/flags/china.png" alt="">Chinese</a></li>
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
                                                            <img src="./assets/images/avatars/avatar.png" alt="">
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
                                                            <img src="./assets/images/avatars/avatar.png" alt="">
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
            <a href="#" class="content-menu-toggle btn btn-primary"><i class="material-icons">menu</i> content</a>
            <div class="content-menu content-menu-right">
                <ul class="list-unstyled">
                    <?php
                    $stmt = $pdo->prepare("SELECT * FROM users WHERE rolle = 'azubi'");
                    $stmt->execute();

                    foreach($stmt->fetchAll() as $k) {
                        if($_GET["id"] == $k["id"]){
                            echo '<li><a href="#" class="active">'.$k["vorname"].' '. $k["nachname"].'</a></li>';
                        }else {
                            echo '<li><a href="?id='.$k["id"].'">'.$k["vorname"].' '. $k["nachname"].'</a></li>';
                        }
                    }
                    ?>
                    <li class="divider"></li>
                    <li><a href="#">Shared with me</a></li>
                    <li><a href="#">My Collections</a></li>
                    <li><a href="#">Settings</a></li>
                    <li style="margin-left: 30px"><a class="btn btn-outline-success" onclick="save()" href="?id=<?php echo $_GET["id"]; ?>">Speichern</a></li>
                    <li style="margin-left: 30px"><a class="btn btn-outline-danger" onclick="cancel()" href="?id=<?php echo $_GET["id"]; ?>">Abbruch</a></li>
                </ul>
            </div>
            <div class="content-wrapper">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col">
                            <div class="card calendar-container">
                                <div class="card-body">
                                    <div id="calendar"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"><?php echo $k["vorname"] ?>  <?php echo $k["nachname"] ?>s Kalender</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <label for="basic-url" class="form-label">Abteilung wählen</label>
                <div class="input-group mb-3">
                <select class="form-select" onchange="change();" id="select" aria-label="Default select example">
                    <option selected>Wähle deine Abteilung</option>
                    <?php
                    $stmt = $pdo->prepare("SELECT * FROM Abteilungen");
                    $stmt->execute();

                    foreach($stmt->fetchAll() as $f) {
                        echo '<option value="'.$f["id"].'">'.$f["Name"].'</option>';
                    }
                    ?>
                </select>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include "inc/script.inc.php"; ?>
<script>

    var calendar;
    var start;
    var end;

    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');

        calendar = new FullCalendar.Calendar(calendarEl, {
            initialDate: '<?php echo date("Y-m-d"); ?>',
            locale: 'de',
            firstDay: 1,
            selectable: true,
            weekends: false,
            height: "100%",
            dayMaxEvents: true, // allow "more" link when too many events
            events: [
                <?php
                $stmt = $pdo->prepare("SELECT * FROM Ausbildungsplan WHERE AzubiID = ".$_GET["id"]);
                $stmt->execute();

                foreach($stmt->fetchAll() as $s) {
                    echo "{
                    title: '".getAbteilung($s['AbteilungsID'])."',
                    start: '".$s['StartDate']."',
                    end: '".$s['EndDate']."',
                    id: 'a'
                },";
                }
                ?>

            ],

            select: function( info ){
                $('#modal').modal('show');
                start = info.startStr;
                end = info.endStr;
            }

        });

        calendar.render();
    });


    function save(){
        $(window).unbind('beforeunload');
        var events = calendar.getEvents();
        events.forEach(saveCookie);
        document.cookie = "Save=1;";
    }

    function cancel(){
        $(window).unbind('beforeunload');
    }

    function saveCookie(item, index) {
        var x = 0;
        if(item.id != 'a') {
            document.cookie = x + "=" + item.title + "?" + item.startStr + "?" + item.endStr + ";";
            x++;
        }
    }

    function change(){
        $('#modal').modal('hide');
        var e = document.getElementById("select");
        var value = e.value;
        var text = e.options[e.selectedIndex].text;

        if(text != "Wähle deine Abteilung"){
            calendar.addEvent(
                {
                    title: text,
                    start: start,
                    end: end,
                    backgroundColor: '#fff'
                }
            )
        }

        $(window).bind('beforeunload', function() {
            return 'Wollen Sie die Seite wirklich verlassen?';
        });
    }
</script>
</body>
</html>
