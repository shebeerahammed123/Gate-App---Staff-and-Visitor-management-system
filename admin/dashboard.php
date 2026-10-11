<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}

require '../includes/db.php';
require '../includes/header.php'; // Include header
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
</head>
<body>

<div class="container py-4">
    <h2 class="mb-4">Admin Dashboard - Current People on Site</h2>

    <div class="text-end mb-3">
        <a href="logout.php" class="btn btn-danger">Logout</a>
    </div>

    <div class="row mb-4">
        <?php
        $staff_count = $pdo->query("SELECT COUNT(*) FROM staff_attendance WHERE clock_out IS NULL")->fetchColumn();
        $visitor_count = $pdo->query("SELECT COUNT(*) FROM visitors_log WHERE clock_out IS NULL")->fetchColumn();
        ?>
        <div class="col-md-6"><div class="card bg-secondary p-3"><h4>Staff On Site</h4><p class="display-6"><?= $staff_count ?></p></div></div>
        <div class="col-md-6"><div class="card bg-secondary p-3"><h4>Visitors On Site</h4><p class="display-6"><?= $visitor_count ?></p></div></div>
    </div>

    <!-- Filters -->
    <form method="GET" class="row mb-3 g-3">
        <div class="col-md-3"><input type="date" name="date_from" class="form-control" value="<?= $_GET['date_from'] ?? '' ?>"></div>
        <div class="col-md-3"><input type="date" name="date_to" class="form-control" value="<?= $_GET['date_to'] ?? '' ?>"></div>
        <div class="col-md-3">
            <select name="type_filter" class="form-control">
                <option value="">All</option>
                <option value="staff" <?= (isset($_GET['type_filter']) && $_GET['type_filter']=='staff')?'selected':'' ?>>Staff</option>
                <option value="visitor" <?= (isset($_GET['type_filter']) && $_GET['type_filter']=='visitor')?'selected':'' ?>>Visitor</option>
            </select>
        </div>
        <div class="col-md-3"><button type="submit" class="btn btn-primary w-100">Apply Filter</button></div>
    </form>

    <table id="peopleTable" class="table table-dark table-striped table-bordered">
        <thead>
            <tr>
                <th>Type</th><th>Name</th><th>Email / Company</th><th>Role / Reason</th><th>Vehicle</th><th>Clock In</th><th>Clock Out</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $date_from = $_GET['date_from'] ?? null;
            $date_to = $_GET['date_to'] ?? null;
            $type_filter = $_GET['type_filter'] ?? null;

            // Staff
            $staff_query = "SELECT s.name, s.email, s.role, a.clock_in, a.clock_out FROM staff s JOIN staff_attendance a ON s.id=a.staff_id WHERE 1";
            if($date_from) $staff_query .= " AND a.date >= '$date_from'";
            if($date_to) $staff_query .= " AND a.date <= '$date_to'";
            if($type_filter && $type_filter != 'staff') $staff_query .= " AND 0";
            $stmt = $pdo->query($staff_query);
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $data = htmlspecialchars(json_encode($row));
                echo "<tr class='clickable-row' data-type='staff' data-user='$data'>
                    <td>Staff</td><td>{$row['name']}</td><td>{$row['email']}</td><td>{$row['role']}</td><td>-</td><td>{$row['clock_in']}</td><td>{$row['clock_out']}</td></tr>";
            }

            // Visitors
            $visitor_query = "SELECT first_name, last_name, company, reason, vehicle_number, clock_in, clock_out, photo FROM visitors_log WHERE 1";
            if($date_from) $visitor_query .= " AND date >= '$date_from'";
            if($date_to) $visitor_query .= " AND date <= '$date_to'";
            if($type_filter && $type_filter != 'visitor') $visitor_query .= " AND 0";
            $stmt = $pdo->query($visitor_query);
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $row['photo_base64'] = $row['photo'] ? base64_encode($row['photo']) : null;
                $data = htmlspecialchars(json_encode($row));
                echo "<tr class='clickable-row' data-type='visitor' data-user='$data'>
                    <td>Visitor</td><td>{$row['first_name']} {$row['last_name']}</td><td>{$row['company']}</td><td>{$row['reason']}</td><td>{$row['vehicle_number']}</td><td>{$row['clock_in']}</td><td>{$row['clock_out']}</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <button id="exportCSV" class="btn btn-success mt-3">Export CSV</button>
    <button id="exportPDF" class="btn btn-danger mt-3">Export PDF</button>

</div>

<!-- Modal for details -->
<div class="modal fade" id="userDetailsModal" tabindex="-1" aria-labelledby="userDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">User Details</h5>
                <button type="button" class="btn btn-secondary btn-sm me-2" id="copyDetailsBtn">Copy Details</button>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <table class="table table-borderless"><tbody id="modalBodyContent"></tbody></table>
                <div id="visitorPhoto" class="text-center my-2"></div>
            </div>
        </div>
    </div>
</div>

<!-- Lightbox modal -->
<div class="modal fade" id="photoLightbox" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content bg-dark">
            <div class="modal-body text-center p-0">
                <img id="lightboxImage" src="" class="img-fluid" style="max-height:90vh;">
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function(){
    $('#peopleTable').DataTable({order:[[5,'desc']], pageLength:10});

    let visitorPhotos = [];
    $('.clickable-row[data-type="visitor"]').each(function(){
        let user = $(this).data('user');
        visitorPhotos.push(user.photo_base64 || null);
    });

    let currentPhotoIndex = 0;

    $('.clickable-row').on('click', function(){
        $('.clickable-row').removeClass('selected-row');
        $(this).addClass('selected-row');

        let user = $(this).data('user');
        let type = $(this).data('type');
        let content = '';
        let photoHtml = '';

        if(type === 'staff'){
            content+=`<tr><th>Name</th><td>${user.name}</td></tr>`;
            content+=`<tr><th>Email</th><td>${user.email}</td></tr>`;
            content+=`<tr><th>Role</th><td>${user.role}</td></tr>`;
            content+=`<tr><th>Clock In</th><td>${user.clock_in}</td></tr>`;
            content+=`<tr><th>Clock Out</th><td>${user.clock_out}</td></tr>`;
        } else if(type === 'visitor'){
            content+=`<tr><th>Name</th><td>${user.first_name} ${user.last_name}</td></tr>`;
            content+=`<tr><th>Company</th><td>${user.company}</td></tr>`;
            content+=`<tr><th>Reason</th><td>${user.reason}</td></tr>`;
            content+=`<tr><th>Vehicle</th><td>${user.vehicle_number}</td></tr>`;
            content+=`<tr><th>Clock In</th><td>${user.clock_in}</td></tr>`;
            content+=`<tr><th>Clock Out</th><td>${user.clock_out}</td></tr>`;

            // Show photo
            if(user.photo_base64) {
                photoHtml = `<img src
