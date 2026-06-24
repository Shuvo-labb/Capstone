<?php
// Start PHP session to load stored login state
session_start();
// Check if user session variable is already set
if (isset($_SESSION["user_id"])) {
    // Redirect logged-in user to the main dashboard
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
  <title>Reset Password — Security Threat Dashboard</title>
  <!-- Include global stylesheet resource -->
  <link rel="stylesheet" href="../../css/style.css">
<!-- End document head block -->
</head>
<!-- Start document body block -->
<body>
  <!-- Define main container shell for auth view -->
  <main class="auth-shell">
    <!-- Define card section representing the reset password UI -->
    <section class="auth-card">
      <!-- Create container for branding and title metadata -->
      <div class="auth-brand">
        <!-- Render application eyebrow slogan -->
        <p class="eyebrow">Security Threat Dashboard</p>
        <!-- Render page main title header -->
        <h1>Reset Password</h1>
        <!-- Render muted description text -->
        <p class="muted">Set a new password for your account using your reset link.</p>
      <!-- Close branding container -->
      </div>
      <!-- Define form block with form handler ID and browser validation disabled -->
      <form id="resetForm" class="auth-form" novalidate>
        <!-- Render hidden input field to store the reset token -->
        <input type="hidden" name="token" id="resetToken">
        <!-- Declare input field label wrapper for new password -->
        <label class="field">
          <!-- Render text span for field label -->
          <span>New password</span>
          <!-- Render password input field with auto-completion and placeholder text -->
          <input type="password" name="password" id="password" required placeholder="New password" autocomplete="new-password">
        <!-- Close label wrapper -->
        </label>
        <!-- Declare input field label wrapper for confirming new password -->
        <label class="field">
          <!-- Render text span for field label -->
          <span>Confirm password</span>
          <!-- Render confirm password input field with auto-completion and placeholder text -->
          <input type="password" name="confirm_password" id="confirm_password" required placeholder="Confirm password" autocomplete="new-password">
        <!-- Close label wrapper -->
        </label>
        <!-- Render primary form submission button -->
        <button type="submit" class="primary-btn">Reset password</button>
        <!-- Create paragraph element to print notification responses -->
        <p id="resetMessage" class="message" aria-live="polite"></p>
      <!-- Close form element -->
      </form>
      <!-- Create container for helper links -->
      <div class="auth-links">
        <!-- Link anchor routing back to login screen -->
        <a href="login.php">Back to login</a>
      <!-- Close helper links container -->
      </div>
    <!-- Close card section -->
    </section>
  <!-- Close main shell container -->
  </main>
  <!-- Load deferring reset_password handler script file -->
  <script src="../../js/auth/reset_password.js" defer></script>
<!-- End document body block -->
</body>
<!-- End html tag block -->
</html>
