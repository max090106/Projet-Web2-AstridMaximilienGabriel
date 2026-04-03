<?php
session_start();
header('Content-Type: application/json');
require_once 'db.php';

// Vérifie que l'utilisateur est bien connecté
if (!isset($_SESSION['pseudo'])) {
    echo json_encode(['success' => false, 'message' => 'Vous devez être connecté pour réserver.']);
    exit;
}

$pdo = getDB();

$pdo->exec("
    CREATE TABLE IF NOT EXISTS reservations (
        id          INT AUTO_INCREMENT PRIMARY KEY,
        id_etudiant VARCHAR(20)  NOT NULL,
        professeur  VARCHAR(100) NOT NULL,
        creneau     VARCHAR(50)  NOT NULL,
        date_rdv    DATE         NOT NULL,
        created_at  TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY uniq_rdv (professeur, creneau, date_rdv)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // L'identifiant de l'étudiant vient directement de la session
    $idEtudiant = $_SESSION['pseudo'];
    $professeur = trim($_POST['professeur'] ?? '');
    $creneau    = trim($_POST['creneau']    ?? '');
    $dateRdv    = trim($_POST['date_rdv']   ?? '');

    if (!$professeur || !$creneau || !$dateRdv) {
        echo json_encode(['success' => false, 'message' => 'Tous les champs sont obligatoires.']);
        exit;
    }
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateRdv)) {
        echo json_encode(['success' => false, 'message' => 'Format de date invalide.']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("
            INSERT INTO reservations (id_etudiant, professeur, creneau, date_rdv)
            VALUES (:id_etudiant, :professeur, :creneau, :date_rdv)
        ");
        $stmt->execute([
            ':id_etudiant' => $idEtudiant,
            ':professeur'  => $professeur,
            ':creneau'     => $creneau,
            ':date_rdv'    => $dateRdv,
        ]);
        echo json_encode(['success' => true, 'message' => 'Réservation enregistrée avec succès !']);
    } catch (PDOException $e) {
        if ($e->getCode() === '23000') {
            echo json_encode(['success' => false, 'message' => 'Ce créneau est déjà réservé.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur : ' . $e->getMessage()]);
        }
    }
    exit;
}

echo json_encode(['success' => false, 'message' => 'Méthode non autorisée.']);
