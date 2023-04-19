<?php
require_once 'facebookVendor/vendor/autoload.php';

if (!session_id()){
    session_start();
}

//Chiamata all'APi di Facebook 
$facebook = new \Facebook\Facebook([
  'app_id'      => '5503514119777231',
  'app_secret'     => '2fdc68d1867634c6d11af2efe8ae6879',
  'default_graph_version'  => 'v2.10'
]);

?>