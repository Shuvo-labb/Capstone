<?php
require_once __DIR__ . "/../auth/require_login.php";
require_once __DIR__ . "/../auth/global_security.php";
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Upload Log — Security Threat Dashboard</title>
  <link rel="stylesheet" href="../../css/style.css">
</head>
<body>
  <main class="dashboard-shell">
    <div class="dashboard-layout">
      <aside class="sidebar" aria-label="Primary navigation">
        <div class="sidebar-brand"><p class="eyebrow">Security Threat Dashboard</p></div>
        <nav class="nav-links">
          <a href="../auth/logout.php">Logout</a>

          <hr>
          <a href="index.php">Main Dashboard</a>
          <a href="threat_overview.php">Threat Overview</a>
          <a href="real_time_alerts.php">Real-Time Alerts</a>
          <a href="statistics.php">Statistics</a>
          <a href="activity_logs.php">Activity Logs</a>
          <a href="suspicious_ips.php">Suspicious IPs</a>
          <a href="failed_logins.php">Failed Login Attempts</a>

          <hr>
          <a href="upload_log.php" class="active">Log Upload</a>
          <a href="parser_results.php">Parser Results</a>
          <a href="threat_analysis.php">Threat Analysis</a>
          <a href="detailed_threat_report.php">Detailed Threat Report</a>
          <a href="sql_injection_attempts.php">SQL Injection Attempts</a>
          <a href="xss_attempts.php">XSS Attempts</a>
          <a href="malware_attempts.php">Malware Upload Attempts</a>

          <hr>
          <a href="export_reports.php">Export Reports</a>
          <a href="daily_report.php">Daily Report</a>
          <a href="weekly_report.php">Weekly Report</a>
          <a href="monthly_report.php">Monthly Report</a>
          <a href="custom_report_builder.php">Custom Report Builder</a>

          <hr>
          <a href="system_settings.php">System Settings</a>
          <a href="profile.php">Profile</a>
          <a href="change_password.php">Change Password</a>
          <a href="audit_trail.php">Audit Trail</a>
        </nav>
      </aside>

      <div class="main-content">
        <div class="topbar">
          <div>
            <h2 style="margin:0 0 6px 0">Upload Log File</h2>
            <p class="muted" style="margin:0">Upload an Apache access log (.txt or .log). The parser will detect SQL injection, XSS, and brute force attacks.</p>
          </div>
          <div class="actions">
            <a class="small-btn" href="index.php">Back to Dashboard</a>
          </div>
        </div>

        <section class="section" style="max-width:520px">
          <form id="uploadForm" enctype="multipart/form-data">
            <label class="field">
              <span>Log file</span>
              <input type="file" name="log_file" id="log_file" accept=".txt,.log" required>
            </label>
            <button type="submit" class="primary-btn">Upload and Parse</button>
            <p id="uploadMessage" class="message" aria-live="polite"></p>
          </form>
        </section>
      </div>
    </div>
  </main>

  <script>
    document.getElementById("uploadForm").addEventListener("submit", async (e) => {
      e.preventDefault();
      const msg = document.getElementById("uploadMessage");
      msg.textContent = "Uploading and parsing...";
      msg.style.color = "";

      try {
        const res = await fetch("handle_upload.php", { method: "POST", body: new FormData(e.target) });
        const data = await res.json();
        msg.textContent = data.message;
        msg.style.color = data.success ? "green" : "red";
        if (data.success) e.target.reset();
      } catch (err) {
        msg.textContent = "Upload failed. Please try again.";
        msg.style.color = "red";
      }
    });
  </script>
</body>
</html>
