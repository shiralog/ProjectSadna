<?php
session_start();

require_once 'CalendarAPISecrets.php';

if (isset($_GET['code'])) {
    $authorization_code = $_GET['code'];

    $tenant_id = "common"; 
    $client_id = client_id;
    $client_secret = client_secret;
    $redirect_uri = 'http://localhost:3000/pages/callback.php';
    $token_endpoint = "https://login.microsoftonline.com/common/oauth2/v2.0/token";

    // Request body for token endpoint
    $post_data = array(
        'client_id' => $client_id,
        'client_secret' => $client_secret,
        'grant_type' => 'authorization_code',
        'code' => $authorization_code,
        'redirect_uri' => $redirect_uri,
        'scope' => 'Calendars.ReadWrite'
    );

    // Initialize cURL session
    $ch = curl_init($token_endpoint);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));

    // Execute cURL session
    $response = curl_exec($ch);
    curl_close($ch);

    // Decode JSON response
    $response_data = json_decode($response, true);

    if (isset($response_data['access_token'])) {
        $access_token = $response_data['access_token'];

        // Store the access token in session or database
        $_SESSION['access_token'] = $access_token;

        // Redirect to the main page
        header('Location: http://localhost:3000/pages/classrooms.php');
        exit();
    } else {
        // Handle error
        echo "Error getting access token";
        var_dump($response_data);
    }
} else {
    // Handle error
    echo "Authorization code not received";
}
?>