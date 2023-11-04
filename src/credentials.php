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

// -->JS React.
//require_once "user.php";


class credentials {
    private User $siteUser;
    private GoogleUser $googleUser;
    private function areEmailCompatible(): void
    {}
    private User $choseUser;

    function toString() : string {
        $s= "<ul>";
        if($this->choseUser!=NULL) {
            $s .= "<li>";
            $s .= $this->choseUser->email;
            $s .= (is_subclass_of($this->choseUser, GoogleUser::class))?"Google":"";
            $s .= "</li>";
        }
        $s.= "</ul>";
        return $s;
    }
}

$webmail_pop3_addr = "pop.one.com";
$webmail_pop3_port = 995;
$webmail_smtp_addr = "send.one.com";
$webmail_smtp_port = 465;
$webmail_account = "checkauth@empty3.one";
$webmail_password = "PvB9ZpeY%GtM$(99";

?>