<?php

function initIdHospitalise() {
    global $id_hospitalise;
    if(!isset($id_hospitalise)) {
        $i=0;

        $id_hospitalise = array();
        if(isset($_GET["id_hospitalise"]) && is_scalar($_GET["id_hospitalise"])) {
            foreach ($_GET as $key => $value) {
                $id_hospitalise[$i] = $_GET["id_hospitalise"];
                $i++;
            }
        } else if(isset($_GET["id_hospitalise"]) && is_array($_GET["id_hospitalise"])) {
            foreach ($_GET as $key => $value) {
                if (array_search($value, $id_hospitalise)===false) {
                    $id_hospitalise[$i] = $_GET["id_hospitalise"];
                    $i++;
                }
            }
        }
        foreach ($_GET as $key => $value) {
            if(str_starts_with("id_hospitalise_", $key)) {
                if (array_search($value, $id_hospitalise)===false) {
                    $id_hospitalise[$i] = $value;
                    $i++;
                }
            }
        }
        foreach ($_GET as $key => $value) {
            if (str_starts_with("id_hospitalises_", $key)) {
                if (array_search($value, $id_hospitalise) === false) {
                    $id_hospitalise[$i] = $value;
                    $i++;
                }
            }
        }
    }

}

function implodeIdsInUrl($varName, $ids) : string{
    $ret = "";
    foreach ($ids as $value) {
        $ret.= "&".$varName."=".$value;
    }
    return $ret;
}

?>