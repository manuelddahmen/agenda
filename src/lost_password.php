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

require_once "db.php";


if(isset($_POST["lost_password"])) {
    $email = $_POST["username"] ?? "";

    ?>
    <form action="index.php?page=send_password_reset_link" method="post">
        <input type="text" name="email" value="<?php echo $email; ?>">

        <!-- CAPCHA -->
        <input type="submit" id='submit' value='Log in.'>
        <?php
        if (isset($_GET['erreur'])) {
            $err = $_GET['erreur'];
            if ($err == 1 || $err == 2)
                echo "<p style='color:red'>Utilisateur ou mot de passe incorrect</p>";
        }
        ?>

    </form>
    <?php

}

