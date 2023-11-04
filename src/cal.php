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

require_once("../lib/icalendar-master/zapcallib.php");
require_once ("framework.php");
$icalobj = new ZCiCal();

$eventobj = new ZCiCalNode("VEVENT", $icalobj->curnode);
global $datetime;
global $jour__demie_heure_semaine_duree_2, $jour__demie_heure_semaine_duree_1;
// add start date
$eventobj->addNode(new ZCiCalDataNode("DTSTART:" . ZCiCal::fromSqlDateTime($datetime." ".str_replace(".", ":", $jour__demie_heure_semaine_duree_1))));

// add end date
$eventobj->addNode(new ZCiCalDataNode("DTEND:" . ZCiCal::fromSqlDateTime($datetime." ".str_replace(".", ":", $jour__demie_heure_semaine_duree_1))));

echo $icalobj->export();

