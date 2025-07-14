<?php
require_once __DIR__ . '/../inc/config.php';

// Vérifier si l'utilisateur est connecté
if (empty($_SESSION['membre'])) {
    $_SESSION['error'] = "Veuillez vous connecter pour emprunter un objet.";
    header('Location: ../index.php');
    exit;
}

$idMembre = $_SESSION['membre']['id_membre'];

// Récupère l'objet
$idObjet = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$stmt = $pdo->prepare("
    SELECT o.*, m.nom AS proprio_nom
    FROM objet o
    JOIN membre m ON o.id_membre = m.id_membre
    WHERE o.id_objet = ?
");
$stmt->execute([$idObjet]);
$obj = $stmt->fetch();

if (!$obj) {
    $_SESSION['error'] = "Objet introuvable.";
    header('Location: liste_objets.php');
    exit;
}

// Vérifie que ce n’est pas son propre objet
if ($obj['id_membre'] == $idMembre) {
    $_SESSION['error'] = "Vous ne pouvez pas emprunter votre propre objet.";
    header('Location: liste_objets.php');
    exit;
}

// Vérifie que l’objet n’est pas abîmé
if ($obj['abime']) {
    $_SESSION['error'] = "Cet objet est abîmé et ne peut pas être emprunté.";
    header('Location: liste_objets.php');
    exit;
}

// Vérifie que l’objet n’est pas déjà emprunté
$stmt = $pdo->prepare("SELECT COUNT(*) FROM emprunt WHERE id_objet = ? AND date_retour IS NULL");
$stmt->execute([$idObjet]);
$dejaPris = $stmt->fetchColumn();

if ($dejaPris > 0) {
    $_SESSION['error'] = "Cet objet est déjà emprunté par un autre membre.";
    header('Location: liste_objets.php');
    exit;
}

// Traitement du formulaire d’emprunt
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $duree = (int) $_POST['duree'];
    if ($duree < 1 || $duree > 30) {
        $_SESSION['error'] = "Durée invalide (1 à 30 jours autorisés).";
        header("Location: emprunter.php?id=" . $idObjet);
        exit;
    }

    $dateEmprunt = date('Y-m-d');
    $dateRetour = date('Y-m-d', strtotime("+$duree days"));

    $stmt = $pdo->prepare("
        INSERT INTO emprunt (id_objet, id_membre, date_emprunt, date_retour)
        VALUES (?, ?, ?, ?)
    ");
    $stmt->execute([$idObjet, $idMembre, $dateEmprunt, $dateRetour]);

    $_SESSION['success'] = "Objet emprunté avec succès jusqu’au $dateRetour.";
    header('Location: liste_objets.php');
    exit;
}

include __DIR__ . '/../inc/header.php';
?>

<div class="container py-4">
    <h2 class="mb-4">Emprunter l’objet : <?= htmlspecialchars($obj['nom_objet']) ?></h2>

    <?php if (!empty($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <p><strong>Propriétaire :</strong> <?= htmlspecialchars($obj['proprio_nom']) ?></p>

    <form method="post" class="mt-4">
        <div class="mb-3">
            <label for="duree" class="form-label">Durée de l’emprunt (en jours)</label>
            <input type="number" name="duree" id="duree" class="form-control" min="1" max="30" value="7" required>
        </div>
        <button type="submit" class="btn btn-primary">Valider l’emprunt</button>
        <a href="liste_objets.php" class="btn btn-secondary">Annuler</a>
    </form>
</div>

<?php include __DIR__ . '/../inc/footer.php'; ?>
