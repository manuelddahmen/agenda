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

$curl = curl_init();

curl_setopt_array($curl, [
    CURLOPT_URL => "https://d7sms.p.rapidapi.com/secure/sendbatch",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "POST",
    CURLOPT_POSTFIELDS => "{\r\n    \"messages\": [\r\n        {\r\n            \"content\": \"Bulk SMS Content\",\r\n            \"from\": \"D7-Rapid\",\r\n            \"to\": [\r\n                \"Destination1\",\r\n                \"Destination2\"\r\n            ]\r\n        }\r\n    ]\r\n}",
    CURLOPT_HTTPHEADER => [
        "Authorization: undefined",
        "X-RapidAPI-Host: d7sms.p.rapidapi.com",
        "X-RapidAPI-Key: a42e28c396msha2c80fc22f2ecc7p1b5798jsn98b82656c768",
        "content-type: application/json"
    ],
]);

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
    echo "cURL Error #:" . $err;
} else {
    echo $response;
}