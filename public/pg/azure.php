<?php

$appid = "f4229199-4972-48c3-b6a0-bd7255a6410e";
$tennantid = "f8cdef31-a31e-4b4a-93e4-5f571e91255a";
$secret = "60853e79-4152-4010-9c9e-1bd8e8a66cf5";
$login_url = "https://login.microsoftonline.com/" . $tennantid . "/oauth2/v2.0/authorize";

session_start();

$_SESSION['state'] = session_id();

echo '<h2><p>You can <a href="?action=login">Log In</a> with Microsoft</p></h2>';

if ($_GET['action'] == 'login') {
    $params = array(
        'client_id' => $appid,
        'redirect_uri' => 'https://example/',
        'response_type' => 'token',
        'response_mode' => 'form_post',
        'scope' => 'https://graph.microsoft.com/User.Read',
        'state' => $_SESSION['state']
    );

    header('Location: ' . $login_url . '?' . http_build_query($params));
}

if (array_key_exists('access_token', $_POST)) {
    $_SESSION['t'] = $_POST['access_token'];
    $t = $_SESSION['t'];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $t, 'Conent-type: application/json'));

    curl_setopt($ch, CURLOPT_URL, "https://graph.microsoft.com/v1.0/me/");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    $rez = json_decode(curl_exec($ch), 1);

    if (array_key_exists('error', $rez)) {
        var_dump($rez['error']);
        die();
    }
}