<?php
/*
 * Copyright (c) 2023-2024. Manuel Daniel Dahmen
 *
 *

 *
 *    Licensed under the Apache License, Version 2.0 (the "License");
 *    you may not use this file except in compliance with the License.
 *    You may obtain a copy of the License at
 *
 *        http://www.apache.org/licenses/LICENSE-2.0
 *
 *    Unless required by applicable law or agreed to in writing, software
 *    distributed under the License is distributed on an "AS IS" BASIS,
 *    WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *    See the License for the specific language governing permissions and
 *    limitations under the License.
 */

require_once "framework.php";

global $userData;
if($userData==NULL) {
    echo "<h2>Non connecté</h2>";
    exit(0);
}
if (isset($_GET["action"]) && $_GET["action"] == "backup") {
    if (checkLoginForm()) {
        global $username;
        $name = "database_agenda_bak2022-07";//_";//.$username;
        $from = "../data_agenda/".$name;
        echo $dest= "../backup/backup_of_".$name ."__". date("Y-m-d H.i.u") . "--" ;//. $_SESSION['username'];
        if (copy($from, $dest, stream_context_create())){

            echo "Database saved";

            global $db;


            $stmt = $db->prepare("SELECT sql FROM sqlite_master");
            $stmt->execute();
            $results = $stmt->fetchAll();

            foreach ($results as $line) {
                echo "<p>" . $line["sql"] . "</p>";
            }
            echo "<a href='$dest'>Saved current copy, download it now it won't be available in the future.</a>";
        } else {
            echo "<a class='error'> error backup database. Login failed</a>";
        }
    }
}
?>
<ul>
    <li>Username: <?php if(isset($_SESSION['username'])) echo $_SESSION['username'];
        ?></li>
    <li class="btn-new"><a href="?page=parametres&action=backup">Backup</a></li>
</ul>
<li class="btn-new"><a href="logout.php">Logout</a></li>
</ul>