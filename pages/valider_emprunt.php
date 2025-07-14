<?php
require_once __DIR__ . '/../inc/config.php';

if (!isset($_SESSION['membre'])) {
  header('Location: login.php');
  exit;
}

$id_objet = (int) ($_POST['id_objet'] ?? 0);
$duree = (int) ($_POST['duree'] ?? 0);

if ($id_objet < 1 || $duree < 1) {
  $_SESSION['error'] = "Paramètres invalides.";
  header("Location: liste_objets.php");
  exit;
}

// Vérifie si l'objet est déjà emprunté
$verif = $pdo->prepare("SELECT COUNT(*) FROM emprunt WHERE id_objet = ? AND date_retour IS NULL");
$verif->execute([$id_objet]);

if ($verif->fetchColumn() > 0) {
  $_SESSION['error'] = "Objet déjà emprunté.";
  header("Location: liste_objets.php");
  exit;
}

// Calcule la date de retour
$dateRetour = (new DateTime())->modify("+$duree days")->format('Y-m-d H:i:s');

// Insertion de l'emprunt
$stmt = $pdo->prepare("INSERT INTO emprunt (id_objet, id_membre, date_emprunt, date_retour) VALUES (?, ?, NOW(), ?)");
$stmt->execute([$id_objet, $_SESSION['membre']['id_membre'], $dateRetour]);

$_SESSION['success'] = "Emprunt enregistré pour $duree jour(s).";
header("Location: liste_objets.php");
exit;
