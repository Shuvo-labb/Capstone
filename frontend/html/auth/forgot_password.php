<?php
// Programmer Name : MD ABU SAYED SHUVO
// Program Name    : forgot_password.php
// Description     : Authentication page or handler
// First Commit Date: Monday,25-May-2026
// Last Commit Date : Friday,19-Jun-2026
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
  <title>Forgot Password â€” Security Threat Dashboard</title>
  <link rel="stylesheet" href="../../css/style.css">
</head>
<body>
  <main class="auth-shell">
    <section class="auth-card">
      <div class="auth-brand">
        <p class="eyebrow">Security Threat Dashboard</p>
        <h1>Forgot Password</h1>
        <p class="muted">Enter your account email to receive a password reset link.</p>
      </div>

      <form id="forgotForm" class="auth-form" novalidate>
        <label class="field">
          <span>Email address</span>
          <input type="email" name="email" id="email" required placeholder="you@example.com" autocomplete="email">
        </label>

        <button type="submit" class="primary-btn">Send reset link</button>
        <p id="forgotMessage" class="message" aria-live="polite"></p>
        <p id="forgotResetLink" class="message" hidden></p>
      </form>

      <div class="auth-links">
        <a href="login.php">Back to login</a>
      </div>
    </section>
  </main>

  <script src="../../js/auth/forgot_password.js" defer></script>
</body>
</html>
