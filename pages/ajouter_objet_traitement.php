<?php
session_start();
require_once __DIR__ . '/../inc/config.php';

if (empty($_SESSION['membre'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ajouter_objet.php');
    exit;
}

$nom_objet = trim($_POST['nom_objet'] ?? '');
$id_categorie = (int)($_POST['id_categorie'] ?? 0);
$id_membre = $_SESSION['membre']['id_membre'];

// Validation simple des champs
if ($nom_objet === '' || $id_categorie === 0) {
    $_SESSION['error'] = "Veuillez remplir tous les champs.";
    header('Location: ajouter_objet.php');
    exit;
}

// Insertion de l'objet
$stmt = $pdo->prepare("INSERT INTO objet (nom_objet, id_categorie, id_membre) VALUES (?, ?, ?)");
$stmt->execute([$nom_objet, $id_categorie, $id_membre]);
$id_objet = $pdo->lastInsertId();

// Upload images
$uploadDir = __DIR__ . '/../assets/uploads/';
if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

$allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
$uploadedImages = 0;

if (!empty($_FILES['images']) && !empty($_FILES['images']['name'][0])) {
    foreach ($_FILES['images']['tmp_name'] as $key => $tmpName) {
        $error = $_FILES['images']['error'][$key];
        if ($error === UPLOAD_ERR_OK) {
            $type = mime_content_type($tmpName);
            if (!in_array($type, $allowedTypes)) continue;

            $ext = pathinfo($_FILES['images']['name'][$key], PATHINFO_EXTENSION);
            $filename = uniqid('img_') . '.' . $ext;

            if (move_uploaded_file($tmpName, $uploadDir . $filename)) {
                $stmtImg = $pdo->prepare("INSERT INTO images_objet (id_objet, nom_image) VALUES (?, ?)");
                $stmtImg->execute([$id_objet, $filename]);
                $uploadedImages++;
            }
        }
    }
}

// Si aucune image uploadée, insérer image par défaut
if ($uploadedImages === 0) {
    $defaultImg = 'default.jpeg'; // Assure-toi que ce fichier existe dans assets/uploads
    $stmtImg = $pdo->prepare("INSERT INTO images_objet (id_objet, nom_image) VALUES (?, ?)");
    $stmtImg->execute([$id_objet, $defaultImg]);
}

$_SESSION['success'] = "Objet ajouté avec succès.";
header('Location: liste_objets.php');
exit;
