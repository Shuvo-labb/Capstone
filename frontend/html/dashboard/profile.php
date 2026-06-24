<?php
require_once __DIR__ . "/../auth/require_login.php";
require_once __DIR__ . "/../auth/global_security.php";
$active_page = 'profile.php';
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Security Threat Dashboard — User Profile</title>
  <link rel="stylesheet" href="../../css/style.css">
</head>
<body>
  <main class="dashboard-shell">
    <div class="dashboard-layout">
      <?php require_once __DIR__ . "/sidebar.php"; ?>

      <div class="main-content">
        <div class="topbar">
          <div>
            <h2 style="margin:0 0 6px 0">User Profile</h2>
            <p class="muted" style="margin:0">View and manage your profile information.</p>
          </div>
        </div>

        <section class="section">
          <h3 style="margin-top:0">Profile Details</h3>
          <div style="display:grid;gap:16px;max-width:520px">
            <div class="stat-card">
              <div class="muted">Username</div>
              <div class="stat-value"><?php echo htmlspecialchars($_SESSION["username"] ?? "Unknown"); ?></div>
            </div>
            <div class="stat-card">
              <div class="muted">User ID</div>
              <div class="stat-value"><?php echo htmlspecialchars($_SESSION["user_id"] ?? "Unknown"); ?></div>
            </div>
            <div class="stat-card">
              <div class="muted">Account Status</div>
              <div class="stat-value" style="color:#4ade80">Active</div>
            </div>
          </div>
          <div style="margin-top:16px">
            <a href="change_password.php" class="primary-btn">Change Password</a>
          </div>
        </section>
      </div> <!-- /.main-content -->
    </div> <!-- /.dashboard-layout -->
  </main>
</body>
</html>