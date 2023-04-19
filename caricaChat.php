<?php
require "connessione.php";
session_start();

//Query per ottenere la cronologia della chat
$email = $_SESSION['user_email_address'];
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


$query = "SELECT messaggio, orario_messaggio FROM `$nomeChat` ORDER BY ID ASC LIMIT 20";
$ris = $connessione -> query($query);
if(($ris -> num_rows) > 0){
    $array = array();
    while ($r = $ris -> fetch_assoc()) {
        $array[] = $r;
    }
    print(json_encode($array));
}
?>