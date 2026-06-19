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
  <title>Security Threat Dashboard | Register</title>
  <link rel="stylesheet" href="../../css/style.css">
</head>
<body>
  <main class="auth-shell">
    <section class="auth-card">
      <div class="auth-brand">
        <p class="eyebrow">Security Threat Dashboard</p>
        <h1>Register New Account</h1>
        <p class="muted">Create your account to access the security threat dashboard.</p>
      </div>

      <form id="registerForm" class="auth-form" autocomplete="on" novalidate>
        <label class="field">
          <span>Username</span>
          <input type="text" name="username" id="username" required autocomplete="username" placeholder="Enter desired username">
        </label>

        <label class="field">
          <span>Email address</span>
          <input type="email" name="email" id="email" required placeholder="you@example.com" autocomplete="email">
        </label>

        <label class="field">
          <span>Password</span>
          <input type="password" name="password" id="password" required autocomplete="new-password" placeholder="Enter password">
        </label>

        <label class="field">
          <span>Confirm Password</span>
          <input type="password" name="confirm_password" id="confirm_password" required autocomplete="new-password" placeholder="Confirm password">
        </label>

        <button type="submit" class="primary-btn">Register</button>
        <p id="registerMessage" class="message" aria-live="polite"></p>
      </form>

      <div class="auth-links">
        <a href="login.php">Already have an account? Login</a>
      </div>
    </section>
  </main>

  <script src="../../js/auth/register.js" defer></script>
</body>
</html>
