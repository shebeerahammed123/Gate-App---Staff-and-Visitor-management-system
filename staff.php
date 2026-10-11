<?php 
include 'includes/header.php'; 
include 'includes/db.php'; 
?>

<h2>Select Staff</h2>

<!-- Search box -->
<input type="text" id="staffSearch" placeholder="Search staff..." class="form-control mb-3">

<!-- Staff grid -->
<div class="staff-grid">
<?php
$stmt = $pdo->query("SELECT * FROM staff");
while($row = $stmt->fetch()) {
    echo "<a href='staff_clock.php?id={$row['id']}' class='staff-btn' data-name='{$row['name']}'>
            {$row['name']} ({$row['role']})
          </a>";
}
?>
</div>

<style>
/* Grid layout: max 4 per row */
.staff-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
    gap: 10px;
}

/* Staff button styling */
.staff-btn {
    display: block;
    padding: 10px;
    background-color: #343a40;
    color: #fff;
    text-align: center;
    text-decoration: none;
    border-radius: 5px;
    transition: 0.2s;
}

.staff-btn:hover {
    background-color: #495057;
}
</style>

<script>
// Simple JS search
const searchInput = document.getElementById('staffSearch');
searchInput.addEventListener('keyup', function() {
    const filter = searchInput.value.toLowerCase();
    document.querySelectorAll('.staff-btn').forEach(btn => {
        const name = btn.dataset.name.toLowerCase();
        btn.style.display = name.includes(filter) ? 'block' : 'none';
    });
});
</script>

<?php include 'includes/footer.php'; ?>
