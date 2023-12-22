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

require_once "SQLitePDO.php";

///?????
$username = $username ?? (isset($_SESSION['username']) ? $_SESSION["username"] : "");

class MyDB
{
    public ?SQLitePDO $handle = NULL;

    function __construct($database="passwords")
    {
        global $username;
        global $db;

        if (!isset($username)) {
            error_log("Pas d'utilisateur");
            $username = $_SESSION["username"] ?? "";
            if ($username == "") {
                error_log("username not set");
            }
        }

        if(($database=="passwords" || $username=="" )&&isset($_POST["username"])) {
            $username = $_POST["username"];
        }

        if ($database=="passwords" || (isset($username) && strlen($username) >= 3)) {
            //$absolutePath = "/customers/c/0/a/empty3.one/httpd.www/agenda/data_agenda/";
            $absolutePath = "../data_agenda/";
            $oldFilename = $absolutePath . "database_agenda";

            //echo $oldFilename;
            if (file_exists($oldFilename)) {
                $this->handle = new SQLitePDO($oldFilename);

                $this->handle->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
                error_log("No error".$oldFilename);
                //echo "No error";
            } else {
                error_log("Error : no database");
                error_log("Username : {{$username}}");
                echo "<p><a href='?page=create_data'>Create database for User: {{$username}}</a></p>";
            }
        } else {
            error_log("Error Constructor: no valid user");
            return;

        }
        error_log("Constructor");
        error_log("MyDB instance: " . ($this->handle != NULL)?"exists()":"not exists()");
    }


    function backup()
    {

    }

    public function query($query)
    {
        $this->handle->query($query );
    }

    public function execute($array)
    {
        if ($this->handle == null) {
            $db = new MyDB();
            $this->handle = $db->handle;
        }
        if ($this->handle == null) {
            return null;
        }
        return $this->handle->execute($array);
    }

    public function prepare($sql)
    {
        global $db;

        if ($this->handle == null) {
            $db = new MyDB();
            $this->handle = $db->handle;
        }
        if ($this->handle == null) {
            return null;
        }

        try {
            return $this->handle->prepare($sql);
        } catch (Exception $exception) {
            print_r($exception);
            print_r("<b>" . $sql . "</b>");
        }
    }

    public function exec(string $sql)
    {
        try {
            return $this->handle->exec($sql);
        } catch (Exception $exception) {
            echo "<b>".$sql."</b>";
            print_r($exception);
            exit(-1);
    }
    }
}


function db_make_a_copy($username)
{
    $db = AgendaUser::createUser();

}

function delete_data($username) : void
{
        global $db;
        global $userData;

        $tableName = array( "table_activite_christine",
            "table_activites",
            "table_activites_autonomie",
            "table_assoc_personnel_activ",
            "table_employes",
            "table_groupes",
            "table_hospitalises",
            "table_notes",
            "table_personnel",
            "table_taches",
            "table_taches_autonomie",
            "table_taches_patients",
            "table_utilisateurs"
        );
        foreach ( $tableName as $tablename) {
            try {
                $stmt = $db->prepare("delete from $tablename where user_id=:id;");
                $stmt->bindParam("id", $userData["id"]);
                $stmt->execute();
            } catch (Exception $ex) {
                echo "<p>".($ex->getTraceAsString())."</p>";
            }
        }


}
function delete_archive(int $id)
{

    global $db;
    global $userData;

    $filename = (int)getArchiveFilename($id);

    if(unlink($filename)) {
        echo "<p>archive file deleted</p>";
    }
}

function delete_user($username): void
{
    global $db;
    global $userData;

    try {
        $stmt = $db->prepare("delete from table_users where id=:id;");
        $stmt->bindParam("id", $userData["id"]);
        $stmt->execute();
    } catch (Exception $ex) {
        echo "<p>".($ex->getTraceAsString())."</p>";
    }

}

function downloads(int $id): void
{
    global $db;
    global $userData;
    $filename = getArchiveFilename($id);
    $tableName = array("table_activite_christine",
        "table_activites",
        "table_activites_autonomie",
        "table_assoc_personnel_activ",
        "table_employes",
        "table_groupes",
        "table_hospitalises",
        "table_notes",
        "table_personnel",
        "table_taches",
        "table_taches_autonomie",
        "table_taches_patients",
        "table_utilisateurs"
    );

    $fp = fopen($filename, "w+");

    if (!$fp) {

    } else {
        foreach ($tableName as $tablename) {
            try {
                $stmt = $db->prepare("select * from $tablename where user_id=:id;");
                $stmt->bindParam("id", $userData["id"]);
                $results = $stmt->execute();


                if(!is_bool($results)) {
                    write_table_xml($fp, $results, $tablename);
                }
            } catch (Exception $ex) {
                echo "<p>" . ($ex->getTraceAsString()) . "</p>";
            }
        }

        try {
            $tablename = "table_users";
            $stmt = $db->prepare("select * from $tablename where id=:id;");
            $stmt->bindParam("id", $userData["id"]);
            $results = $stmt->execute();

            if(!is_bool($results)) {
                write_table_xml($fp, $results, $tablename);
            }
        } catch (Exception $ex) {
            echo "<p>" . ($ex->getTraceAsString()) . "</p>";
        }

        echo "<a href='$filename'>Download the archive file</a>";
        echo "<a href='?page=delete_archive'>Delete the archive file</a>";
    }
}

function getArchiveFilename(int $id):String
{
    return "data_agenda.$id.xml";

}

function write_table_xml($fp, array $results, $tablename)
{
    global $db;
    fwrite($fp, "<table>$tablename</table>\n");
    foreach ($results as $row) {
        foreach ($row as $key =>$value) {
            fwrite($fp, "\t<field name='$key'>$value</field");
        }
        fwrite($fp, "\n</table>");
    }
}
