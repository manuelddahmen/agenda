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

/**
 * Recurring Date Example
 *
 * Recurring date examples with RRULE property
 *
 */

require_once("../lib/icalendar-master/zapcallib.php");

$id_activite = $_GET["id_activite"];
$id_hospitalise = $_GET["id_hospotalise"];



$examples =
    array(
        array(
            "name" => "Abraham Lincon's birthday",
            "date" => "2015-02-12",
            "rule" => "FREQ=YEARLY;INTERVAL=1;BYMONTH=2;BYMONTHDAY=12"
        ),

        array(
            "name" => "Start of U.S. Supreme Court Session (1st Monday in October)",
            "date" => "2015-10-01",
            "rule" => "FREQ=YEARLY;INTERVAL=1;BYMONTH=10;BYDAY=1MO"
        )
    );

// Use maxdate to limit # of infinitely repeating events
$maxdate = strtotime("2021-01-01");

foreach($examples as $example)
{
    echo $example["name"] . ":\n";
    $rd = new ZCRecurringDate($example["rule"],strtotime($example["date"]));
    $dates = $rd->getDates($maxdate);
    foreach($dates as $d)
    {
        echo "  " . date('l, F j, Y ',$d) . "\n";
    }
    echo "\n";
}
