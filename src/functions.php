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

ini_set('display_errors', true);
ini_set('error_log', 'app.log');


function escapeSqlChars($chars): string
{
return SQLite3::escapeString(htmlspecialchars(urldecode($chars)));
}

function unescapeSqlChars($chars): string
{
return urldecode($chars);
}

global $days;
$days = array(0=>"Lundi", 1=>"Mardi", 2=>"Mercredi", 3=>"Jeudi", 4=>"Vendredi",
    5=>"Samedi", 6=>"Dimanche", 7=>"Tous les jours");
$day = 0;

$joursSemaine = array('Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi');

global $days, $day, $joursSemaine;



$halfHour = array(
    "08.00", "08.30", "09.00", "09.30", "10.00",
    "10.30", "11.00", "11.30", "12.00", "12.30",
    "13.00", "13.30", "14.00", "14.30", "15.00",
    "15.30", "16.00",
    "16.30", "17.00", "17.30", "18.00", "18.30",
    "19.00", "19.30", "20.00", "20.30", "21.00"
    /*    "21.30", "22.00", "22.30", "23.00", "23.30","00.00"*/
);
