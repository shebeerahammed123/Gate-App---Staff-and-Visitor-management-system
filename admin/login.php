<?php
session_start();
require '../includes/db.php';
require '../includes/header.php';

// Handle login submission
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM admins WHERE username=?");
    $stmt->execute([$username]);
    $admin = $stmt->fetch();

    if ($admin) {
        // For production, you should hash passwords using password_hash()
        if ($password === $admin['password']) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_username'] = $admin['username'];
            header("Location: dashboard.php");
            exit;
        } else {
            $message = "Incorrect password.";
        }
    } else {
        $message = "Admin user not found.";
    }
}
?>

<div class="row justify-content-center">
    <div class="col-md-4">
        <div class="card bg-dark text-light p-4">
            <h3 class="text-center mb-3">Administrator Login</h3>

            <?php if($message): ?>
                <div class="alert alert-danger"><?php echo $message; ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control bg-secondary text-light" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control bg-secondary text-light" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Sign In</button>
            </form>
        </div>
    </div>
</div>

<?php require '../includes/footer.php'; ?>