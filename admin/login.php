<?php
// admin/login.php
session_start();
require_once __DIR__ . "/../config.php";

function h($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

if (!empty($_SESSION['admin_id'])) {
  header("Location: dashboard.php");
  exit;
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = trim($_POST['username'] ?? '');
  $password = trim($_POST['password'] ?? '');

  if ($username === '' || $password === '') {
    $error = "Please enter your username and password.";
  } else {
    $stmt = $mysqli->prepare("
      SELECT admin_id, password, full_name, role, branch_id, is_active
      FROM admin_users
      WHERE username=?
      LIMIT 1
    ");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$row || !$row['is_active'] || $row['password'] !== $password) {
      $error = "Invalid credentials.";
    } else {
      session_regenerate_id(true);
      $_SESSION['admin_id'] = $row['admin_id'];
      $_SESSION['admin_name'] = $row['full_name'];
      $_SESSION['admin_role'] = $row['role'];
      $_SESSION['admin_branch_id'] = $row['branch_id'];
      header("Location: dashboard.php");
      exit;
    }
  }
}

$page_title = "Admin Login";
include __DIR__ . "/includes/admin-head.php";
?>

<div class="container py-5" style="max-width:960px;">
  <div class="text-center mb-4">
    <h1 class="fw-bold">DON MACCHIATOS</h1>
    <div class="text-white-50 text-uppercase small">
      Staff & Management Access
    </div>
  </div>

  <div class="row justify-content-center">
    <div class="col-lg-6">
      <div class="panel-card rounded-4 p-4 p-lg-5">

        <h3 class="fw-bold mb-1 text-center">Admin Login ☕</h3>
        <div class="text-white-50 text-center small mb-4">
          Sign in to manage orders and update customer tracking.
        </div>

        <?php if ($error): ?>
          <div class="alert alert-danger py-2 small">
            <i class="fa-solid fa-circle-exclamation me-1"></i><?= h($error) ?>
          </div>
        <?php endif; ?>

        <form method="post">
          <div class="mb-3">
            <label class="form-label small text-white-50">Username</label>
            <input class="form-control" name="username" required>
          </div>

          <div class="mb-3">
            <label class="form-label small text-white-50">Password</label>
            <input class="form-control" type="password" name="password" required>
          </div>

          <button class="btn btn-primary w-100 fw-bold">
            <i class="fa-solid fa-right-to-bracket me-2"></i>Login
          </button>

          <div class="text-white-50 small mt-3 text-center">
            Same system. Same coffee. Different role.
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . "/includes/admin-footer.php"; ?>
