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
global $password;

class AgendaUser {
    private MyDB $db;
    private array $user_data = array();

    public function __construct($username, $email = NULL)
    {
        global $password;
        if($username!=null) {
            global $db;

            if($db==NULL) {
                $db = $this->db = new MyDB();
            }

            if ($email != NULL) {
                $stmt = $db->prepare("select * from table_users where email=:email");// and :password=:passowrd
                $stmt->bindParam("email", $email);
                if ($stmt->execute()) {
                    $var = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    if (is_array($var) && isset($var[0])) {
                        $this->user_data = $var[0];
                        print_r($this->user_data);
                        $username = $this->user_data["username"];
                        $password = $this->user_data["password"];
                    }
                }

            }

            $stmt = $db->prepare("select * from table_users where username=:username");// and :password=:passowrd
            $stmt->bindParam("username", $username);
            if ($stmt->execute()) {
                $var = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if (is_array($var) && isset($var[0])) {
                    $this->user_data = $var[0];
                    print_r($this->user_data);
                    $username = $this->user_data["username"];
                    $password = $this->user_data["password"];
                }
            }
        }
    }

    public function getData() {
        return $this->user_data;
    }

    public static function createUser() : bool{
        echo "Create user profile";
        return true;
    }

}


global $username, $user, $userData;
if($userData==null || !isset($userData["id"])) {
    global $username;
    $user = new AgendaUser($username);
    $userData = $user->getData();
} else if($userData!=null && isset($userData["id"])){
    $user_id = $userData["id"];
}

?>

