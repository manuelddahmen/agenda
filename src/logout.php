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

if (session_status() === PHP_SESSION_NONE) {
    session_start();
    $_SESSION["username"] = null;
    $_SESSION["password"] = null;

    session_commit();

    echo "<h1>No session. User not connected</h1>";
} else {
    $_SESSION["username"] = null;
    $_SESSION["password"] = null;

    session_commit();

    session_destroy();

    echo "<h1>Session closed. User not connected</h1>";


}
?><h1><a href="index.php?page=login">Se connecter</a></h1>
<h1><a href="index.php?page=create_user">Créer un utilisateur</a></h1>
<script>
    document.location.href = "index.php";
</script>