<?php
require_once __DIR__ . "/../auth/require_login.php";
require_once __DIR__ . "/../auth/global_security.php";
$active_page = 'audit_trail.php';
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Security Threat Dashboard — Audit Trail</title>
  <link rel="stylesheet" href="../../css/style.css">
</head>
<body>
  <main class="dashboard-shell">
    <div class="dashboard-layout">
      <?php require_once __DIR__ . "/sidebar.php"; ?>

      <div class="main-content">
        <div class="topbar">
          <div>
            <h2 style="margin:0 0 6px 0">Audit Trail</h2>
            <p class="muted" style="margin:0">Review system activity and user actions.</p>
          </div>
        </div>

        <section class="section">
          <h3 style="margin-top:0">System Activities</h3>
          <div class="controls" style="display:flex; gap:8px; align-items:center; margin-bottom:12px;">
            <label class="field"><span>Filter by user</span><input type="text" id="filterUser" placeholder="username"></label>
            <label class="field"><span>Action</span><input type="text" id="filterAction" placeholder="e.g. login"></label>
            <button id="applyFilters" class="primary-btn">Apply</button>
          </div>

          <div style="margin-top:12px; max-height:520px; overflow:auto;">
            <table id="logsTable" style="width:100%; border-collapse:collapse;">
              <thead>
                <tr>
                  <th style="padding:10px; border-bottom:1px solid rgba(118,153,207,0.04); text-align:left;">When</th>
                  <th style="padding:10px; border-bottom:1px solid rgba(118,153,207,0.04); text-align:left;">User</th>
                  <th style="padding:10px; border-bottom:1px solid rgba(118,153,207,0.04); text-align:left;">Action</th>
                  <th style="padding:10px; border-bottom:1px solid rgba(118,153,207,0.04); text-align:left;">Source IP</th>
                </tr>
              </thead>
              <tbody>
                <tr><td colspan="4" class="muted" style="padding:10px;">Loading...</td></tr>
              </tbody>
            </table>
          </div>
        </section>
      </div> <!-- /.main-content -->
    </div> <!-- /.dashboard-layout -->
  </main>
  <script src="../../js/dashboard/activity_logs.js" defer></script>
</body>
</html>