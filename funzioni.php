<?php
//Funzione per salvare i dati nel db
function salvaAccountDB(){
    //Necessari per costringere SQL a fornire i messaggi di errore
    ini_set('display_errors', 1); ini_set('log_errors',1); error_reporting(E_ALL); 
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    
    include "connessione.php";
    
    $sql = "SELECT email FROM utenti WHERE email = ?";
    $statement = $connessione -> prepare($sql);
    $statement -> bind_param("s",$_SESSION['user_email_address']);
    $statement -> execute();
    $ris = $statement -> get_result();
    $n_righe = $ris -> num_rows;
    
    if($n_righe == 0){
        $queryAcc = "INSERT INTO utenti(username,email,link_img) VALUES(?,?,?)";

        $stmt = $connessione -> prepare($queryAcc);
        $str = "";
        //Concatenare nome cognome nel caso si tratti dell'API di Google (fa distinzione fra nome e cognome)
        if(isset($_SESSION['google'])){
            $str = $_SESSION['user_first_name'].$_SESSION['user_last_name'];
        }
        if(isset($_SESSION['fb'])){
            //Significa che si tratta dell'API di Facebook
            $str = $_SESSION['user_name'];
        }
        $stmt -> bind_param("sss", $str, $_SESSION['user_email_address'], $_SESSION['user_image']);
        $stmt -> execute();
        $stmt -> close();
        
        //Creo la chat che l'utente potrà usare per inviarsi messaggi e promemoria vari
        creaChat();

        return true;
    }else {
      return false;
    }
    
}

//Funzione per creare i cookie
function creaCookie(){
    include "connessione.php";
    //Non controllo se esistono ancora vecchi valori per l'attributo token poichè lo sto per sovrascrivere
    $tokenSicurezza = bin2hex(random_bytes(32));
    //Invio al db il token di sicurezza
    $queryToken = "UPDATE utenti SET token = ? WHERE email = '$_SESSION[user_email_address]'";
    $stmt = $connessione -> prepare($queryToken);
    $stmt -> bind_param("s",$tokenSicurezza);
    $stmt -> execute();
    //Scade dopo 30 giorni
    $scadenza = time() + 86400 * 30;
    setcookie("cookieRicordami",$tokenSicurezza,$scadenza);
    /*Questo secondo cookie indicherà il momento in cui il cookie scadrà.
    Meglio farlo coi cookie che usando i database per evitare un inutile operazione che graverebbe inutilmente
    sul server, tutto ciò può essere fatto dal client*/
    setcookie('scadenzaCookieRicordami',$scadenza,$scadenza);
}

function controllaSessione(){
    session_start();
    if(isset($_SESSION['user_email_address'])){
        return true;
    }else{
        false;
    }
}

//Per controllare se esistono cookie e se la sessione non è settata, in quel caso interviene
function controllaCookie(){
    //session_start();
    include "connessione.php";
    /*Se non esiste la sessione e i cookie sono settati recupero il valore della sessione altrimenti niente*/
    if (isset($_COOKIE['cookieRicordami']) && isset($_COOKIE['scadenzaCookieRicordami']) && $_COOKIE['scadenzaCookieRicordami'] > time()
    && !isset($_SESSION['user_email_address'])) {
        $query = "SELECT email FROM utenti WHERE token = '$_COOKIE[cookieRicordami]'";
        $ris = $connessione -> query($query);
        if ($ris -> num_rows == 1) {
          //Verificato il token di accesso del cookie setto la sessione
          $sessione = $ris -> fetch_assoc();
          $_SESSION['user_email_address'] = $sessione;
          //Chiudo connessione al database
          $connessione -> close();
        }
      }
  }


  //Funzione per cancellare i cookie
  function cancellaCookie(){
    if (isset($_COOKIE['scadenzaCookieRicordami']) && isset($_COOKIE['cookieRicordami'])) {
        require "connessione.php";
        $query = "UPDATE utenti SET token = '' WHERE token '$_COOKIE[cookieRicordami]'";
        $ris = $connessione -> query($query);
        setcookie('cookieRicordami',time() - 86400);
        setcookie('scadenzaCookieRicordami',time() - 86400);
        $connessione -> close();
      }
  }

  //Aggiorna ultimo accesso ogni volta che l'utente si disconnette NB: L'orario di riferimento saranno quelli italiani
  function aggiornaAccesso($email){
    include "connessione.php";
    //Ottengo l'orario
    $ora = new DateTime();
    $ora = $ora->format('Y-m-d H:i:s');
    //Aggiorno la data dell'ultimo accesso
    $sql = "UPDATE utenti SET last_online = '$ora' WHERE email = ?";
    $statement = $connessione -> prepare($sql);
    $statement -> bind_param("s",$_SESSION['user_email_address']);
    $statement -> execute();
  }

  //Recupera dati utente dal DB
  //La variabile ID serve perchè ad una seconda interrogazione al DB devo sapere dove sono arrivato
  function recuperaDatiUtente(){
    include "connessione.php";
    //LIMIT -> per mostrare tutti i possibili utenti nella schermata e mantenere la query più leggera
    $sql = "SELECT ID,username,email,last_online,link_img FROM utenti WHERE email = ?";
    $statement = $connessione -> prepare($sql);
    $statement -> bind_param("s",$_SESSION['user_email_address']);
    $statement -> execute();
    $ris = $statement -> get_result();
    $n_righe = $ris -> num_rows;
    if($n_righe > 0){
      $riga = $ris -> fetch_assoc();
      //Nel caso in cui la funzione abbia successo restituisco l'array associativo contenente i valori recuperati dalla query
      return $riga;
    }else{
      return false;
    }
  }

  
  /*Creo le tabelle per chattare con il sottoscritto e una chat
    per inviare messaggi a se stessi ispirata a servizi come telegram e whatsapp*/
  function creaChat(){
    include "connessione.php";
    do {
      $nomeTabellaChatPersonale = mt_rand(10000,10000000);
      //Controllo che non esista già nel db e che non esistano già 2 chat per quell'utente
      $q = "SELECT ID_chat FROM elenco_chat WHERE ID_chat = '$nomeTabellaChatPersonale'";
      $ris = $connessione -> query($q);
    } while (($ris -> num_rows) != 0);
    

        //Salvo nella tabella elenco e poi creo la chat vera e propria
        $ora = new DateTime();
        $ora = $ora->format('Y-m-d H:i:s');

        //Estrai l'ID usando la mail per il controllo
        $x = "SELECT ID from utenti WHERE email = ?";
        $statement = $connessione -> prepare($x);
        $statement -> bind_param("s",$_SESSION['user_email_address']);
        $statement -> execute();
        $ris = $statement -> get_result();
        $n_righe = $ris -> num_rows;
        $id = 0;
        if($n_righe > 0){
          $riga = $ris -> fetch_assoc();
          $id = $riga['ID'];  
        }
        //Query per la chat personale
        $query = "INSERT INTO elenco_chat(ID_chat,data_creazione,ID_utente)
                  VALUES('$nomeTabellaChatPersonale','$ora','$id')";
        $connessione -> query($query);


        $chatPersonale = "CREATE TABLE `{$nomeTabellaChatPersonale}`(
          ID INT (6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
          messaggio TEXT (500),
          orario_messaggio TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
          FOREIGN KEY(ID) REFERENCES elenco_chat(ID_chat) ON UPDATE CASCADE ON DELETE NO ACTION
          )";
        $connessione -> query($chatPersonale);  

        $_SESSION['id_chat'] = $nomeTabellaChatPersonale;
    }

?>
