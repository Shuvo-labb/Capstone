<?php
require_once __DIR__ . "/../auth/require_login.php";
require_once __DIR__ . "/../auth/global_security.php";
$active_page = 'failed_logins.php';
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Failed Login Attempts — Security Threat Dashboard</title>
  <link rel="stylesheet" href="../../css/style.css">
  <style>
    .attempt-row{display:flex;justify-content:space-between;padding:10px;border-radius:10px;background:rgba(255,255,255,0.02);margin-bottom:8px}
    .attempt-row .meta{color:var(--muted);font-size:0.9rem}
  </style>
</head>
<body>
  <main class="dashboard-shell">
    <div class="dashboard-layout">
      <?php require_once __DIR__ . "/sidebar.php"; ?>

      <div class="main-content">
        <div class="topbar">
          <div>
            <h2 style="margin:0 0 6px 0">Failed Login Attempts</h2>
            <p class="muted" style="margin:0">List of recent failed logins and quick actions for blocking or investigating.</p>
          </div>
          <div class="actions"><a class="small-btn" href="../auth/logout.php">Logout</a></div>
        </div>

        <section class="section">
          <div style="margin-bottom:12px" class="controls">
            <label class="field"><span>Threshold</span><input id="threshold" type="number" value="5"></label>
            <button id="applyThreshold" class="primary-btn">Apply</button>
          </div>

          <div id="attemptsList">
            <!-- rows inserted by JS -->
          </div>
        </section>
      </div>
    </div>
  </main>

  <script src="../../js/dashboard/failed_logins.js" defer></script>
</body>
</html>
