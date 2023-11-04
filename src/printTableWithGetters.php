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

function condition00($condition)
{
    $condition = "";
    $first = true;
    foreach ($_GET as $key0 => $get) {
        if (str_starts_with($key0, "field-")) {
            if (!$first)
                $condition .= " and ";
            else {
                $first = false;
                $condition = " where ";
            }
            $substr = substr($key0, strlen("field-"));
            $condition .= " " . $substr . "=" . $get;
        }
    }
    return $condition;
}
function condition($fields, $where): string
{
    $first = true;
    $condition = "";
    foreach ($fields as $key => $get) {
        if (str_starts_with($key, "field-like-")) {
            $key = substr($key, strlen("field-like-"));
            $char0 = ' LIKE ';
        }else
            $char0 = ' = ';
            if (!$first)
                $condition .= " and ";
            else {
                $first = false;
                $condition .= " ".($where?"where":"and")." ";
            }
            $condition .= " " . escapeSqlChars($key) . $char0 . escapeSqlChars($get);
    }
    return $condition;
}
function printTableWithGetter($sql, $fields, $id_url)
{
    if(!str_contains($sql, 'where'))
        $condition = condition($fields, false);
    else
        $condition = condition($fields, true);
    if(isset($condition))
        $sql .=" ".$condition.";";
    $db = new MyDB();
    if (!$db) {
        //echo $db->lastErrorMsg();
    } else {
        //echo "Opened database successfully\n";
    }
    try {
        $stmt = $db->prepare($sql);
        $stmt->execute();
        //$stmt->setFetchMode(PDO::FETCH_CLASS);
        $result = $stmt->fetchAll();
    } catch (Exception $exception) {
        print_r($exception);
        print_r("||<b>".$sql."</b>");
    }
    echo "<table class='table_database_'>";

    for ($i = 0; $i < count($result); $i++) {
        echo "<tr>";
        $prefixUrl="<a href='";
        $suffixUrl="'>";
        $prefixName="";
        $suffixName="</a>";
        $url="?page=advent&";
        $url .= $id_url . '=' . $result[$i][$id_url];
        foreach($result[$i] as $key => $value) {
            foreach($result[$i] as $keyUrl => $valueUrl) {
            }
            $url = trim($url, "&");

            if($fields==$key) {
            }
            echo "<td>";
            echo $prefixUrl.$url.$suffixUrl.$prefixName.$value.$suffixName;
            echo "</td>";
            }

        echo  "</tr>";
    }
    echo "</table>";
}
