

<div class="app-sidebar">
    <div class="logo">
        <a href="index.html" class="logo-icon"><span class="logo-text">VSENET</span></a>
        <div class="sidebar-user-switcher user-activity-online">
            <a href="#">
                <img src="https://chat.artelis.net/avatar/<?php echo $user["nachname"]; ?>.<?php echo $user["vorname"]; ?>">
                <span class="activity-indicator"></span>
                <span class="user-info-text"><?php echo $user["vorname"]; ?> <?php echo $user["nachname"]; ?><br><span class="user-state-info"><?php echo $user["abteilung"]; ?></span></span>
            </a>
        </div>
    </div>
    <div class="app-menu">
        <ul class="accordion-menu">
            <li class="sidebar-title">
                Apps
            </li>
            <li class="active-page">
                <a href="internal.php" class="active"><i class="material-icons-two-tone">dashboard</i>Dashboard</a>
            </li>
            <li>
                <a href="meeting.php"><i class="material-icons-two-tone">inbox</i>Meeting</a>
            </li>
            <li>
                <a href="file-manager.html"><i class="material-icons-two-tone">cloud_queue</i>Deine Bewertungsbögen</a>
            </li>
            <li>
                <a href="calendar.php"><i class="material-icons-two-tone">calendar_today</i>Calendar<span class="badge rounded-pill badge-success float-end">14</span></a>
            </li>
            <li>
                <a href="todo.html"><i class="material-icons-two-tone">done</i>Berichtsheft</a>
            </li>
            <li class="sidebar-title">
                Ausbilder
            </li>
            <li>
                <a href="#"><i class="material-icons-two-tone">color_lens</i>Ausbildungsplan<i class="material-icons has-sub-menu">keyboard_arrow_right</i></a>
                <ul class="sub-menu">
                    <li><a href="einteilen.php">Azubi einteilen</a>
                    </li>
                    <li>
                        <a href="ausbildungsplan.php">Übersicht</a>
                    </li>
                </ul>
            </li>
            <li>
                <a href="#"><i class="material-icons-two-tone">grid_on</i>Abteilungen<i class="material-icons has-sub-menu">keyboard_arrow_right</i></a>
                <ul class="sub-menu">
                    <li>
                        <a href="tables-basic.html">Erstellen</a>
                    </li>
                    <li>
                        <a href="tables-datatable.html">Bearbeiten</a>
                    </li>
                </ul>
            </li>
            <li>
                <a href=""><i class="material-icons-two-tone">sentiment_satisfied_alt</i>Azubis<i class="material-icons has-sub-menu">keyboard_arrow_right</i></a>
                <ul class="sub-menu">
                    <li>
                        <a href="create-azubi.php">Anlegen / Importieren</a>
                    </li>
                    <li>
                        <a href="ui-avatars.html">Bearbeiten</a>
                    </li>
                </ul>
            </li>
            <li>
                <a href=""><i class="material-icons-two-tone">sentiment_satisfied_alt</i>Bewertungsbögen<i class="material-icons has-sub-menu">keyboard_arrow_right</i></a>
                <ul class="sub-menu">
                    <li>
                        <a href="file-manager.php">Alle Bewertungsbögen</a>
                    </li>
                    <li>
                        <a href="beurteilungsbogen.php">Erstellen / Ausfüllen</a>
                    </li>
                </ul>
            </li>

            <li class="sidebar-title">
                ADMINISTRATOR
            </li>
            <li>
                <a href="#"><i class="material-icons-two-tone">view_agenda</i>Ausbilder<i class="material-icons has-sub-menu">keyboard_arrow_right</i></a>
                <ul class="sub-menu">
                    <li>
                        <a href="create-ausbilder.php">Anlegen</a>
                    </li>
                    <li>
                        <a href="content-section-headings.html">Bearbeiten</a>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
</div>
