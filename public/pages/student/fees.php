<?php require __DIR__ . '/../../../includes/header.php';
if (!is_student_logged_in()){ header('Location:/login'); exit; }
if (!enforce_session_timeout()) { student_logout(); header('Location:/login'); exit; }
$stu = current_student();
$stmt = pdo()->prepare('SELECT * FROM fees WHERE student_id=? ORDER BY created_at DESC'); $stmt->execute([$stu['id']]); $fees = $stmt->fetchAll();
?>
<h2>Fees</h2>
<?php if(!$fees) echo '<div class="card">No fee records found.</div>'; ?>
<?php foreach($fees as $f): ?>
  <div class="card">
    <div>Amount: ₹<?php echo e($f['amount']); ?> — Status: <?php echo e($f['status']); ?></div>
    <div>Month: <?php echo e($f['month']); ?> | Paid on: <?php echo e($f['paid_on']); ?></div>
    <?php if($f['status']==='paid'): ?><a href="#">Download Receipt</a><?php else: ?>
      <div style="margin-top:10px;">
        <button class="btn" onclick="startRazorpay(<?php echo intval($f['id']); ?>)">Pay Online (Razorpay)</button>
      </div>
      <div style="margin-top:10px;">
        <form id="upi-form-<?php echo intval($f['id']); ?>" method="post" enctype="multipart/form-data" onsubmit="return submitUpi(event, <?php echo intval($f['id']); ?>)">
          <label>Or upload UPI screenshot and transaction id:</label><br>
          <input type="text" name="txn_id" placeholder="Transaction ID" required />
          <input type="file" name="screenshot" accept="image/*,application/pdf" required />
          <button class="btn" type="submit">Submit UPI Payment</button>
        </form>
        <div id="upi-msg-<?php echo intval($f['id']); ?>"></div>
      </div>
    <?php endif; ?>
  </div>
<?php endforeach; ?>
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
async function startRazorpay(feeId){
  try{
    const form = new FormData(); form.append('fee_id', feeId);
    const res = await fetch('/api/razorpay_create_order.php', { method:'POST', body: form });
    const data = await res.json();
    if (data.error) return alert('Error: '+data.error);
    const options = {
      key: data.key_id,
      amount: data.order.amount,
      currency: data.order.currency,
      name: 'Tuition Fees',
      order_id: data.order.id,
      handler: async function (response){
        // verify on server
        const vform = new FormData();
        vform.append('razorpay_order_id', response.razorpay_order_id);
        vform.append('razorpay_payment_id', response.razorpay_payment_id);
        vform.append('razorpay_signature', response.razorpay_signature);
        vform.append('fee_id', feeId);
        const vr = await fetch('/api/razorpay_verify.php',{method:'POST',body:vform});
        const vdata = await vr.json();
        if (vdata.ok) { alert('Payment successful'); location.reload(); } else { alert('Verification failed'); }
      }
    };
    const rzp = new Razorpay(options);
    rzp.open();
  } catch(err){ console.error(err); alert('Payment init failed'); }
}

async function submitUpi(e, feeId){
  e.preventDefault();
  const form = document.getElementById('upi-form-'+feeId);
  const fd = new FormData(form);
  fd.append('fee_id', feeId);
  const res = await fetch('/api/upload_upi_payment.php', { method:'POST', body: fd });
  const data = await res.json();
  const msg = document.getElementById('upi-msg-'+feeId);
  if (data.ok) { msg.textContent = 'UPI payment submitted and pending verification.'; form.reset(); } else { msg.textContent = 'Error: '+(data.message||data.error); }
  return false;
}
</script>
<?php require __DIR__ . '/../../../includes/footer.php'; ?>
