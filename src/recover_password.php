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

global $db;

$password = null;
$email = null;
$username = null;
$updated = false;

if (isset($_GET["hash"]) && isset($_GET["email"])) {

    $hash = $_GET["hash"];
    $email = $_GET["email"];

    $prepared = $db->prepare("select table_users.id as id, table_users.username as username, table_users.email as email, " .
        " table_users.password as password from main.table_users where email=:email");
    $prepared->bindParam(':email', $email);
    $prepared->execute();
    $results = $prepared->fetchAll();

    if (count($results) > 0) {
        $resultUserId = $results[0]["id"];
        $resultUsername = $results[0]["username"];
        $resultEmail = $results[0]["email"];

        $stmt = $db->prepare("select ref_user_id as ref_user_id, timestamp as timestamp from table_recover_password where hash=:hash;");
        $stmt->bindParam("hash", $hash);
        $stmt->execute();
        $result = $stmt->fetchAll();
        if ($result == null) {
            ?>
            <p class="error">Ne peut pas afficher le formulaire: requête erronée</p>

            <?php


        } else {
            $user_id2 = $result[0]["ref_user_id"];
            $timestamp = $result[0]["timestamp"];


            if (isset($_GET["submit"])) {
                $password1 = $_GET["password1"];
                $password2 = $_GET["password2"];
                $email = $_GET["email"];
                $username = $_GET["username"];
                if ($password1 == $password2 && strlen($password1) >= 8 &&
                    $email == $resultEmail && $username == $resultUsername && $resultUserId == $user_id2) {
                    $prepared = $db->prepare("update table_users set password=:password where id=:id");
                    $prepared->bindParam("password", $password1);
                    $prepared->bindParam("id", $resultUserId);
                    $bool = $prepared->execute();
                    if ($bool > 0) {

                        $prepared = $db->prepare("delete from table_recover_password where ref_user_id=:id");//hash=:hash and
                        $prepared->bindParam("hash", $password1);
                        $prepared->bindParam("id", $resultUserId);
                        $bool = $stmt->execute();

                        if ($bool > 0) {
                            $updated = true;
                        }
                    }
                }
            } else if ($resultUserId == $user_id2 && $user_id2 > 0 && !$updated) {
                if ((time() - $timestamp) > 10 * 60) {
                    ?>
                    <p class="error">Ne peut pas afficher le formulaire: temps écoulé (depuis plus de 10 min)</p>

                    <?php
                } else {
                    ?>
                    <div class="table">
                        <form action="index.php" method="get">
                            <div>
                                <label for="password1">Mot de passe
                                    <input id="password1" name="password1" type="password"
                                           value="<?php echo $password1; ?>"/>
                                </label>
                            </div>
                            <div>
                                <label for="password2">Confirmation de mot de passe
                                    <input name="password2" id="password2" type="password"
                                           value="<?php echo $password2; ?>"/>
                                </label>
                            </div>
                            <div>
                                <label for="email">Email
                                    <input name="email" id="email" type="text" value="<?php echo $email; ?>"/>
                                </label>
                            </div>
                            <div>
                                <label for="username">Nom d'utilisateur
                                    <input name="username" id="username" type="text"
                                           value="<?php echo $resultUsername; ?>"/>
                                </label>
                            </div>
                            <input name="hash" type="hidden" value="<?php echo $hash; ?>"/>
                            <input name="page" type="hidden" value="recover_password"/>
                            <input name="submit" type="submit" value="Enregistrer">
                        </form>
                    </div>
                    <?php
                }
            } else if ($updated) {
                ?>
                <p class="btn-new">Mot de passe mis à jour</p>
                <?php
            } else {
                ?>
                <p class="error">Ne peut pas afficher le formulaire.</p>
                <?php
                //echo "resultUserId=$resultUserId user_id2=$user_id2 updated="($updated ? "true" : "false)");
            }
        }
    }

}