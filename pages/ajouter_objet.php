<?php
session_start();
require_once __DIR__ . '/../inc/config.php';

// Vérifie si membre connecté
if (empty($_SESSION['membre'])) {
    header('Location: login.php');
    exit;
}

// Récupérer catégories pour le select
$stmt = $pdo->query("SELECT id_categorie, nom_categorie FROM categorie_objet ORDER BY nom_categorie");
$categories = $stmt->fetchAll();

include __DIR__ . '/../inc/header.php';
?>

<div class="container py-5">
  <h2>Ajouter un nouvel objet</h2>

  <form method="post" action="ajouter_objet_traitement.php" enctype="multipart/form-data" class="mt-4">
    <div class="mb-3">
      <label for="nom_objet" class="form-label">Nom de l'objet</label>
      <input type="text" id="nom_objet" name="nom_objet" class="form-control" required>
    </div>

    <div class="mb-3">
      <label for="categorie" class="form-label">Catégorie</label>
      <select id="categorie" name="id_categorie" class="form-select" required>
        <option value="">-- Choisir une catégorie --</option>
        <?php foreach ($categories as $cat): ?>
          <option value="<?= $cat['id_categorie'] ?>"><?= htmlspecialchars($cat['nom_categorie']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="mb-3">
      <label for="images" class="form-label">Images (plusieurs possibles)</label>
      <input type="file" id="images" name="images[]" class="form-control" multiple accept="image/jpeg,image/png,image/jpg">
      <div class="form-text">Formats acceptés : jpeg, jpg, png</div>
    </div>

    <button type="submit" class="btn btn-primary">Ajouter l'objet</button>
  </form>
</div>

<?php include __DIR__ . '/../inc/footer.php'; ?>
