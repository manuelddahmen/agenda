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

global $webmail_account, $webmail_password, $webmail_smtp_port, $webmail_smtp_addr, $db;

global $user_id;
if (isset($_POST["email"]) || (isset($_GET["email"]) && isset($_GET["attempts"]))) {
    $email = $_POST["email"] ?? $_GET["email"];
    $stmt = $db->prepare("select ref_user_id as user_id from main.table_users inner join table_recover_password on table_users.id=table_recover_password.ref_user_id where email=:email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $results0 = $stmt->fetchAll();
    if ($results0 === FALSE || (isset($_GET["attempts"]) && ((int)($_GET["attempts"])) > 1)) {

        $prepared = $db->prepare("select table_users.id as id, table_users.username as username, table_users.email as email from main.table_users where email=:email");
        $prepared->bindParam(':email', $email);
        $prepared->execute();
        $results = $prepared->fetchAll();

        if ($results == null) { ?>
            <p>Cet email ne correspond à aucun compte.</p>";
        <?php
        } else {
            $resultUserId = $results[0]["id"];
            $resultUsername = $results[0]["username"];
            $resultEmail = $results[0]["email"];

            if ($resultUserId > 0 && $resultEmail == $email) {

                $hash = md5($resultUserId . $email . time());
                $stmt = $db->prepare("insert into table_recover_password (id, ref_user_id, hash, timestamp) " .
                    " VALUES (:id, :ref_user_id, :hash, :timestamp);");
                $timestamp = time();
                $id = "" . rand(1, PHP_INT_MAX);
                $stmt->bindParam(":id", $id);
                $stmt->bindParam(":ref_user_id", $resultUserId);
                $stmt->bindParam(":hash", $hash);
                $stmt->bindParam(":timestamp", $timestamp);

                $stmt->execute();

                $to = $email;
                $subject = "Lost password: recovery email from empty3.one agendaapp";
                $message = "Bonjour $resultUsername,\r\n Récupérez votre mot de passe.\r\n" .
                    "Lien: \r\n".
                    "Votre lien :\r\n<a href='https://empty3.one/agenda/src/index.php?page=recover_password&hash=$hash&email=$email'>https://empty3.one/agenda/src/index.php?page=recover_password&hash=$hash&email=$email</a>\r\n".
                    "A utliser dans un délai de 5 min\r\n";
                $headers = 'From: checkauth@empty3.one' . "\r\n" .
                    'Reply-To: checkauth@empty3.one' . "\r\n" .
                    'X-Mailer: PHP/' . phpversion() . "\r\n";

                if (mail($to, $subject, $message, $headers)) {
                    ?>
                    <p>Votre email de récupération de mot de passe a été envoyé. <?php //echo $email; ?></p>
                    <?php
                } else {
                    ?>
                    <p class="error">Votre e-mail de récupération de mot de passe n'a pas été envoyé.</p>
                    <?php
                }
            }
        }
    } else {
        ?>
        <p class="error">Un lien a déjà été envoyé.
            <a href="index.php?page=send_password_reset_link&attempts=2&email=<?php echo $email; ?>">
                Recevoir un nouveau lien</a></p>
        <?php

    }
}


require_once "credentials.php";
$from = $webmail_account;
$to = 'test@mytest.com';

$host = $webmail_smtp_addr;
$port = $webmail_smtp_port;
$username = $webmail_account;
$password = $webmail_password;

//  $body = $message;

?>

