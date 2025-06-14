<?php
require_once 'config.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: reservation_list.php');
    exit();
}

$id = (int)$_GET['id'];

try {
    // Démarrer transaction
    $pdo->beginTransaction();

    // Vérifier que la réservation est confirmée
    $stmtCheck = $pdo->prepare("SELECT * FROM Reservation WHERE id_reservation = ? AND statut = 'confirmée'");
    $stmtCheck->execute([$id]);
    $reservation = $stmtCheck->fetch(PDO::FETCH_ASSOC);

    if (!$reservation) {
        // Pas trouvé ou pas confirmée => retour liste avec message possible
        $pdo->rollBack();
        header('Location: reservation_list.php?msg=archive_error');
        exit();
    }

    // Insérer dans HistoriqueReservation
    $stmtInsert = $pdo->prepare("INSERT INTO HistoriqueReservation (date_debut, date_fin, statut, id_user, id_property) VALUES (?, ?, ?, ?, ?)");
    $stmtInsert->execute([
        $reservation['date_debut'],
        $reservation['date_fin'],
        $reservation['statut'],
        $reservation['id_user'],
        $reservation['id_property']
    ]);

    // Supprimer de Reservation
    $stmtDelete = $pdo->prepare("DELETE FROM Reservation WHERE id_reservation = ?");
    $stmtDelete->execute([$id]);

    // Valider transaction
    $pdo->commit();

    header('Location: reservation_list.php?msg=archived');
    exit();

} catch (Exception $e) {
    $pdo->rollBack();
    echo "Erreur lors de l'archivage : " . $e->getMessage();
}
