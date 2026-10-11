<?php 
include 'includes/header.php'; 
include 'includes/db.php';

// Alias $pdo to $conn so existing code works
$conn = $pdo;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = $_POST['first_name'] ?? '';
    $last_name  = $_POST['last_name'] ?? '';
    $company    = $_POST['company'] ?? '';
    $reason     = $_POST['reason'] ?? null;
    $staff_name = $_POST['staff_name'] ?? null;
    $vehicle_number = $_POST['vehicle_number'] ?? null;
    $photo      = $_POST['photo'] ?? '';
    $clock_in   = date('Y-m-d H:i:s');
    $date       = date('Y-m-d');

    $reason_text = $staff_name ? "Visiting: $staff_name" : $reason;

    try {
        $stmt = $conn->prepare("INSERT INTO visitors_log 
            (first_name, last_name, company, reason, vehicle_number, photo, clock_in, date) 
            VALUES (:first_name, :last_name, :company, :reason, :vehicle_number, :photo, :clock_in, :date)");

        $stmt->execute([
            ':first_name' => $first_name,
            ':last_name'  => $last_name,
            ':company'    => $company,
            ':reason'     => $reason_text,
            ':vehicle_number' => $vehicle_number,
            ':photo'      => $photo,
            ':clock_in'   => $clock_in,
            ':date'       => $date
        ]);

        $message = "Visitor signed in successfully!";

    } catch (PDOException $e) {
        $message = "Database error: " . $e->getMessage();
    }
}
?>

<div class="container mt-5" style="max-width:600px;">
    <?php if(isset($message)) echo "<div class='alert alert-".(strpos($message,'successfully')!==false?'success':'danger')."'>$message</div>"; ?>
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white"><h2 class="mb-0">Visitor Sign-In</h2></div>
        <div class="card-body">
            <form method="post">
                <div class="row mb-2">
                    <div class="col"><input type="text" name="first_name" placeholder="First Name" class="form-control" required></div>
                    <div class="col"><input type="text" name="last_name" placeholder="Last Name" class="form-control" required></div>
                </div>
                <input type="text" name="company" placeholder="Company" class="form-control mb-2">

                <label class="form-label" style="color: white;">Are you visiting a staff member?</label>
                <div class="btn-group w-100 mb-2" role="group">
                    <input type="radio" class="btn-check" name="visiting_staff" id="visit_no" value="No" checked onchange="toggleVisitType()">
                    <label class="btn btn-outline-primary" for="visit_no">No</label>

                    <input type="radio" class="btn-check" name="visiting_staff" id="visit_yes" value="Yes" onchange="toggleVisitType()">
                    <label class="btn btn-outline-primary" for="visit_yes">Yes</label>
                </div>

                <input type="text" name="staff_name" id="staff_search" placeholder="Search staff" class="form-control mb-2" style="display:none;">
                <input type="text" name="reason" id="reason_div" placeholder="Reason for Visit" class="form-control mb-2">

                <label class="form-label" style="color: white;">Vehicle parked onsite?</label>
                <div class="btn-group w-100 mb-2" role="group">
                    <input type="radio" class="btn-check" name="vehicle" id="vehicle_no" value="No" checked onchange="toggleVehicle()">
                    <label class="btn btn-outline-primary" for="vehicle_no">No</label>

                    <input type="radio" class="btn-check" name="vehicle" id="vehicle_yes" value="Yes" onchange="toggleVehicle()">
                    <label class="btn btn-outline-primary" for="vehicle_yes">Yes</label>
                </div>

                <input type="text" name="vehicle_number" id="vehicle_div" placeholder="Vehicle Number" class="form-control mb-2" style="display:none;">

                <div class="border p-2 text-center mb-2">
                    <div id="my_camera" class="mb-2"></div>
                    <button type="button" onclick="take_snapshot()" class="btn btn-secondary btn-sm mb-2">Capture Photo</button>
                    <input type="hidden" name="photo" id="photo">
                    <div id="results"></div>
                </div>

                <button type="submit" class="btn btn-success w-100">Sign In</button>
            </form>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.26/webcam.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.26/webcam.min.js"></script>
<script>
window.onload = function() {
    Webcam.set({
        width: 320,
        height: 240,
        image_format: 'jpeg',
        jpeg_quality: 90
    });
    Webcam.attach('#my_camera');
}

function toggleVehicle(){ 
    document.getElementById('vehicle_div').style.display = document.querySelector('input[name="vehicle"]:checked').value==='Yes'?'block':'none'; 
}
function toggleVisitType(){ 
    const visit = document.querySelector('input[name="visiting_staff"]:checked').value;
    document.getElementById('staff_search').style.display = visit==='Yes'?'block':'none';
    document.getElementById('reason_div').style.display = visit==='No'?'block':'none';
}
function take_snapshot(){ 
    Webcam.snap(data_uri=>{
        document.getElementById('results').innerHTML='<img src="'+data_uri+'" class="img-fluid rounded"/>';
        document.getElementById('photo').value=data_uri; 
    }); 
}
</script>
<script>
function toggleVehicle(){ 
    document.getElementById('vehicle_div').style.display = document.querySelector('input[name="vehicle"]:checked').value==='Yes'?'block':'none'; 
}
function toggleVisitType(){ 
    const visit = document.querySelector('input[name="visiting_staff"]:checked').value;
    document.getElementById('staff_search').style.display = visit==='Yes'?'block':'none';
    document.getElementById('reason_div').style.display = visit==='No'?'block':'none';
}
Webcam.set({width:320,height:240,image_format:'jpeg',jpeg_quality:90});
Webcam.attach('#my_camera');
function take_snapshot(){ 
    Webcam.snap(data_uri=>{
        document.getElementById('results').innerHTML='<img src="'+data_uri+'" class="img-fluid rounded"/>';
        document.getElementById('photo').value=data_uri; 
    }); 
}
</script>

<?php include 'includes/footer.php'; ?>
