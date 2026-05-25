<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Security Threat Dashboard | Logout</title>
  <link rel="stylesheet" href="../../css/style.css">
</head>
<body>
  <main class="auth-shell">
    <section class="auth-card auth-status-card">
      <p class="eyebrow">Security Threat Dashboard</p>
      <h1>Signing you out</h1>
      <p class="muted">Your session will be cleared and you will be returned to the login page.</p>
      <button id="logoutButton" class="primary-btn" type="button">Confirm Logout</button>
      <p id="logoutMessage" class="message" aria-live="polite"></p>
      <p class="auth-links single-link"><a href="login.php">Back to login</a></p>
    </section>
  </main>

  <script src="../../js/auth/logout.js" defer></script>
</body>
</html>