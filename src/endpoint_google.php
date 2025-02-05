<?php
require_once '../vendor/autoload.php';

error_reporting(E_ALL);

// Get $id_token via HTTPS POST.
$token = $_POST["credential"];


//echo $token;
// Specify the CLIENT_ID of the app that accesses the backend
$client = new Google_Client(['client_id' => "1053386986412-q05vuknkmq57aid34r52fitjq5ku1nuk.apps.googleusercontent.com"]);
//echo "new client : ok";
$payload = $client->verifyIdToken($token);
//echo "verifyIdToken : ok";
//print_r($payload);
if ($payload) {
    $userid = $payload['email'];
// If the request specified a Google Workspace domain
    //echo $userid;
    if ($userid != NULL) {
        $username = $userid;

        $_SESSION['username'] = $userid;
        $_SESSION['password'] = $token;

        echo "Connect success " . $userid;

        global $password, $userData, $username;

        require_once "MyDB.php";
        require_once "AgendaUser.php";
        $user = new AgendaUser($username, $username);
        $userData = $user->getData();

        print_r($userData);


        $url = 'index.php?page=login';
        $data = ['page' => 'login', 'username' => $username, 'password' => $password];

// use key 'http' even if you send the request to https://...
        $options = [
            'http' => [
                'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                'method' => 'POST',
                'content' => http_build_query($data),
            ],
        ];

        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        if ($result === false) {
            /* Handle error */
        }
        header("location: index.php?page=login&username=" . $username . "&password=" . $password);
    } else {
        echo "Connect fail";
    }
} else {
    echo "Connect fail";
} ?><!--
<html>
<head></head>
<body>
<div id="g_id_onload"
     data-client_id="1053386986412-q05vuknkmq57aid34r52fitjq5ku1nuk.apps.googleusercontent.com"
     data-callback="handleCredentialResponse">
</div>
<script>
    function decodeJwtResponse(credential) {
        return credential;
    }
  function handleCredentialResponse(response) {
     // decodeJwtResponse() is a custom function defined by you

      // to decode the credential response.
     const responsePayload = decodeJwtResponse(response.credential);

     console.log("ID: " + responsePayload.sub);
     console.log('Full Name: ' + responsePayload.name);
     console.log('Given Name: ' + responsePayload.given_name);
     console.log('Family Name: ' + responsePayload.family_name);
     console.log("Image URL: " + responsePayload.picture);
     console.log("Email: " + responsePayload.email);
  }
</script>
</body></html>-->