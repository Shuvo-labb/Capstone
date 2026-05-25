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
        <p class="muted">Set a new password for your account. The reset token is required.</p>
      </div>

      <form id="resetForm" class="auth-form" novalidate>
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
