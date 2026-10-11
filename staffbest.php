<?php include 'includes/header.php'; include 'includes/db.php'; ?>
<h2>Select Staff</h2>
<div class="list-group">
<?php
$stmt = $pdo->query("SELECT * FROM staff");
while($row = $stmt->fetch()) {
echo "<a href='staff_clock.php?id={$row['id']}' class='list-group-item list-group-item-action bg-dark text-light'>{$row['name']} ({$row['role']})</a>";
}
?>
</div>
<?php include 'includes/footer.php'; ?>