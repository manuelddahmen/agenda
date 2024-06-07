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

function login(): void
{

    global $connectedLogin;

    $connectedLogin = false;

    $p_username = $_POST["username"] ?? $_GET["username"];
    $p_password = $_POST["password"] ?? $_GET["password"];

    //echo $p_username." ".$p_password;
    if (isset($p_username) && isset($p_password)) {
        // connexion à la base de données
        // on applique les deux fonctions mysqli_real_escape_string et htmlspecialchars
        require_once "functions.php";
        $p_username = escapeSqlChars($p_username);
        $p_password = escapeSqlChars($p_password);

        if (strlen($p_username) >= 3 && ctype_alpha($p_username)) {
            global $db;
            $db = new MyDB();
            if ($p_username !== "" && $p_password !== "") {
                $request = "SELECT count(*)  as yes FROM table_users where username = :nom_utilisateur and password = :mot_de_passe; ";
                $stmt = $db->prepare($request);
                $stmt->bindParam("nom_utilisateur", $p_username);
                $stmt->bindParam("mot_de_passe", $p_password);
                $stmt->execute();
                $response = $stmt->fetchAll();
                $count = $response[0]['yes'];
                if ($count != 0) // nom d'utilisateur et mot de passe trouvés
                {
                    /*if (session_status() === PHP_SESSION_NONE) {
                        session_start();
                    }*/
                    $_SESSION['username'] = $p_username;
                    $_SESSION['password'] = $p_password;
                    $connectedLogin = true;
                    session_commit();
         //           echo "Login OK";
                } else {
         //           echo "<p>Erreur login. Utilisateur non trouvé count=$count<br>$p_username<br>$p_password</p>";
                    $connectedLogin = false;
                }
            }
        } else {
            echo "Nom d'utilisateur invalide : $p_username";
        }
    } else {
        if (!checkLoginForm()) {
            ?>
            <script src="https://accounts.google.com/gsi/client" async defer></script>
            <script>
                /*   function handleCredentialResponse(response) {
                       console.log("Encoded JWT ID token: " + response.credential);
                   }

                   window.onload = function () {
                       google.accounts.id.initialize({
                           client_id: "YOUR_GOOGLE_CLIENT_ID",
                           callback: handleCredentialResponse
                       });
                       google.accounts.id.renderButton(
                           document.getElementById("buttonDiv"),
                           {theme: "outline", size: "large"}  // customization attributes
                       );
                       google.accounts.id.prompt(); // also display the One Tap dialog
                   }*/
            </script>
            <div id="g_id_onload"
                 data-client_id="AIzaSyB_38L9B3BcrAI5ecTl8FqTSgURqnM7p58"
                 data-login_uri="https://empty3.one/agenda/src/index.php"
                 data-your_own_param_1_to_login="session_user_id"
                 data-your_own_param_2_to_login="session_user_id">
            </div>
            <form action="index.php?page=login" method="POST">
                <h1>Connexion</h1>

                <label><b>Nom d'utilisateur</b></label>
                <input type="text" placeholder="Entrer le nom d'utilisateur" name="username" required>

                <label><b>Mot de passe</b></label>
                <input type="password" placeholder="Entrer le mot de passe" name="password" required>

                <input type="submit" id='submit' value='Log in.'>
                <?php
                if (isset($_GET['erreur'])) {
                    $err = $_GET['erreur'];
                    if ($err == 1 || $err == 2)
                        echo "<p style='color:red'>Utilisateur ou mot de passe incorrect</p>";
                }
                ?>
                <input type="submit" placeholder="Perte de mot de passe" name="lost_password">
            </form>


            <div id="content">
                <!-- tester si l'utilisateur est connecté -->
                <?php
                if (isset($_SESSION['username'])) {
                    $user = $_SESSION['username'];
                    // afficher un message
                    echo "Bonjour $user, vous êtes connecté";

                    ?>Vous devriez être redirigé... vers
                    <script type="text/javascript">
                        const delai = 6000; // Delai en secondes
                        const url = 'index.php'; // Url de destination
                        //setTimeout("document.location.replace(url)", delai);
                    </script>-->

                    <?php
                }
                ?>


            </div>

            <a href="?page=logout">Logout (fermer la session)</a>

            <?php
        }
    }
}
