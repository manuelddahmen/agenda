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
 * Create Event Example With Local Timezone
 *
 */

require_once("../zapcallib.php");

$title = "Event in New York timezone";
// date/time is in SQL datetime format
$event_start = "2020-01-01 12:00:00";
$event_end = "2020-01-01 13:00:00";
// timezone must be a supported PHP timezone
// (see http://php.net/manual/en/timezones.php )
// Note: multi-word timezones must use underscore "_" separator
$tzid = "America/New_York";

// create the ical object
$icalobj = new ZCiCal();

// Add timezone data
ZCTimeZoneHelper::getTZNode(substr($event_start,0,4),substr($event_end,0,4),$tzid, $icalobj->curnode);

// create the event within the ical object
$eventobj = new ZCiCalNode("VEVENT", $icalobj->curnode);

// add title
$eventobj->addNode(new ZCiCalDataNode("SUMMARY:" . $title));

// add start date
$eventobj->addNode(new ZCiCalDataNode("DTSTART:" . ZCiCal::fromSqlDateTime($event_start)));

// add end date
$eventobj->addNode(new ZCiCalDataNode("DTEND:" . ZCiCal::fromSqlDateTime($event_end)));

// UID is a required item in VEVENT, create unique string for this event
// Adding your domain to the end is a good way of creating uniqueness
$uid = date('Y-m-d-H-i-s') . "@demo.icalendar.org";
$eventobj->addNode(new ZCiCalDataNode("UID:" . $uid));

// DTSTAMP is a required item in VEVENT
$eventobj->addNode(new ZCiCalDataNode("DTSTAMP:" . ZCiCal::fromSqlDateTime()));

// write iCalendar feed to stdout
echo $icalobj->export();

