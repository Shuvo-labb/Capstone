<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Security Threat Dashboard | Login</title>
  <link rel="stylesheet" href="../../css/style.css">
</head>
<body>
  <main class="auth-shell">
    <section class="auth-card">
      <div class="auth-brand">
        <p class="eyebrow">Security Threat Dashboard</p>
        <h1>Administrator Sign In</h1>
        <p class="muted">Access logs, threat reports, and real-time alerts from one secure place.</p>
      </div>

      <form id="loginForm" class="auth-form" autocomplete="on" novalidate>
        <label class="field">
          <span>Username</span>
          <input type="text" name="username" id="username" required autocomplete="username" placeholder="Enter username">
        </label>

        <label class="field">
          <span>Password</span>
          <input type="password" name="password" id="password" required autocomplete="current-password" placeholder="Enter password">
        </label>

        <label class="checkbox-row">
          <input type="checkbox" id="rememberMe" name="rememberMe">
          <span>Remember me</span>
        </label>

        <button type="submit" class="primary-btn">Login</button>
        <p id="loginMessage" class="message" aria-live="polite"></p>
      </form>

      <div class="auth-links">
        <a href="forgot_password.php">Forgot password?</a>
        <a href="logout.php">Logout</a>
      </div>
    </section>
  </main>

  <script src="../../js/auth/login.js" defer></script>
</body>
</html>