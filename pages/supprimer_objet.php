<?php
require_once __DIR__ . '/../inc/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['id_objet'])) {
    header('Location: liste_objets.php');
    exit;
}

$idObjet = (int) $_POST['id_objet'];

// Vérifie que l'objet existe et que le membre connecté est bien le propriétaire
$stmt = $pdo->prepare("SELECT id_membre FROM objet WHERE id_objet = ?");
$stmt->execute([$idObjet]);
$proprio = $stmt->fetchColumn();

if (!$proprio) {
    $_SESSION['error'] = "Objet non trouvé.";
    header('Location: liste_objets.php');
    exit;
}

if (empty($_SESSION['membre']) || $_SESSION['membre']['id_membre'] != $proprio) {
    $_SESSION['error'] = "Vous n'avez pas le droit de supprimer cet objet.";
    header('Location: liste_objets.php');
    exit;
}

// Supprime d'abord les images associées (fichiers + base)
$stmtImg = $pdo->prepare("SELECT nom_image FROM images_objet WHERE id_objet = ?");
$stmtImg->execute([$idObjet]);
$images = $stmtImg->fetchAll(PDO::FETCH_COLUMN);

foreach ($images as $img) {
    $filePath = __DIR__ . '/../assets/uploads/' . $img;
    if (file_exists($filePath)) {
        unlink($filePath);
    }
}

$pdo->prepare("DELETE FROM images_objet WHERE id_objet = ?")->execute([$idObjet]);

// Supprime les emprunts liés
$pdo->prepare("DELETE FROM emprunt WHERE id_objet = ?")->execute([$idObjet]);

// Supprime l'objet
$pdo->prepare("DELETE FROM objet WHERE id_objet = ?")->execute([$idObjet]);

$_SESSION['success'] = "Objet supprimé avec succès.";
header('Location: liste_objets.php');
exit;
