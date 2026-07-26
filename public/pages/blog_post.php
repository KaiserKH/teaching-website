<?php require __DIR__ . '/../../includes/header.php';
$slug = $_GET['slug'] ?? '';
$stmt = pdo()->prepare('SELECT * FROM blog_posts WHERE slug = ? LIMIT 1');
$stmt->execute([$slug]);
$post = $stmt->fetch();
if (!$post) { echo '<h2>Post not found</h2>'; require __DIR__ . '/../../includes/footer.php'; exit; }
?>
<article class="card">
  <h2><?php echo e($post['title']); ?></h2>
  <div><?php echo $post['content']; ?></div>
</article>
<?php require __DIR__ . '/../../includes/footer.php'; ?>
