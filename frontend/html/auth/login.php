<?php
// Start PHP session to load stored login state
session_start();
// Include the global security check filter
require_once __DIR__ . "/global_security.php";
// Check if user session variable is already set
if (isset($_SESSION["user_id"])) {
    // Redirect already logged-in user to the main dashboard
    header("Location: ../dashboard/index.php");
    // Terminate script execution immediately
    exit;
}
?>
<!-- Declare standard HTML5 document type -->
<!doctype html>
<!-- Start main html block with English language specification -->
<html lang="en">
<!-- Start document head block -->
<head>
  <!-- Set encoding scheme to UTF-8 -->
  <meta charset="utf-8">
  <!-- Define responsive design viewport scaling settings -->
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Set page title string -->
  <title>Security Threat Dashboard | Login</title>
  <!-- Include global stylesheet resource -->
  <link rel="stylesheet" href="../../css/style.css">
<!-- End document head block -->
</head>
<!-- Start document body block -->
<body>
  <!-- Define main container shell for auth view -->
  <main class="auth-shell">
    <!-- Define card section representing the login UI -->
    <section class="auth-card">
      <!-- Create container for branding and title metadata -->
      <div class="auth-brand">
        <!-- Render application eyebrow slogan -->
        <p class="eyebrow">Security Threat Dashboard</p>
        <!-- Render page main title header -->
        <h1>Administrator Sign In</h1>
        <!-- Render muted description text -->
        <p class="muted">Access logs, threat reports, and real-time alerts from one secure place.</p>
      <!-- Close branding container -->
      </div>
      <!-- Define form block with form handler ID and browser validation disabled -->
      <form id="loginForm" class="auth-form" autocomplete="on" novalidate>
        <!-- Declare input field label wrapper -->
        <label class="field">
          <!-- Render text span for field label -->
          <span>Username or email</span>
          <!-- Render text input field with auto-completion and placeholder text -->
          <input type="text" name="username" id="username" required autocomplete="username" placeholder="Enter username or email">
        <!-- Close label wrapper -->
        </label>
        <!-- Declare password input field label wrapper -->
        <label class="field">
          <!-- Render text span for field label -->
          <span>Password</span>
          <!-- Render password input field with auto-completion and placeholder text -->
          <input type="password" name="password" id="password" required autocomplete="current-password" placeholder="Enter password">
        <!-- Close label wrapper -->
        </label>
        <!-- Declare checkbox row wrapper -->
        <label class="checkbox-row">
          <!-- Render checkbox input control element -->
          <input type="checkbox" id="rememberMe" name="rememberMe">
          <!-- Render text span for checkbox label -->
          <span>Remember me</span>
        <!-- Close checkbox row wrapper -->
        </label>
        <!-- Render primary form submission button -->
        <button type="submit" class="primary-btn">Login</button>
        <!-- Create paragraph element to print notification responses -->
        <p id="loginMessage" class="message" aria-live="polite"></p>
      <!-- Close form element -->
      </form>
      <!-- Create container for helper links -->
      <div class="auth-links">
        <!-- Link anchor routing to forgot password screen -->
        <a href="forgot_password.php">Forgot password?</a>
        <!-- Link anchor routing to registration screen -->
        <a href="register.php">Create an account</a>
      <!-- Close helper links container -->
      </div>
    <!-- Close card section -->
    </section>
  <!-- Close main shell container -->
  </main>
  <!-- Load deferring login handler script file -->
  <script src="../../js/auth/login.js" defer></script>
<!-- End document body block -->
</body>
<!-- End html tag block -->
</html>
