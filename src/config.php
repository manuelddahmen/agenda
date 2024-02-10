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
 * Build a configuration array to pass to `Hybridauth\Hybridauth`
 *
 * Set the Authorization callback URL to https://path/to/hybridauth/examples/example_07/callback.php
 * Understandably, you need to replace 'path/to/hybridauth' with the real path to this script.
 */
global $config;
$config = [
    'callback' => 'https://empty3.one/agenda/src/callback.php',
    'providers' => [

        'Google' => [
            'enabled' => true,
            'keys' => [
                'id' => '838858680256-lvnerq3ilnqnc1gd3sh6acubpia9vjr5.apps.googleusercontent.com',
                'secret' => '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',
            ],
            'scope' => 'email',
        ],

       // 'Yahoo' => ['enabled' => true, 'keys' => ['key' => '...', 'secret' => '...']],
       // 'Facebook' => ['enabled' => true, 'keys' => ['id' => '...', 'secret' => '...']],
       // 'Twitter' => ['enabled' => true, 'keys' => ['key' => '...', 'secret' => '...']],
       // 'Instagram' => ['enabled' => true, 'keys' => ['id' => '...', 'secret' => '...']],

    ],
];