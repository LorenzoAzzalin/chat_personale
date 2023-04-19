<?php
include('funzioni.php');
//Controlla se esiste una sessione (session_start() si trova giò qui)
if(controllaSessione()){
    header("location: home.html");
}

//Parametro che utilizzo per distinguere fra login Facebook e Google
$_SESSION['google'] = true;

include('config.php');

$login_button = '';

if(isset($_GET["code"])){
 //Otterò un token di accesso tramite il codice di autenticazione
 $token = $google_client->fetchAccessTokenWithAuthCode($_GET["code"]);

 //Controlla se ci sono errori nel token
 if(!isset($token['error'])){
  //Setto il token da usare per le richieste
  $google_client->setAccessToken($token['access_token']);

  $_SESSION['access_token'] = $token['access_token'];

  $google_service = new Google_Service_Oauth2($google_client);

  //Recupero i dati dell'utente da Google
  $data = $google_service->userinfo->get();

  //Salvo i dati appena ottenuti nelle sessioni
  if(!empty($data['given_name'])){
   $_SESSION['user_first_name'] = $data['given_name'];
  }

  if(!empty($data['family_name'])){
   $_SESSION['user_last_name'] = $data['family_name'];
  }

  if(!empty($data['email'])){
   $_SESSION['user_email_address'] = $data['email'];
  }

  if(!empty($data['gender'])){
   $_SESSION['user_gender'] = $data['gender'];
  }

  if(!empty($data['picture'])){
   $_SESSION['user_image'] = $data['picture'];
  }
 }
}

if(!isset($_SESSION['access_token'])){
 //Url per ottene l'autorizzazione utente
 $login_button = '<a href="'.$google_client->createAuthUrl().'"><img src="sign-in-with-google.png" /></a>';
}
?>
<html>
 <head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title>PHP Login con Account Google</title>
  <meta content='width=device-width, initial-scale=1, maximum-scale=1' name='viewport'/>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
  <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet" />
  <link   rel="stylesheet" 
        href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" 
        integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" 
        crossorigin="anonymous">
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" 
        integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" 
        crossorigin="anonymous">
</script>
 </head>

 <body>
  <div class="container">
   <br />
   <h2 align="center">PHP Login con Google Account</h2>
   <br />
   <div class="panel panel-default">
   <?php
   if($login_button == ''){
    echo '<div class="panel-heading">Benvenuto Utente</div><div class="panel-body">';
    echo '<img src="'.$_SESSION["user_image"].'" class="img-responsive img-circle img-thumbnail" />';
    echo '<h3><b>Nome :</b> '.$_SESSION['user_first_name'].' '.$_SESSION['user_last_name'].'</h3>';
    echo '<h3><b>Email :</b> '.$_SESSION['user_email_address'].'</h3>';
    echo '<h3><a href="logout.php">Logout</h3></div>';
        //Salvo i dati nel DB
        if(salvaAccountDB()){
            //Creo il cookie che ricorderà l'utente la prossima volta
            creaCookie();
            header("Refresh: 0; url=chat.php");
        }else{
            //echo "Invio dei dati fallito. Si prega di riprovare più tardi";
            header("Refresh: 0; url=chat.php");
        }
        
   }else{
        echo '<div align="center" width="80px" class="btnClicca">'.$login_button . '</div>';
   }
   ?>
   </div>
  </div>
 </body>
</html>
