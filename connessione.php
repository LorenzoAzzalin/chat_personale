<?php
$hostname = "127.0.0.1";
$user = "root";
$pwd = "";
$dbname = "chat_app";

$connessione = new mysqli($hostname,$user,$pwd,$dbname);

if ($connessione -> connect_error) {
  echo "<p>Errore di connessione al database</p>";
}

?>
