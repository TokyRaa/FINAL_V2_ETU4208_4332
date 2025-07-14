<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>EmpruntObjets</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
  <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
  <div class="container">
    <a class="navbar-brand fw-bold" href="filtre.php">EmpruntObjets</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navMenu">
      <ul class="navbar-nav ms-auto">
        <?php if (!empty($_SESSION['membre'])): ?>
           <li class="nav-item"><a class="nav-link" href="../pages/liste_objets.php">Accueil</a></li>
          <li class="nav-item"><a class="nav-link" href="../pages/objets_abimes.php">Objets abîmés</a></li>
<li class="nav-item"><a class="nav-link" href="../pages/objets_non_abimes.php">Objets en bon état</a></li>
  <li class="nav-item"><a class="nav-link" href="../pages/fiche_membre.php?id=<?= (int)$_SESSION['membre']['id_membre'] ?>">Profil</a></li>
  <li class="nav-item"><a class="nav-link" href="../pages/logout.php">Déconnexion</a></li>
  <li class="nav-item nav-link disabled">Bonjour, <?= htmlspecialchars($_SESSION['membre']['nom']) ?></li>
<?php else: ?>
  <li class="nav-item"><a class="nav-link" href="../pages/inscription.php">Inscription</a></li>
  <li class="nav-item"><a class="nav-link" href="../pages/login.php">Connexion</a></li>
<?php endif; ?>

      </ul>
    </div>
  </div>
</nav>

<div class="container my-5">
