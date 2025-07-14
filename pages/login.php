<?php
session_start();
require_once __DIR__ . '/../inc/config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $mdp = trim($_POST['mdp']);

    // Recherche membre par email
    $stmt = $pdo->prepare("SELECT * FROM membre WHERE email = ?");
    $stmt->execute([$email]);
    $membre = $stmt->fetch();

    if ($membre && password_verify($mdp, $membre['mdp'])) {
        // Création de la session
        $_SESSION['membre'] = [
            'id_membre' => $membre['id_membre'],
            'nom'       => $membre['nom'],
            'email'     => $membre['email']
        ];
        // Redirection vers la liste des objets
        header('Location: ../pages/liste_objets.php');
        exit;
    } else {
        $error = 'Email ou mot de passe incorrect.';
    }
}

include __DIR__ . '/../inc/header.php';
?>

<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-5">
      <h2 class="mb-4">Connexion</h2>

      <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <form method="post" action="">
        <div class="mb-3">
          <label for="email" class="form-label">Email</label>
          <input id="email" type="email" name="email" class="form-control" required autofocus>
        </div>
        <div class="mb-3">
          <label for="mdp" class="form-label">Mot de passe</label>
          <input id="mdp" type="password" name="mdp" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Se connecter</button>
      </form>

      <p class="mt-3">
        Pas encore inscrit ? <a href="inscription.php">Inscrivez-vous ici</a>.
      </p>
    </div>
  </div>
</div>
