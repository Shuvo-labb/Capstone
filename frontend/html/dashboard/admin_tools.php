<?php
// Programmer Name : MD ABU SAYED SHUVO
// Program Name    : admin_tools.php
// Description     : Dashboard page
// First Commit Date: Friday,19-Jun-2026
// Last Commit Date : Friday,19-Jun-2026
// Admin Tools
// This page will contain administrative utilities.
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Security Threat Dashboard Ã¢â‚¬â€ Admin Tools</title>
  <link rel="stylesheet" href="../../css/style.css">
</head>
<body>
  <main class="dashboard-shell">
    <div class="dashboard-layout">
      <aside class="sidebar" aria-label="Primary navigation">
        <div class="sidebar-brand">
          <p class="eyebrow">Security Threat Dashboard</p>
        </div>
        <nav class="nav-links">
          <a href="index.php">Main Dashboard</a>
          <a href="threat_overview.php">Threat Overview</a>
          <a href="real_time_alerts.php">Real-Time Alerts</a>
          <a href="activity_logs.php">Activity Logs</a>
          <a href="suspicious_ips.php">Suspicious IPs</a>
          <a href="failed_logins.php">Failed Login Attempts</a>
          <hr>
          <a href="admin_tools.php">Admin Tools</a>
          <a href="system_settings.php">System Settings</a>
          <a href="profile.php">Profile</a>
          <a href="change_password.php">Change Password</a>
          <a href="audit_trail.php">Audit Trail</a>
        </nav>
      </aside>

      <div class="main-content">
        <div class="topbar">
          <div>
            <h2 style="margin:0 0 6px 0">Admin Tools</h2>
            <p class="muted" style="margin:0">Manage system-wide settings and administrative functions.</p>
          </div>
        </div>

        <section class="section">
          <h3 style="margin-top:0">Administrative Functions</h3>
          <p>Content for Admin Tools will go here.</p>
        </section>
      </div> <!-- /.main-content -->
    </div> <!-- /.dashboard-layout -->
  </main>
</body>
</html>
