<?php
require_once __DIR__ . '/../inc/config.php';

if (empty($_GET['id'])) {
    header('Location: liste_objets.php');
    exit;
}

$id = (int) $_GET['id'];
$stmt = $pdo->prepare("SELECT o.*, m.nom AS proprio_nom FROM objet o JOIN membre m ON o.id_membre = m.id_membre WHERE o.id_objet = ?");
$stmt->execute([$id]);
$objet = $stmt->fetch();

if (!$objet) {
    $_SESSION['error'] = "Objet introuvable.";
    header('Location: liste_objets.php');
    exit;
}

// Récupère les images
$stmtImg = $pdo->prepare("SELECT nom_image FROM images_objet WHERE id_objet = ?");
$stmtImg->execute([$id]);
$images = $stmtImg->fetchAll(PDO::FETCH_COLUMN);

// Historique des emprunts
$stmtHist = $pdo->prepare("
    SELECT e.date_emprunt, e.date_retour, m.nom 
    FROM emprunt e 
    JOIN membre m ON e.id_membre = m.id_membre 
    WHERE e.id_objet = ? 
    ORDER BY e.date_emprunt DESC
");
$stmtHist->execute([$id]);
$historique = $stmtHist->fetchAll();

include __DIR__ . '/../inc/header.php';
?>

<div class="container py-4">
  <div class="row g-4 align-items-start mb-4">
    <div class="col-md-5">
      <?php if (!empty($images)): ?>
        <img src="../assets/uploads/<?= htmlspecialchars($images[0]) ?>" class="img-fluid rounded shadow-sm">
      <?php else: ?>
        <img src="../assets/uploads/default.jpeg" class="img-fluid rounded shadow-sm">
      <?php endif; ?>
    </div>

    <div class="col-md-7">
     <div class="d-flex justify-content-between align-items-start mb-2">
  <h2 class="fw-bold"><?= htmlspecialchars($objet['nom_objet']) ?></h2>
  <div>
    <a href="liste_objets.php" class="btn btn-outline-secondary btn-sm me-2">&larr; Retour à la liste</a>

    <?php if (!empty($_SESSION['membre']) && $_SESSION['membre']['id_membre'] == $objet['id_membre']): ?>
      <form method="post" action="supprimer_objet.php" onsubmit="return confirm('Confirmer la suppression ?');" class="d-inline">
        <input type="hidden" name="id_objet" value="<?= $objet['id_objet'] ?>">
        <button type="submit" class="btn btn-danger btn-sm">Supprimer</button>
      </form>
    <?php endif; ?>
  </div>
</div>
      <p><strong>Propriétaire :</strong> <?= htmlspecialchars($objet['proprio_nom']) ?></p>
      <p>
        <strong>État :</strong>
        <?php if ($objet['abime']): ?>
          <span class="badge bg-danger">Abîmé</span>
        <?php else: ?>
          <span class="badge bg-success">En bon état</span>
        <?php endif; ?>
      </p>
      <p><strong>Catégorie :</strong> 
        <?php
          $cat = $pdo->prepare("SELECT nom_categorie FROM categorie_objet WHERE id_categorie = ?");
          $cat->execute([$objet['id_categorie']]);
          echo htmlspecialchars($cat->fetchColumn());
        ?>
      </p>
    </div>
  </div>

  <?php if (count($images) > 1): ?>
    <h5>Autres images</h5>
    <div class="row mb-4">
      <?php foreach(array_slice($images, 1) as $img): ?>
        <div class="col-md-3 mb-3">
          <img src="../assets/uploads/<?= htmlspecialchars($img) ?>" class="img-fluid rounded shadow-sm">
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <h5>Historique des emprunts :</h5>
  <?php if (empty($historique)): ?>
    <p class="text-muted">Aucun emprunt enregistré pour cet objet.</p>
  <?php else: ?>
    <ul class="list-group mb-4">
      <?php foreach ($historique as $h): ?>
        <li class="list-group-item">
          <?= htmlspecialchars($h['nom']) ?> — du <?= htmlspecialchars($h['date_emprunt']) ?>
          <?php if ($h['date_retour']): ?>
            au <?= htmlspecialchars($h['date_retour']) ?>
          <?php else: ?>
            (en cours)
          <?php endif; ?>
        </li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>
</div>

<?php include __DIR__ . '/../inc/footer.php'; ?>
