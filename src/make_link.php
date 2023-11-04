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

// Merci Hughes Grant
function make_link(string $string, $options = array("patients"))
{
    if (array_search("patients", $options) >= 0) {
        if (isset($_GET["id_hospitalise"]) && is_array($_GET["id_hospitalise"])) {
            $string = $string . "?";
            $passed = false;
            foreach ($_GET["id_hospitalise"] as $id => $value) {
                if ($passed)
                    $string .= "&$id=$value";
                else {
                    $string .= "$id=$value";
                    $passed = true;
                }
            }
        }
    }
    return $string;
    /*
    global $action;
    $stringArr = explode("?", $string);
    if(isset($_GET["submit"])||isset($_POST["submit"]) || $action=="save" || $action=="saveNew" || $action=="delete") {
        return $string;
    }
    if (count($stringArr) == 0 || $stringArr[0] == "") {
        if (!isset($stringArr) || !isset($stringArr[0])) {
            return "?";
        }
        if (isset($stringArr[1]) && $stringArr[1] == "") {
            return add_assoc("?", $addGet ? $_GET : array());
        } else {
            if (str_contains($stringArr[1], "action=")) {
                $stringArr[1] = substr($stringArr[1], 0, stripos($stringArr[1], "action="));
            }
            return "?" . ($deleteUrlParams ? "" : $stringArr[1]) . add_assoc($string, $_GET);
        }
    }
    if (count($stringArr)>0 && str_contains($stringArr[0], "action=")) {
        $stringArr[0] = substr($stringArr[0], 0, stripos($stringArr[0], "action="));
    }
    if (count($stringArr)>1 && str_contains($stringArr[1], "action=")) {
        $stringArr[1] = substr($stringArr[1], 0, stripos($stringArr[1], "action="));
    }

    return $stringArr[0] . "?" . ($addGet ? add_assoc($string, $_GET) : "") . (count($stringArr) > 1 ? (($deleteUrlParams ? "" : $stringArr[1])) : "");

    */
}


function add_assoc($query, $add)
{
    if (is_array($query)) {
        $query = add_assoc("", $query);
    }
    if (!isset($add["action"])) {
        foreach ($add as $var => $item) {
            if (is_array($var) || is_array($item)) {

            } else {
                $query = $query . is_string($var) ? ("&" . htmlentities($var) . "=" . htmlentities($item)) : "";
            }
        }
    }
    return $query;
}
