<?php include 'includes/header.php'; include 'includes/db.php';
$id = $_GET['id'] ?? ($_GET['auto_id'] ?? null);
if (!$id) die("No staff selected");
$stmt = $pdo->prepare("SELECT * FROM staff WHERE id=?");
$stmt->execute([$id]);
$staff = $stmt->fetch();


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
if (isset($_POST['clock_in'])) {
$pdo->prepare("INSERT INTO staff_attendance (staff_id, clock_in, date) VALUES (?, NOW(), CURDATE())")->execute([$id]);
}
if (isset($_POST['clock_out'])) {
$pdo->prepare("UPDATE staff_attendance SET clock_out=NOW() WHERE staff_id=? AND clock_out IS NULL")->execute([$id]);
}
}
$check = $pdo->prepare("SELECT * FROM staff_attendance WHERE staff_id=? AND clock_out IS NULL");
$check->execute([$id]);
$active = $check->fetch();
?>
<h2><?php echo $staff['name']; ?> (<?php echo $staff['role']; ?>)</h2>
<form method="post">
<button type="submit" name="clock_in" class="btn btn-success" <?php if($active) echo 'disabled'; ?>>Clock In</button>
<button type="submit" name="clock_out" class="btn btn-danger" <?php if(!$active) echo 'disabled'; ?>>Clock Out</button>
</form>
<hr>
<h4>Or Scan QR Badge:</h4>
<div id="reader" style="width:300px;"></div>
<script>
function onScanSuccess(decodedText) {
if(decodedText.startsWith("STAFF:")) {
let id = decodedText.split(":")[1];
window.location.href = "staff_clock.php?auto_id=" + id;
}
}
new Html5QrcodeScanner("reader", { fps: 10, qrbox: 250 }).render(onScanSuccess);
</script>
<?php include 'includes/footer.php'; ?>