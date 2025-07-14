<?php
require_once __DIR__ . '/../inc/config.php';

$categories = $pdo->query("SELECT * FROM categorie_objet")->fetchAll();

$idCat = isset($_GET['categorie']) ? (int) $_GET['categorie'] : 0;
$nomObjet = trim($_GET['nom'] ?? '');
$disponible = isset($_GET['dispo']) ? 1 : 0;

$sql = "
  SELECT o.id_objet, o.nom_objet, o.id_membre, o.id_categorie, m.nom AS proprio,
         e.date_emprunt
  FROM objet o
  JOIN membre m ON o.id_membre = m.id_membre
  LEFT JOIN emprunt e ON o.id_objet = e.id_objet AND e.date_retour IS NULL
  WHERE 1
";
$params = [];

if ($idCat > 0) {
    $sql .= " AND o.id_categorie = :cat";
    $params['cat'] = $idCat;
}
if ($nomObjet) {
    $sql .= " AND o.nom_objet LIKE :nom";
    $params['nom'] = "%$nomObjet%";
}
if ($disponible) {
    $sql .= " AND e.date_emprunt IS NULL";
}
$sql .= " ORDER BY o.nom_objet";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$objets = $stmt->fetchAll();

include __DIR__ . '/../inc/header.php';
?>

<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold mb-0">Liste des objets</h2>
    <?php if (!empty($_SESSION['membre'])): ?>
      <a href="ajouter_objet.php" class="btn btn-success">+ Ajouter un objet</a>
    <?php endif; ?>
  </div>

  <form method="get" class="row g-3 mb-4">
    <div class="col-md-4">
      <label for="categorie" class="form-label">Catégorie</label>
      <select name="categorie" id="categorie" class="form-select">
        <option value="0">-- Toutes --</option>
        <?php foreach ($categories as $cat): ?>
          <option value="<?= $cat['id_categorie'] ?>" <?= $idCat == $cat['id_categorie'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($cat['nom_categorie']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="col-md-4">
      <label for="nom" class="form-label">Nom de l’objet</label>
      <input type="text" name="nom" id="nom" value="<?= htmlspecialchars($nomObjet) ?>" class="form-control">
    </div>

    <div class="col-md-4 d-flex align-items-end">
      <div class="form-check me-3">
        <input type="checkbox" name="dispo" id="dispo" class="form-check-input" <?= $disponible ? 'checked' : '' ?>>
        <label class="form-check-label" for="dispo">Disponible uniquement</label>
      </div>
      <button type="submit" class="btn btn-primary">Rechercher</button>
    </div>
  </form>

  <?php if (empty($objets)): ?>
    <div class="alert alert-info">Aucun objet trouvé pour votre recherche.</div>
  <?php else: ?>
    <div class="row g-4">
      <?php foreach ($objets as $o): ?>
        <?php
          $imgStmt = $pdo->prepare("SELECT nom_image FROM images_objet WHERE id_objet = ? LIMIT 1");
          $imgStmt->execute([$o['id_objet']]);
          $imgName = $imgStmt->fetchColumn();
          $imgPath = $imgName ? "../assets/uploads/$imgName" : "../assets/uploads/default.jpeg";
        ?>
        <div class="col-sm-6 col-lg-4">
          <a href="fiche_objet.php?id=<?= $o['id_objet'] ?>" class="text-decoration-none text-dark">
            <div class="card h-100 shadow-sm">
              <img src="<?= $imgPath ?>" class="card-img-top" alt="<?= htmlspecialchars($o['nom_objet']) ?>" style="height: 200px; object-fit: cover;">
              <div class="card-body">
                <h5 class="card-title"><?= htmlspecialchars($o['nom_objet']) ?></h5>
                <p class="card-text mb-1">Propriétaire : <?= htmlspecialchars($o['proprio']) ?></p>

                <?php if ($o['date_emprunt']): ?>
                  <?php
                    $retourStmt = $pdo->prepare("SELECT date_retour FROM emprunt WHERE id_objet = ? AND date_retour IS NOT NULL ORDER BY id_emprunt DESC LIMIT 1");
                    $retourStmt->execute([$o['id_objet']]);
                    $dateRetour = $retourStmt->fetchColumn();
                  ?>
                  <span class="badge bg-warning text-dark">Emprunté</span>
                  <?php if ($dateRetour): ?>
                    <p class="text-muted small mb-0">Retour prévu : <?= date('d/m/Y', strtotime($dateRetour)) ?></p>
                  <?php endif; ?>
                <?php else: ?>
                  <span class="badge bg-success mb-2 d-inline-block">Disponible</span>
                  <?php if (!empty($_SESSION['membre'])): ?>
                    <form method="get" action="emprunter.php">
                      <input type="hidden" name="id" value="<?= $o['id_objet'] ?>">
                      <button type="submit" class="btn btn-sm btn-outline-primary mt-2">Emprunter</button>
                    </form>
                  <?php endif; ?>
                <?php endif; ?>
              </div>
            </div>
          </a>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>

<?php include __DIR__ . '/../inc/footer.php'; ?>
