<?php
require_once __DIR__ . '/../inc/config.php';

// Vérifier que l'id membre est passé en GET
if (empty($_GET['id'])) {
    header('Location: liste_objets.php');
    exit;
}

$idMembre = (int) $_GET['id'];

// Récupérer les infos du membre
$stmt = $pdo->prepare("SELECT id_membre, nom, email, date_naissance, ville, genre, image_profil FROM membre WHERE id_membre = ?");
$stmt->execute([$idMembre]);
$membre = $stmt->fetch();

if (!$membre) {
    // Membre non trouvé
    header('Location: liste_objets.php');
    exit;
}

// Récupérer les objets du membre, regroupés par catégorie
$sql = "
    SELECT c.nom_categorie, o.id_objet, o.nom_objet
    FROM objet o
    JOIN categorie_objet c ON o.id_categorie = c.id_categorie
    WHERE o.id_membre = ?
    ORDER BY c.nom_categorie, o.nom_objet
";
$stmt = $pdo->prepare($sql);
$stmt->execute([$idMembre]);
$objets = $stmt->fetchAll();

// Regrouper les objets par catégorie
$objetsParCategorie = [];
foreach ($objets as $objet) {
    $objetsParCategorie[$objet['nom_categorie']][] = $objet;
}

include __DIR__ . '/../inc/header.php';
?>

<h2 class="mb-4">Fiche membre : <?= htmlspecialchars($membre['nom']) ?></h2>

<div class="row mb-4">
  <div class="col-md-4 text-center">
    <?php
    $img = $membre['image_profil'] ? "../assets/uploads/" . htmlspecialchars($membre['image_profil']) : "../assets/uploads/default.jpeg";
    ?>
    <img src="<?= $img ?>" alt="Photo de profil de <?= htmlspecialchars($membre['nom']) ?>" class="img-fluid rounded mb-3" style="max-height: 300px;">
  </div>
  <div class="col-md-8">
    <ul class="list-group">
      <li class="list-group-item"><strong>Nom :</strong> <?= htmlspecialchars($membre['nom']) ?></li>
      <li class="list-group-item"><strong>Email :</strong> <?= htmlspecialchars($membre['email']) ?></li>
      <li class="list-group-item"><strong>Date de naissance :</strong> <?= htmlspecialchars($membre['date_naissance']) ?></li>
      <li class="list-group-item"><strong>Ville :</strong> <?= htmlspecialchars($membre['ville']) ?></li>
      <li class="list-group-item"><strong>Genre :</strong> <?= htmlspecialchars($membre['genre']) ?></li>
    </ul>
  </div>
</div>

<h3>Objets de <?= htmlspecialchars($membre['nom']) ?></h3>

<?php if (empty($objetsParCategorie)): ?>
  <div class="alert alert-info">Ce membre n'a pas encore d'objets enregistrés.</div>
<?php else: ?>
  <?php foreach ($objetsParCategorie as $categorie => $objets): ?>
    <h4 class="mt-4"><?= htmlspecialchars($categorie) ?></h4>
    <div class="row g-3">
      <?php foreach ($objets as $objet): ?>
        <?php
        // Récupérer image principale
        $imgStmt = $pdo->prepare("SELECT nom_image FROM images_objet WHERE id_objet = ? LIMIT 1");
        $imgStmt->execute([$objet['id_objet']]);
        $imgName = $imgStmt->fetchColumn();
        $imgPath = $imgName ? "../assets/uploads/$imgName" : "../assets/uploads/default.jpeg";
        ?>
        <div class="col-sm-6 col-md-4 col-lg-3">
          <a href="fiche_objet.php?id=<?= $objet['id_objet'] ?>" class="text-decoration-none text-dark">
            <div class="card h-100 shadow-sm">
              <img src="<?= $imgPath ?>" alt="<?= htmlspecialchars($objet['nom_objet']) ?>" class="card-img-top" style="height: 180px; object-fit: cover;">
              <div class="card-body">
                <h6 class="card-title"><?= htmlspecialchars($objet['nom_objet']) ?></h6>
              </div>
            </div>
          </a>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endforeach; ?>
<?php endif; ?>

<?php include __DIR__ . '/../inc/footer.php'; ?>
