<?php
/*
 * Copyright (c) 2023. Manuel Daniel Dahmen
 *
 *
 *    Copyright 2012-2023 Manuel Daniel Dahmen
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
    global $username;

    $p_username = $_POST["username"]??$_GET["username"];
    $p_password = $_POST["password"]??$_GET["password"];

    if ($p_username && isset($p_password)) {
        // connexion à la base de données
        // on applique les deux fonctions mysqli_real_escape_string et htmlspecialchars
        require_once "functions.php";
        $username = escapeSqlChars($p_username);
        $password = escapeSqlChars($p_password);


        if(strlen($username)>=3&&ctype_alpha($username)) {
            global $db;
            $db = new MyDB();
            if ($username !== "" && $password !== "") {
            $request = "SELECT count(*)  as yes FROM table_utilisateurs where 
              nom_utilisateur = '" . $username . "' and mot_de_passe = '" . $password . "' ";
            $stmt = $db->prepare($request);
            $stmt->execute();
            $response = $stmt->fetchAll();
            $count = $response[0]['yes'];
            if ($count != 0) // nom d'utilisateur et mot de passe corrects
            {
                /*if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }*/
                $_SESSION['username'] = $username;
                $_SESSION['password'] = $password;
                session_commit();
                echo "Login OK";
                echo "<a href='?page=agenda'>Aller à l\'application</a>";
            } else {
                echo "Erreur login. Utilisateur non trouvé";
            }
        }
        } else {
            echo "Nom d'utilisateur invalide : {$username}";
        }
    } else {
        if (!checkLoginForm()) {
            ?>
            <script src="https://accounts.google.com/gsi/client" async defer></script>
            <script>
                function handleCredentialResponse(response) {
                    console.log("Encoded JWT ID token: " + response.credential);
                }
                window.onload = function () {
                    google.accounts.id.initialize({
                        client_id: "YOUR_GOOGLE_CLIENT_ID",
                        callback: handleCredentialResponse
                    });
                    google.accounts.id.renderButton(
                        document.getElementById("buttonDiv"),
                        { theme: "outline", size: "large" }  // customization attributes
                    );
                    google.accounts.id.prompt(); // also display the One Tap dialog
                }
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
                        const url = 'vue_semaine.php'; // Url de destination
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
