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

/**
 * Parse iCalendar Example
 *
 * Enter an ics filename or URL on the command line, 
 * or leave blank to parse the default file.
 *
 */

require_once("../zapcallib.php");

$icalfile = count($argv) > 1 ? $argv[1] : "abrahamlincoln.ics";
$icalfeed = file_get_contents($icalfile);

// create the ical object
$icalobj = new ZCiCal($icalfeed);

echo "Number of events found: " . $icalobj->countEvents() . "\n";

$ecount = 0;

// read back icalendar data that was just parsed
if(isset($icalobj->tree->child))
{
	foreach($icalobj->tree->child as $node)
	{
		if($node->getName() == "VEVENT")
		{
			$ecount++;
			echo "Event $ecount:\n";
			foreach($node->data as $key => $value)
			{
				if(is_array($value))
				{
					for($i = 0; $i < count($value); $i++)
					{
						$p = $value[$i]->getParameters();
						echo "  $key: " . $value[$i]->getValues() . "\n";
					}
				}
				else
				{
					echo "  $key: " . $value->getValues() . "\n";
				}
			}
		}
	}
}
