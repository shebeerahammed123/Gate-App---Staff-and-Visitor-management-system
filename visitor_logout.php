<?php
include 'includes/header.php'; 
include 'includes/db.php';

$success = false;

// Handle clock out
if(isset($_POST['clockout_id'])) {
    $id = (int)$_POST['clockout_id'];
    $now = date('Y-m-d H:i:s');
    $stmt = $pdo->prepare("UPDATE visitors_log SET clock_out = :now WHERE id = :id");
    $stmt->execute(['now' => $now, 'id' => $id]);
    $success = true;
}

// Handle search
$search = isset($_GET['q']) ? $_GET['q'] : '';
$searchParam = "%$search%";

function getVisitors($pdo, $searchParam){
    $stmt = $pdo->prepare("SELECT * FROM visitors_log 
                       WHERE clock_out IS NULL 
                       AND (first_name LIKE :s OR last_name LIKE :s OR company LIKE :s)
                       ORDER BY clock_in DESC");
    $stmt->execute(['s' => $searchParam]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$visitors = getVisitors($pdo, $searchParam);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Clock Out Visitors</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body {
    background-color: #121212;
    color: #e0e0e0;
}
.visitor-list { display: flex; flex-wrap: wrap; gap: 10px; }
.visitor { border: 1px solid #333; padding: 10px; width: 23%; box-sizing: border-box; border-radius: 5px; background-color: #1d1d1d; }
.visitor img { width: 100%; height: auto; border-radius: 5px; }
.visitor button { background-color: red; color: white; border: none; padding: 8px; cursor: pointer; border-radius: 3px; margin-top: 5px; }
.modal-content { text-align: center; }
.alert-success {
    background-color: #28a745;
    color: white;
}
</style>
</head>
<body class="container mt-4">

<h2>Clock Out Visitors</h2>

<?php if($success): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
  Visitor clocked out successfully!
  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<script>
setTimeout(function() {
    window.location.href = 'index.php';
}, 4000);
</script>
<?php endif; ?>

<div class="search mb-3">
  <form method="GET" class="d-flex">
    <input type="text" name="q" placeholder="Search visitors..." value="<?php echo htmlspecialchars($search); ?>" class="form-control me-2">
    <button type="submit" class="btn btn-primary">Search</button>
  </form>
</div>

<div class="visitor-list" id="visitorList">
<?php foreach($visitors as $v): ?>
  <div class="visitor">
    <?php if($v['photo']): ?>
      <img src="<?php echo htmlspecialchars($v['photo']); ?>" alt="">
    <?php else: ?>
      <img src="placeholder.jpg" alt="">
    <?php endif; ?>
    <p><b><?php echo htmlspecialchars($v['first_name'].' '.$v['last_name']); ?></b></p>
    <p><?php echo htmlspecialchars($v['company']); ?></p>
    <p><?php echo htmlspecialchars($v['reason']); ?></p>
    <button class="btn btn-danger btn-sm" onclick="openModal(<?php echo $v['id']; ?>)">Clock Out</button>
  </div>
<?php endforeach; ?>
</div>

<!-- Modal -->
<div class="modal fade" id="clockOutModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content p-3">
      <p>Are you sure you want to clock out this visitor?</p>
      <form method="POST" id="clockOutForm">
        <input type="hidden" name="clockout_id" id="clockout_id">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-danger">Clock Out</button>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
let clockOutModal = new bootstrap.Modal(document.getElementById('clockOutModal'));
function openModal(id){
    document.getElementById('clockout_id').value = id;
    clockOutModal.show();
}

// Auto-refresh visitor list every 30 seconds
setInterval(() => {
    fetch(window.location.href + '&ajax=1')
    .then(res => res.text())
    .then(html => {
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        const newList = doc.getElementById('visitorList');
        if(newList) document.getElementById('visitorList').innerHTML = newList.innerHTML;
    });
}, 30000);
</script>

<?php
// Handle AJAX refresh
if(isset($_GET['ajax'])){
    foreach(getVisitors($pdo, $searchParam) as $v){
        echo '<div class="visitor">';
        echo '<img src="'.($v['photo'] ?: 'placeholder.jpg').'" alt="">';
        echo '<p><b>'.htmlspecialchars($v['first_name'].' '.$v['last_name']).'</b></p>';
        echo '<p>'.htmlspecialchars($v['company']).'</p>';
        echo '<p>'.htmlspecialchars($v['reason']).'</p>';
        echo '<button class="btn btn-danger btn-sm" onclick="openModal('.$v['id'].')">Clock Out</button>';
        echo '</div>';
    }
    exit;
}
?>

</body>
</html>
