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

function initIdHospitalise(): void
{
    global $id_hospitalise;
    $i = 0;

    $id_hospitalise2 = array();
    if (isset($_GET["id_hospitalise"]) && is_scalar($_GET["id_hospitalise"])) {
        foreach ($_GET as $key => $value) {
            if ($key == "id_hospitalise" && !in_array($value, $id_hospitalise2)) {
                $id_hospitalise2[$i] = $value;
                $i++;
            }
        }
    }
    if (isset($_GET["id_hospitalise"]) && is_array($_GET["id_hospitalise"])) {
        foreach ($_GET as $key => $value) {
            if (!in_array($value, $id_hospitalise2)) {
                $id_hospitalise2[$i] = $value;
                $i++;
            }
        }
    }
    foreach ($_GET as $key => $value) {
        if (str_starts_with($key, "id_hospitalise_")) {
            if (!in_array($value, $id_hospitalise2)) {
                $id_hospitalise2[$i] = $value;
                $i++;
            }
        }
    }
    foreach ($_GET as $key => $value) {
        if (str_starts_with($key, "id_hospitalises_")) {
            if (!in_array($value, $id_hospitalise2)) {
                $id_hospitalise2[$i] = $value;
                $i++;
            }
        }
    }
    $id_hospitalise = $id_hospitalise2;
}

function implodeIdsInUrl(string $varName, array $ids): string
{
    $i = 0;
    $ret = "";
    foreach ($ids as $value) {
        $ret .= "&" . $varName . "_$i=" . $value;
        $i++;
    }
    return $ret;
}

function implodeIdsInUrl2(string $varName, array $ids): string
{
    $i = 0;
    $ret = "";
    foreach ($ids as $value) {
        $ret .= "&" . $varName . "=" . $value;
        $i++;
    }
    return $ret;
}
?>