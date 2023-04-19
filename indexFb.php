<?php
include('funzioni.php');
//Controlla se esiste una sessione (session_start() si trova giò qui)
if(controllaSessione()){
    header("location: home.html");
}

//Parametro che utilizzo per distinguere fra login Facebook e Google
$_SESSION['fb'] = true;

include('configFB.php');

$facebook_output = '';

$facebook_helper = $facebook->getRedirectLoginHelper();

if(isset($_GET['code'])){

 if(isset($_SESSION['access_token'])){
  $access_token = $_SESSION['access_token'];
 }
 else{
  $access_token = $facebook_helper->getAccessToken();

  $_SESSION['access_token'] = $access_token;

  $facebook->setDefaultAccessToken($_SESSION['access_token']);
 }

 $_SESSION['user_id'] = '';
 $_SESSION['user_name'] = '';
 $_SESSION['user_email_address'] = '';
 $_SESSION['user_image'] = '';

 $graph_response = $facebook->get("/me?fields=name,email", $access_token);

 $facebook_user_info = $graph_response->getGraphUser();

 if(!empty($facebook_user_info['id'])){
  $_SESSION['user_image'] = 'http://graph.facebook.com/'.$facebook_user_info['id'].'/picture';
 }

 if(!empty($facebook_user_info['name'])){
  $_SESSION['user_name'] = $facebook_user_info['name'];
 }

 if(!empty($facebook_user_info['email'])){
  $_SESSION['user_email_address'] = $facebook_user_info['email'];
 }
 
}else{
 //Ottieni URL login
    $facebook_permissions = ['email']; 

    $facebook_login_url = $facebook_helper->getLoginUrl('http://localhost/Coding/Chat-App/indexFb.php', $facebook_permissions);
    
    //Bottoni login
    $facebook_login_url = '<div align="center"><a href="'.$facebook_login_url.'"><img src="php-login-with-facebook.gif" /></a></div>';
}



?>
<html>
 <head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title>PHP Login using Google Account</title>
  <meta content='width=device-width, initial-scale=1, maximum-scale=1' name='viewport'/>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
  <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet" />
  
 </head>
 <body>
  <div class="container">
   <br />
   <h2 align="center">PHP Login con Facebook Account</h2>
   <br />
   <div class="panel panel-default">
    <?php 
    if(isset($facebook_login_url)){
     echo $facebook_login_url;
    }
    else{
      //NB: Su facebook ci si può registrare anche senza email ma per poter ricevere notifiche dal sito sarà necessario una mail
      //lo stesso facebook consiglio di associare un indirizzo al proprio account
      if(isset($_SESSION['user_email_address'])){
         echo "L'account non ha una mail ad esso associata";
         die();
         //Timer e poi ritorna alla home per registrarti
      }
    /*Ora vado a mostrare un campo che mi fa inserire un nuovo username, se valido mostro i dati del profilo e metto un bottone per 
     iniziare a chattare con altri utenti*/
     ?>   <script src="user.html"></script>
     <?php echo '<div class="panel-heading">Benvenuto Utente</div><div class="panel-body">';
     echo '<img src="'.$_SESSION["user_image"].'" class="img-responsive img-circle img-thumbnail" />';
     echo '<h3><b>Name :</b> '.$_SESSION['user_name'].'</h3>';
     echo '<h3><b>Email :</b> '.$_SESSION['user_email_address'].'</h3>';
     echo '<h3><a href="logout.php">Logout</h3></div>';

     if(salvaAccountDB()){
        //Creo il cookie che ricorderà l'utente la prossima volta
        creaCookie();
        header("Refresh: 0; url=chat.php");
     }else{
        header("Refresh: 0; url=chat.php");
     }  

    }
    ?>
   </div>
  </div>
 </body>
</html>