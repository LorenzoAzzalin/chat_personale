DROP DATABASE chat_app IF EXISTS;
CREATE DATABASE chat_app IF NOT EXISTS;
USE chat_app IF EXISTS;

DROP TABLE utenti IF EXISTS;
DROP TABLE elenco_chat IF EXISTS;

CREATE TABLE utenti(
    ID BIGINT AUTO_INCREMENT,
    username VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    link_img VARCHAR(255),
    token VARCHAR(255),
    last_online DATETIME,
    google INT,
    fb INT,
    amministratore INT,
    PRIMARY KEY(ID)
)engine=InnoDB;


CREATE TRIGGER checkReg
BEFORE INSERT ON utenti
IF NEW.google = 1 AND NEW.fb = 1
    THEN SIGNAL SQLSTATE '45000';
END IF;
IF NEW.google = 0 AND NEW.fb = 0
    THEN SIGNAL SQLSTATE '45000';
END IF;
IF NEW.google != 0 OR NEW.google != 1 OR NEW.fb != 0 OR NEW.fb != 1
    THEN SIGNAL SQLSTATE '45000';
END IF;

CREATE TABLE chat(
    cod INT AUTO_INCREMENT,
    data_creazione DATETIME NOT NULL,
    creatore VARCHAR(255) NOT NULL,
    PRIMARY KEY(cod)
)engine=InnoDB;

CREATE TABLE elenco_chat(
   ID_chat INT,
   data_creazione DATETIME NOT NULL,
   ID_utente INT,   
   PRIMARY KEY(ID_chat),
   FOREIGN KEY(ID_utente) REFERENCES utenti(ID) ON UPDATE CASCADE ON DELETE NO ACTION       
)engine=InnoDB;

CREATE TABLE messaggi(
    ID_messaggio INT AUTO_INCREMENT,
    autore INT,
    destinatario INT,
    data_messaggio DATETIME NOT NULL,
    PRIMARY KEY(ID_messaggio),
    FOREIGN KEY(autore) REFERENCES utenti(ID) ON UPDATE CASCADE ON DELETE NO ACTION,
    FOREIGN KEY(destinatario) REFERENCES utenti(ID) ON UPDATE CASCADE ON DELETE NO ACTION
)engine=InnoDB;
