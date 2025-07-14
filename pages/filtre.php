<?php
require_once __DIR__ . '/../inc/config.php';
$cats = $pdo->query("SELECT * FROM categorie_objet ORDER BY nom_categorie")->fetchAll();
include __DIR__ . '/../inc/header.php';
?>

<section class="text-center mb-5">
  <h1 class="display-5">Trouvez des objets à emprunter</h1>
  <p class="lead text-muted">Sélectionnez une catégorie pour découvrir des trésors près de chez vous.</p>
</section>

<form method="get" action="liste_objets.php" class="row justify-content-center">
  <div class="col-md-6 mb-3">
    <select name="categorie" class="form-select form-select-lg" required>
      <option value="" disabled selected>-- Sélectionner une catégorie --</option>
      <?php foreach($cats as $cat): ?>
        <option value="<?= $cat['id_categorie'] ?>">
          <?= htmlspecialchars($cat['nom_categorie']) ?>
        </option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="col-auto">
    <button type="submit" class="btn btn-primary btn-lg">Voir les objets</button>
  </div>
</form>

<?php include __DIR__ . '/../inc/footer.php'; ?>
