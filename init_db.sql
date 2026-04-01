-- Exécuter une seule fois dans phpMyAdmin ou MySQL CLI

CREATE DATABASE IF NOT EXISTS efrei_rdv
    CHARACTER SET utf8
    COLLATE utf8_general_ci;

USE efrei_rdv;

CREATE TABLE IF NOT EXISTS reservations (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    id_etudiant VARCHAR(20)  NOT NULL,
    professeur  VARCHAR(100) NOT NULL,
    creneau     VARCHAR(50)  NOT NULL,
    date_rdv    DATE         NOT NULL,
    created_at  TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uniq_rdv (professeur, creneau, date_rdv)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
