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

require_once "db.php";
global $iLogin2;

function login2(): void
{
    global $iLogin2;
    $iLogin2++;
    if($iLogin2==2) {
        error_log("2e appel à login2()");
        return;
    }

    if (isset($_POST['username']) && isset($_POST['password'])) {
        require_once "functions.php";
        $username = $_POST['username'];
        $password = $_POST['password'];


        if(strlen($username)>=3/*&&ctype_alpha($username)*/) {
            global $db;
            if ($username !== "" && $password !== "") {
                if($db==null)
                    $db = new MyDB();
                $stmt = $db->prepare("SELECT count(*)  as yes FROM table_users where username=:username and password=:password;");
                $stmt->bindParam(":username", $username);
                $stmt->bindParam(":password", $password);
                $stmt->execute();
                $response = $stmt->fetchAll();
                if(isset($response[0])) {
                    $count = $response[0]['yes'];
                    if ($count == 1) // nom d'utilisateur et mot de passe corrects
                    {
                        /*if (session_status() === PHP_SESSION_NONE) {
                            session_start();
                        }*/
                        $_SESSION['username'] = $username;
                        $_SESSION['password'] = $password;
                        session_commit();
                        echo "<a href='index.php?page=agenda'><h2>Utilisateur connecté. Aller à l'application</h2></a>";
                        ?>Vous devriez être redirigé... vers la page d'accueil ...
                        <script type="text/javascript">
                            const delai = 3000; // Delai en secondes
                            const url = 'index.php?page=tables'; // Url de destination
                            setTimeout("document.location.replace(url)", delai);
                        </script>
                        <?php
                    }else {
                        echo "<h2 class='error'>Erreur login. Utilisateur non trouvé 1</h2>";
                    }
                } else {
                    echo "<h2 class='error'>Erreur login. Utilisateur non trouvé 2</h2>";
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
            <script src="https://www.google.com/recaptcha/api.js"></script>
            <!--            <script type="text/typescript">
                            import { initializeApp } from 'firebase/app';

                            // TODO: Replace the following with your app's Firebase project configuration
                            const firebaseConfig = {
                            };

                            const app = initializeApp(firebaseConfig);
                        </script>
            -->
            <div id="g_id_onload"
                 data-client_id="AIzaSyB_38L9B3BcrAI5ecTl8FqTSgURqnM7p58"
                 data-login_uri="https://empty3.one/agenda/src/index.php?page=login"
                 data-your_own_param_1_to_login="session_user_id"
                 data-your_own_param_2_to_login="session_user_id">
            </div>
            <form action="index.php?page=login" id="login-form" method="POST">
                <table id="login">
                    <tr>
                        <h1>Connexion</h1></tr>

                    <tr>
                        <td><label><b>Nom d'utilisateur</b></label></td>
                        <td><input type="text" placeholder="Entrer votre adresse email:" name="username" required></td>
                    </tr>
                    <td><label><b>Mot de passe</b></label></td>
                    <td><input type="password" placeholder="Entrer le mot de passe" name="password" required></td>
                    </tr>

                    <tr>
                        <td></td>
                        <td><input type="submit" id='submit' value='Log in.'></td>
                    </tr>
                <?php
                if (isset($_GET['erreur'])) {
                    $err = $_GET['erreur'];
                    if ($err == 1 || $err == 2)
                        echo "<p style='color:red'>Utilisateur ou mot de passe incorrect</p>";
                }
                ?>
                    <tr>
                        <td></td>
                        <td>
                <input type="submit" placeholder="Perte de mot de passe"
                       name="lost_password" id="lost_password" value="Récupérer mon mot de passe">
                        </td>
                    </tr>
                </table>
            </form>
            <!-- <script>
                 function onSubmit(token) {
                     document.getElementById("login-form").submit();
                 }
                 <button class="g-recaptcha"
                         data-sitekey="AIzaSyB_38L9B3BcrAI5ecTl8FqTSgURqnM7p58"
                         data-callback='onSubmit'
                         data-action='submit'>Submit</button>
             </script>
             <script>
                 function onClick(e) {
                     e.preventDefault();
                     grecaptcha.ready(function() {
                         grecaptcha.execute('AIzaSyB_38L9B3BcrAI5ecTl8FqTSgURqnM7p58', {action: 'submit'}).then(function(token) {
                             // Add your logic to submit to your backend server here.
                         });
                     });
                 }
             </script>
             <script src="https://www.google.com/recaptcha/api.js?render=reCAPTCHA_site_key"></script>
             <script>
                 function onClick(e) {
                     e.preventDefault();
                     grecaptcha.ready(function() {
                         grecaptcha.execute('reCAPTCHA_site_key', {action: 'submit'}).then(function(token) {
                             // Add your logic to submit to your backend server here.
                         });
                     });
                 }
             </script>-->
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

            <a href="?page=logout">Fermer la session</a>

            <?php
        }
    }
}

login2();

?>