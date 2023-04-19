<?php
//Controllo se l'utente è connesso e ha una sessione attiva
    include "connessione.php";
    include "funzioni.php";
    //Controlla se esiste una sessione
    if(!controllaSessione()){
        header("location: home.html");
    }
    aggiornaAccesso($_SESSION['user_email_address']);
?>
<html>
<link href="stile.css" rel="stylesheet" />
<div class="container">
<div class="row clearfix">
    <div class="col-lg-12">
        <div class="card chat-app">
            <div id="plist" class="people-list">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fa fa-search"></i></span>
                    </div>
                </div>
                <ul class="list-unstyled chat-list mt-2 mb-0">
                    <li class="clearfix">
                        <?php
                            if(($riga = recuperaDatiUtente()) != false){
                                //sistema tutti i messaggi sulla destra
                                //div che fa l'append e mette i messaggi a schermo chat_history e' quello che stai cercando
                                ?>
                                    <li class="clearfix" id="chat_personale">
                                        <div class="about">
                                            <div class="name"><?php echo $riga['username'];?><img src="<?php echo $riga['link_img'];?>" alt="avatar"></div>
                                                <div class="status"> <i class="fa fa-circle offline"></i> Ultimo accesso: <?php echo $riga['last_online'];?> </div>               
                                            </div>
                                    </li>

                                    <li class="clearfix" id="chat_personale">
                                        <div class="about">
                                                <a href="logout.php" class="btnClicca">Logout</a>               
                                        </div>
                                    </li>
                                <?php
                            }
                        ?>
                    </li>                    
                </ul>
            </div>




            <div class="chat">
                <div class="chat-header clearfix">
                    <div class="row">
                        <div class="col-lg-6">
                            <a href="javascript:void(0);" data-toggle="modal" data-target="#view_info">
                                <img src="<?php echo $riga['link_img'];?>" alt="avatar">
                            </a>
                            <div class="chat-about">
                                <h4 class="m-b-0"><?php echo $riga['username'];?></h4>
                            </div>
                        </div>
                        <div class="col-lg-6 hidden-sm text-right">
                            <a href="javascript:void(0);" class="btn btn-outline-secondary"><i class="fa fa-camera"></i></a>
                            <a href="javascript:void(0);" class="btn btn-outline-primary"><i class="fa fa-image"></i></a>
                            <a href="javascript:void(0);" class="btn btn-outline-info"><i class="fa fa-cogs"></i></a>
                            <a href="javascript:void(0);" class="btn btn-outline-warning"><i class="fa fa-question"></i></a>
                        </div>
                    </div>
                </div>
                <div class="chat-history" id="chatMessaggi">
                    <ul class="m-b-0">                      
                </div>

                <script src="scriptUsati/loadMessaggi.js"></script>            

                <div class="chat-message clearfix">
                    <div class="input-group mb-0">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fa fa-send"></i></span>
                        </div>
                        <input id="mexDaInviare" type="text" class="form-control" placeholder="Invia messaggio" required>
                        <button id="invioMex" class="btnClicca">Invia</button>                                    
                    </div>
                </div>

                <script src="scriptUsati/salvaMessaggi.js"></script>
            </div>
        </div>
    </div>
</div>
</div>
</html>