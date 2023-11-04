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
 * A simple example that shows how to use multiple providers, opening provider authentication in a pop-up.
 */

require_once('../vendor/autoload.php');
require_once('config.php');

use Hybridauth\Exception\Exception;
use Hybridauth\Hybridauth;
use Hybridauth\HttpClient;
use Hybridauth\Storage\Session;

try {
    global $config;
    $hybridauth = new Hybridauth($config);
    $storage = new Session();
    $error = false;

    //
    // Event 1: User clicked SIGN-IN link
    //
    if (isset($_GET['provider'])) {
        // Validate provider exists in the $config
        if (in_array($_GET['provider'], $hybridauth->getProviders())) {
            // Store the provider for the callback event
            $storage->set('provider', $_GET['provider']);
        } else {
            $error = $_GET['provider'];
        }
    }

    //
    // Event 2: User clicked LOGOUT link
    //
    if (isset($_GET['logout'])) {
        if (in_array($_GET['logout'], $hybridauth->getProviders())) {
            // Disconnect the adapter
            $adapter = $hybridauth->getAdapter($_GET['logout']);
            $adapter->disconnect();
        } else {
            $error = $_GET['logout'];
        }
    }

    //
    // Handle invalid provider errors
    //
    if ($error) {
        error_log('Hybridauth Error: Provider ' . json_encode($error) . ' not found or not enabled in $config');
        // Close the pop-up window
        echo "
            <script>
                if (window.opener.closeAuthWindow) {
                    window.opener.closeAuthWindow();
                }
            </script>";
        exit;
    }

    //
    // Event 3: Provider returns via CALLBACK
    //
    if ($provider = $storage->get('provider')) {

        $hybridauth->authenticate($provider);
        $storage->set('provider', null);

        // Retrieve the provider record
        $adapter = $hybridauth->getAdapter($provider);
        $userProfile = $adapter->getUserProfile();
        $accessToken = $adapter->getAccessToken();

        // add your custom AUTH functions (if any) here
        // ...
        $data = [
            'token' => $accessToken,
            'identifier' => $userProfile->identifier,
            'email' => $userProfile->email,
            'first_name' => $userProfile->firstName,
            'last_name' => $userProfile->lastName,
            'photoURL' => strtok($userProfile->photoURL, '?'),
        ];
        // Close pop-up window
        echo "
            <script>
                if (window.opener.closeAuthWindow) {
                    window.opener.closeAuthWindow();
                }
            </script>";

    }

} catch (Exception $e) {
    error_log($e->getMessage());
    echo $e->getMessage();
}

if(isset($_GET["page"]) && basename ($_SERVER['PHP_SELF'])=="callback.php") {
    header("Location: index.php?".$_GET["page"]);
}