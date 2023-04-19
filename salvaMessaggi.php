<?php

ini_set('display_errors', 1); ini_set('log_errors',1); error_reporting(E_ALL); 
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

require "connessione.php";
session_start();

$email = $_SESSION['user_email_address'];

if(isset($_POST['messaggio'])){
    //Prendo l'id della chat
    $sql = "SELECT ID_chat
    FROM elenco_chat 
    JOIN utenti ON ID_utente = ID 
    WHERE email = ?";
    $stmt = $connessione -> prepare($sql);
    $stmt -> bind_param("s", $email);
    $stmt -> execute();
    $ris = $stmt -> get_result();
    if (($ris -> num_rows) == 1) {
        $x = $ris -> fetch_assoc();
        $nomeChat = $x['ID_chat'];
    }else {
        print("Errore sconosciuto");
        die();
    }

    $query = "INSERT INTO `$nomeChat`(messaggio) VALUES(?)";
    $stmt = $connessione -> prepare($query);
    $stmt -> bind_param("s", $_POST['messaggio']);
    $stmt -> execute();
    $stmt -> close();
    
    $_SESSION['nome_chat'] = $nomeChat;

    print("OK");
}

?>