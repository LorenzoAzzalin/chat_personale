<?php
include ('connessione.php');
include('config.php');
include('configFB.php');
include('funzioni.php');

session_unset();
cancellaCookie();
if(isset($_SESSION['google'])){
    //Reset token di accesso OAuth 
    $google_client->revokeToken($_SESSION['access_token']);
}
header('location:home.html');
?>
