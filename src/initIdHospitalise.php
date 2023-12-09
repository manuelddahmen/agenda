<?php

function initIdHospitalise(): void
{
    global $id_hospitalise;
    $i = 0;

    $id_hospitalise = array();
    if (isset($_GET["id_hospitalise"]) && is_scalar($_GET["id_hospitalise"])) {
        foreach ($_GET as $key => $value) {
            if ($key == "id_hospitalise") {
                $id_hospitalise[$i] = $value;
                $i++;
            }
        }
    }
    if (isset($_GET["id_hospitalise"]) && is_array($_GET["id_hospitalise"])) {
        foreach ($_GET as $key => $value) {
            if (!in_array($value, $id_hospitalise)) {
                $id_hospitalise[$i] = $value;
                $i++;
            }
        }
    }
    foreach ($_GET as $key => $value) {
        if (str_starts_with($key, "id_hospitalise_")) {
            if (!in_array($value, $id_hospitalise)) {
                $id_hospitalise[$i] = $value;
                $i++;
            }
        }
    }
    foreach ($_GET as $key => $value) {
        if (str_starts_with($key, "id_hospitalises_")) {
            if (!in_array($value, $id_hospitalise)) {
                $id_hospitalise[$i] = $value;
                $i++;
            }
        }
    }
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

?>