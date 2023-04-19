<?php
require_once 'googleVendor/vendor/autoload.php';

$google_client = new Google_Client();

$google_client->setClientId('982562442947-l96epc41rnpa4olp745lkltlpqa3l67i.apps.googleusercontent.com');

$google_client->setClientSecret('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');

$google_client->setRedirectUri('http://localhost/Coding/Chat-App/index.php');


$google_client->addScope('email');

$google_client->addScope('profile');

$guzzleClient = new \GuzzleHttp\Client(array( 'curl' => array( CURLOPT_SSL_VERIFYPEER => false, ), ));
$google_client->setHttpClient($guzzleClient);

//session_start();
?>