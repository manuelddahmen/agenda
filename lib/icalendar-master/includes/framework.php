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

// No direct access
defined('_ZAPCAL') or die( 'Restricted access' );

/**
 * set MAXYEAR to 2036 for 32 bit systems, can be higher for 64 bit systems
 *
 * @var integer
 */
define('_ZAPCAL_MAXYEAR', 2036);

/**
 * set MAXREVENTS to maximum # of repeating events 
 *
 * @var integer
 */
define('_ZAPCAL_MAXREVENTS', 5000);

require_once(_ZAPCAL_BASE . '/includes/date.php');
require_once(_ZAPCAL_BASE . '/includes/recurringdate.php');
require_once(_ZAPCAL_BASE . '/includes/ical.php');
require_once(_ZAPCAL_BASE . '/includes/timezone.php');
