<?php
// Programmer Name : VISHVAN VARMA A/L SIVA KUMAR
// Program Name    : change_password.php
// Description     : Dashboard page
// First Commit Date: Friday,19-Jun-2026
// Last Commit Date : Wednesday,24-Jun-2026
require_once __DIR__ . "/../auth/require_login.php";
require_once __DIR__ . "/../auth/global_security.php";
$active_page = 'change_password.php';
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Security Threat Dashboard â€” Change Password</title>
  <link rel="stylesheet" href="../../css/style.css">
</head>
<body>
  <main class="dashboard-shell">
    <div class="dashboard-layout">
      <?php require_once __DIR__ . "/sidebar.php"; ?>

      <div class="main-content">
        <div class="topbar">
          <div>
            <h2 style="margin:0 0 6px 0">Change Password</h2>
            <p class="muted" style="margin:0">Update your account password.</p>
          </div>
        </div>

        <section class="section" style="max-width:520px">
          <h3 style="margin-top:0">Change Password</h3>
          <form id="passwordForm">
            <label class="field">
              <span>Current Password</span>
              <input type="password" name="current_password" id="currentPassword" required>
            </label>
            <label class="field">
              <span>New Password</span>
              <input type="password" name="new_password" id="newPassword" required minlength="8">
            </label>
            <label class="field">
              <span>Confirm New Password</span>
              <input type="password" name="confirm_password" id="confirmPassword" required minlength="8">
            </label>
            <button type="submit" class="primary-btn">Update Password</button>
            <p id="passwordMessage" class="message" aria-live="polite"></p>
          </form>
        </section>

        <script>
          document.getElementById('passwordForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const msg = document.getElementById('passwordMessage');
            const current = document.getElementById('currentPassword').value;
            const newPass = document.getElementById('newPassword').value;
            const confirm = document.getElementById('confirmPassword').value;

            if (newPass !== confirm) {
              msg.textContent = 'Passwords do not match.';
              msg.style.color = 'red';
              return;
            }

            msg.textContent = 'Updating password...';
            msg.style.color = '';

            try {
              const res = await fetch('api/change_password.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({current_password: current, new_password: newPass})
              });
              const data = await res.json();
              msg.textContent = data.message;
              msg.style.color = data.success ? 'green' : 'red';
              if (data.success) e.target.reset();
            } catch (err) {
              console.error('Password change failed', err);
              msg.textContent = 'Failed to update password. Please try again.';
              msg.style.color = 'red';
            }
          });
        </script>
      </div> <!-- /.main-content -->
    </div> <!-- /.dashboard-layout -->
  </main>
</body>
</html>
