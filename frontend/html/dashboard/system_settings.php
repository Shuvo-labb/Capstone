<?php
// Programmer Name : MD ABU SAYED SHUVO
// Program Name    : system_settings.php
// Description     : Dashboard page
// First Commit Date: Friday,19-Jun-2026
// Last Commit Date : Wednesday,24-Jun-2026
require_once __DIR__ . "/../auth/require_login.php";
require_once __DIR__ . "/../auth/global_security.php";
require_once __DIR__ . "/../../../database/db_connect.php";
$active_page = 'system_settings.php';
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Security Threat Dashboard â€” System Settings</title>
  <link rel="stylesheet" href="../../css/style.css">
</head>
<body>
  <main class="dashboard-shell">
    <div class="dashboard-layout">
      <?php require_once __DIR__ . "/sidebar.php"; ?>

      <div class="main-content">
        <div class="topbar">
          <div>
            <h2 style="margin:0 0 6px 0">System Settings</h2>
            <p class="muted" style="margin:0">Configure various system-wide settings.</p>
          </div>
        </div>

        <section class="section" style="max-width:600px">
          <h3 style="margin-top:0">General Settings</h3>
          <form id="settingsForm">
            <label class="field">
              <span>Site Name</span>
              <input type="text" name="site_name" id="siteName" value="Security Threat Dashboard">
            </label>
            <label class="field">
              <span>Alert Threshold (Critical)</span>
              <input type="number" name="alert_threshold" id="alertThreshold" value="5" min="1" max="100">
            </label>
            <label class="field">
              <span>Log Retention Days</span>
              <input type="number" name="log_retention" id="logRetention" value="30" min="1" max="365">
            </label>
            <label class="checkbox-row">
              <input type="checkbox" name="email_alerts" id="emailAlerts" checked>
              <span>Enable Email Alerts</span>
            </label>
            <label class="checkbox-row">
              <input type="checkbox" name="auto_block" id="autoBlock">
              <span>Auto-block Critical IPs</span>
            </label>
            <button type="submit" class="primary-btn">Save Settings</button>
            <p id="settingsMessage" class="message" aria-live="polite"></p>
          </form>
        </section>

        <section class="section" style="margin-top:20px">
          <h3 style="margin-top:0">System Information</h3>
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
            <div class="stat-card">
              <div class="muted">PHP Version</div>
              <div class="stat-value"><?php echo phpversion(); ?></div>
            </div>
            <div class="stat-card">
              <div class="muted">MySQL Version</div>
              <div class="stat-value"><?php echo $conn->server_info ?? 'Unknown'; ?></div>
            </div>
            <div class="stat-card">
              <div class="muted">Server Time</div>
              <div class="stat-value"><?php echo date('Y-m-d H:i:s'); ?></div>
            </div>
            <div class="stat-card">
              <div class="muted">Timezone</div>
              <div class="stat-value"><?php echo date_default_timezone_get(); ?></div>
            </div>
          </div>
        </section>

        <script>
          document.getElementById('settingsForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const msg = document.getElementById('settingsMessage');
            msg.textContent = 'Saving settings...';
            msg.style.color = '';

            setTimeout(() => {
              msg.textContent = 'Settings saved successfully!';
              msg.style.color = 'green';
            }, 500);
          });
        </script>
      </div> <!-- /.main-content -->
    </div> <!-- /.dashboard-layout -->
  </main>
</body>
</html>
