<!DOCTYPE html>
<?php
session_start();
require_once 'db.php';

// Redirige vers la connexion si pas connecté
if (!isset($_SESSION['pseudo'])) {
    header("Location: connexion.php");
    exit();
}

$pseudo = $_SESSION['pseudo'];
$pdo    = getDB();

// Récupère le rôle de l'utilisateur connecté
$stmtUser = $pdo->prepare("SELECT role FROM membres WHERE pseudo = :pseudo");
$stmtUser->execute([':pseudo' => $pseudo]);
$user = $stmtUser->fetch();
$role = $user ? $user['role'] : 'etudiant'; // par défaut étudiant si colonne absente

$reservations = [];

if ($role === 'prof') {
    // Le pseudo du prof doit correspondre au champ "professeur" dans reservations.
    // On cherche toutes les réservations où le professeur correspond au pseudo connecté.
    $stmt = $pdo->prepare("
        SELECT id_etudiant, professeur, creneau, date_rdv, created_at
        FROM reservations
        WHERE professeur = :pseudo
        ORDER BY date_rdv ASC, creneau ASC
    ");
    $stmt->execute([':pseudo' => $pseudo]);
} else {
    // Étudiant : on cherche par son numéro étudiant (stocké dans id_etudiant)
    $stmt = $pdo->prepare("
        SELECT id_etudiant, professeur, creneau, date_rdv, created_at
        FROM reservations
        WHERE id_etudiant = :pseudo
        ORDER BY date_rdv ASC, creneau ASC
    ");
    $stmt->execute([':pseudo' => $pseudo]);
}

$reservations = $stmt->fetchAll();
?>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mes réservations — <?= htmlspecialchars($pseudo) ?></title>
    <link rel="stylesheet" href="../css/styles.css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style href="../css/reservation.css" rel="stylesheet"></style>
</head>

<body>
    <?php include("header.php"); ?>

    <div id="mes-resa-wrapper">

        <a href="Accueil.php" class="btn-retour">← Retour à l'accueil</a>

        <?php if ($role === 'prof'): ?>

            <h2>
                Mes heures de permanence
                <span class="badge badge-prof">Professeur</span>
            </h2>
            <p class="sous-titre">
                Bonjour <strong><?= htmlspecialchars($pseudo) ?></strong>,
                voici tous les créneaux que les étudiants ont réservés avec vous.
            </p>

            <table class="resa-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>N° Étudiant</th>
                        <th>Date</th>
                        <th>Créneau</th>
                        <th>Enregistré le</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($reservations)): ?>
                        <tr><td colspan="5" class="no-resa">Aucune réservation pour l'instant.</td></tr>
                    <?php else: ?>
                        <?php foreach ($reservations as $i => $r): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><?= htmlspecialchars($r['id_etudiant']) ?></td>
                            <td class="date-cell"><?= htmlspecialchars($r['date_rdv']) ?></td>
                            <td><?= htmlspecialchars($r['creneau']) ?></td>
                            <td><?= htmlspecialchars($r['created_at']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>

        <?php else: ?>

            <h2>
                Mes cours réservés
                <span class="badge badge-etudiant">Étudiant</span>
            </h2>
            <p class="sous-titre">
                Bonjour <strong><?= htmlspecialchars($pseudo) ?></strong>,
                voici tous les rendez-vous que vous avez réservés avec des professeurs.
            </p>

            <table class="resa-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Professeur</th>
                        <th>Date</th>
                        <th>Créneau</th>
                        <th>Enregistré le</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($reservations)): ?>
                        <tr><td colspan="5" class="no-resa">Vous n'avez encore aucune réservation.</td></tr>
                    <?php else: ?>
                        <?php foreach ($reservations as $i => $r): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><?= htmlspecialchars($r['professeur']) ?></td>
                            <td class="date-cell"><?= htmlspecialchars($r['date_rdv']) ?></td>
                            <td><?= htmlspecialchars($r['creneau']) ?></td>
                            <td><?= htmlspecialchars($r['created_at']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>

        <?php endif; ?>

    </div>

    <?php include("footer.php"); ?>
</body>
</html>
