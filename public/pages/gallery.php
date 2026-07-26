<?php require __DIR__ . '/../../includes/header.php';
$stmt = pdo()->query('SELECT * FROM gallery ORDER BY uploaded_at DESC LIMIT 36');
$items = $stmt->fetchAll();
?>
<h2>Gallery</h2>
<div class="cards">
  <?php foreach($items as $it): ?>
    <div class="card"><img src="<?php echo e($it['image_path']); ?>" alt="<?php echo e($it['caption']); ?>" style="width:100%;height:auto;border-radius:6px;" loading="lazy"><div><?php echo e($it['caption']); ?></div></div>
  <?php endforeach; ?>
</div>
<?php require __DIR__ . '/../../includes/footer.php'; ?>
