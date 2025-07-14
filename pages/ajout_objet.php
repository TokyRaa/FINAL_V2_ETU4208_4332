<?php
require_once __DIR__ . '/../inc/config.php';

if (!isset($_SESSION['membre'])) {
    header('Location: login.php');
    exit;
}

$categories = $pdo->query("SELECT * FROM categorie_objet")->fetchAll();
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom_objet = trim($_POST['nom_objet']);
    $id_cat = (int) $_POST['id_categorie'];
    $id_membre = $_SESSION['membre']['id_membre'];

    if (!$nom_objet || !$id_cat) {
        $error = "Veuillez remplir tous les champs.";
    } else {
        // Insère l'objet
        $stmt = $pdo->prepare("INSERT INTO objet (nom_objet, id_categorie, id_membre) VALUES (?, ?, ?)");
        $stmt->execute([$nom_objet, $id_cat, $id_membre]);
        $id_objet = $pdo->lastInsertId();

        // Upload des images
        $files = $_FILES['images'];
        $count = count($files['name']);
        for ($i = 0; $i < $count; $i++) {
            if ($files['error'][$i] === UPLOAD_ERR_OK) {
                $ext = pathinfo($files['name'][$i], PATHINFO_EXTENSION);
                $img_name = uniqid("img_") . "." . strtolower($ext);
                move_uploaded_file($files['tmp_name'][$i], "../assets/uploads/$img_name");

                $stmtImg = $pdo->prepare("INSERT INTO images_objet (id_objet, nom_image) VALUES (?, ?)");
                $stmtImg->execute([$id_objet, $img_name]);
            }
        }

        $success = "Objet ajouté avec succès !";
    }
}

include __DIR__ . '/../inc/header.php';
?>

<h2>Ajouter un objet</h2>

<?php if ($success): ?>
  <div class="alert alert-success"><?= $success ?></div>
<?php elseif ($error): ?>
  <div class="alert alert-danger"><?= $error ?></div>
<?php endif; ?>

<form method="post" enctype="multipart/form-data" class="mb-4">
  <div class="mb-3">
    <label class="form-label">Nom de l'objet</label>
    <input type="text" name="nom_objet" class="form-control" required>
  </div>
  <div class="mb-3">
    <label class="form-label">Catégorie</label>
    <select name="id_categorie" class="form-select" required>
      <option value="">-- Choisir --</option>
      <?php foreach($categories as $cat): ?>
        <option value="<?= $cat['id_categorie'] ?>"><?= htmlspecialchars($cat['nom_categorie']) ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="mb-3">
    <label class="form-label">Images (vous pouvez en sélectionner plusieurs)</label>
    <input type="file" name="images[]" class="form-control" multiple accept="image/*">
  </div>
  <button class="btn btn-primary">Ajouter</button>
</form>

<?php include __DIR__ . '/../inc/footer.php'; ?>
