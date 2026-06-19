<?php
session_start();

if (isset($_SESSION["user_id"])) {
    header("Location: ../dashboard/index.php");
    exit;
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Reset Password — Security Threat Dashboard</title>
  <link rel="stylesheet" href="../../css/style.css">
</head>
<body>
  <main class="auth-shell">
    <section class="auth-card">
      <div class="auth-brand">
        <p class="eyebrow">Security Threat Dashboard</p>
        <h1>Reset Password</h1>
        <p class="muted">Set a new password for your account using your reset link.</p>
      </div>

      <form id="resetForm" class="auth-form" novalidate>
        <input type="hidden" name="token" id="resetToken">

        <label class="field">
          <span>New password</span>
          <input type="password" name="password" id="password" required placeholder="New password" autocomplete="new-password">
        </label>

        <label class="field">
          <span>Confirm password</span>
          <input type="password" name="confirm_password" id="confirm_password" required placeholder="Confirm password" autocomplete="new-password">
        </label>

        <button type="submit" class="primary-btn">Reset password</button>
        <p id="resetMessage" class="message" aria-live="polite"></p>
      </form>

      <div class="auth-links">
        <a href="login.php">Back to login</a>
      </div>
    </section>
  </main>

  <script src="../../js/auth/reset_password.js" defer></script>
</body>
</html>
