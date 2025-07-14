<?php
require_once __DIR__ . '/../inc/config.php';
include __DIR__ . '/../inc/header.php';

$stmt = $pdo->query("
  SELECT o.nom_objet, m.nom AS proprio
  FROM objet o
  JOIN membre m ON o.id_membre = m.id_membre
  WHERE o.abime = 0
  ORDER BY o.nom_objet
");
$objets = $stmt->fetchAll();
?>

<div class="container py-4">
  <h2 class="mb-4">Objets abîmés</h2>

  <?php if (empty($objets)): ?>
    <div class="alert alert-success">Aucun objet abîmé actuellement.</div>
  <?php else: ?>
    <ul class="list-group">
      <?php foreach ($objets as $o): ?>
        <li class="list-group-item d-flex justify-content-between">
          <?= htmlspecialchars($o['nom_objet']) ?>
          <span class="text-muted">par <?= htmlspecialchars($o['proprio']) ?></span>
        </li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>
</div>

<?php include __DIR__ . '/../inc/footer.php'; ?>
