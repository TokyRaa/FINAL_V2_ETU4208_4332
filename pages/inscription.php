<?php
session_start();
require_once __DIR__ . '/../inc/config.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom']);
    $email = trim($_POST['email']);
    $mdp = $_POST['mdp'];
    $mdp_confirm = $_POST['mdp_confirm'];

    if ($mdp !== $mdp_confirm) {
        $error = "Les mots de passe ne correspondent pas.";
    } else {
        // Vérifie si email existe déjà
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM membre WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetchColumn() > 0) {
            $error = "Cet email est déjà utilisé.";
        } else {
            // Insère le membre
            $mdp_hash = password_hash($mdp, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO membre (nom, email, mdp) VALUES (?, ?, ?)");
            $stmt->execute([$nom, $email, $mdp_hash]);
            $success = "Inscription réussie. Vous pouvez maintenant vous connecter.";
        }
    }
}

include __DIR__ . '/../inc/header.php';
?>

<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-5">
      <h2 class="mb-4">Inscription</h2>

      <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
      <?php elseif ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
      <?php endif; ?>

      <form method="post" action="">
        <div class="mb-3">
          <label>Nom complet</label>
          <input type="text" name="nom" class="form-control" required>
        </div>
        <div class="mb-3">
          <label>Email</label>
          <input type="email" name="email" class="form-control" required>
        </div>
        <div class="mb-3">
          <label>Mot de passe</label>
          <input type="password" name="mdp" class="form-control" required>
        </div>
        <div class="mb-3">
          <label>Confirmer mot de passe</label>
          <input type="password" name="mdp_confirm" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">S’inscrire</button>
      </form>

      <p class="mt-3">
        Déjà membre ? <a href="login.php">Connectez-vous ici</a>.
      </p>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../inc/footer.php'; ?>
