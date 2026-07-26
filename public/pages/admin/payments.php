<?php require __DIR__ . '/../../../includes/header.php';
if (!is_admin_logged_in()){ header('Location:/admin_login'); exit; }
if (!enforce_session_timeout()) { admin_logout(); header('Location:/admin_login'); exit; }

$stmt = pdo()->query('SELECT p.*, s.name as student_name, f.amount as fee_amount, f.month as fee_month FROM payments p LEFT JOIN students s ON p.student_id=s.id LEFT JOIN fees f ON p.fee_id=f.id ORDER BY p.created_at DESC');
$payments = $stmt->fetchAll();
?>
<h2>Payments</h2>
<?php if(!$payments) echo '<div class="card">No payments yet.</div>'; ?>
<?php foreach($payments as $p): ?>
  <div class="card">
    <div><strong>Student:</strong> <?php echo e($p['student_name']); ?> (ID: <?php echo e($p['student_id']); ?>)</div>
    <div><strong>Provider:</strong> <?php echo e($p['provider']); ?> | <strong>Amount:</strong> ₹<?php echo e($p['amount']); ?> | <strong>Status:</strong> <?php echo e($p['status']); ?></div>
    <div><strong>Created:</strong> <?php echo e($p['created_at']); ?></div>
    <?php if($p['screenshot']): ?>
      <div style="margin-top:8px;"><strong>Screenshot:</strong><br>
        <?php $ext = strtolower(pathinfo($p['screenshot'], PATHINFO_EXTENSION)); ?>
        <?php if(in_array($ext, ['jpg','jpeg','png'])): ?>
          <a href="#" onclick="showPreview('<?php echo e($p['screenshot']); ?>'); return false;">View image</a>
        <?php elseif($ext==='pdf'): ?>
          <a href="#" onclick="showPreview('<?php echo e($p['screenshot']); ?>'); return false;">View PDF</a>
        <?php else: ?>
          <a href="<?php echo e($p['screenshot']); ?>" target="_blank">Download</a>
        <?php endif; ?>
      </div>
    <?php endif; ?>
    <?php if($p['receipt']): ?><div><strong>Receipt/Txn:</strong> <?php echo e($p['receipt']); ?></div><?php endif; ?>
    <div style="margin-top:8px;">
      <?php if($p['status']==='pending'): ?>
        <button class="btn" onclick="updatePayment(<?php echo intval($p['id']); ?>,'approve')">Mark Paid</button>
        <button class="btn danger" onclick="updatePayment(<?php echo intval($p['id']); ?>,'fail')">Mark Failed</button>
      <?php else: ?>
        <em>Processed</em>
      <?php endif; ?>
        <div style="margin-top:6px;"><a href="/admin/payment_audit?payment_id=<?php echo intval($p['id']); ?>">View history</a></div>
    </div>
  </div>
<?php endforeach; ?>

<script>
async function updatePayment(id, action){
  if (!confirm('Confirm '+action+' for payment '+id+'?')) return;
  const fd = new FormData(); fd.append('payment_id', id); fd.append('action', action);
  const res = await fetch('/api/admin_update_payment.php',{method:'POST', body: fd});
  const data = await res.json();
  if (data.ok) location.reload(); else alert('Error: '+(data.error||data.message));
}
</script>

<!-- Preview modal -->
<div id="preview-modal" style="display:none;position:fixed;left:0;top:0;width:100%;height:100%;background:rgba(0,0,0,0.7);align-items:center;justify-content:center;z-index:9999;">
  <div style="background:#fff;padding:12px;max-width:90%;max-height:90%;overflow:auto;position:relative;">
    <button onclick="closePreview()" style="position:absolute;right:8px;top:8px;">Close</button>
    <div id="preview-content"></div>
  </div>
</div>

<script>
function showPreview(url){
  const ext = url.split('.').pop().toLowerCase();
  const container = document.getElementById('preview-content');
  container.innerHTML = '';
  if (['jpg','jpeg','png','gif'].includes(ext)){
    const img = document.createElement('img'); img.src = url; img.style.maxWidth='100%'; img.style.height='auto'; container.appendChild(img);
  } else if (ext === 'pdf'){
    const iframe = document.createElement('iframe'); iframe.src = url; iframe.style.width='100%'; iframe.style.height='80vh'; container.appendChild(iframe);
  } else {
    const a = document.createElement('a'); a.href = url; a.textContent = 'Open file'; a.target='_blank'; container.appendChild(a);
  }
  document.getElementById('preview-modal').style.display = 'flex';
}
function closePreview(){ document.getElementById('preview-modal').style.display = 'none'; }
</script>

<?php require __DIR__ . '/../../../includes/footer.php'; ?>
