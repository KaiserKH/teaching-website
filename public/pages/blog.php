<?php require __DIR__ . '/../../includes/header.php';
$stmt = pdo()->query('SELECT id,title,slug,LEFT(content,200) as excerpt,published_at FROM blog_posts WHERE published_at IS NOT NULL ORDER BY published_at DESC');
$posts = $stmt->fetchAll();
?>
<h2>Blog & Study Tips</h2>
<?php foreach($posts as $p): ?>
  <article class="card"><h3><?php echo e($p['title']); ?></h3><p><?php echo e($p['excerpt']); ?></p><a href="/blog/post?slug=<?php echo urlencode($p['slug']); ?>">Read more</a></article>
<?php endforeach; ?>
<?php require __DIR__ . '/../../includes/footer.php'; ?>
