<?php
require_once __DIR__ . "/../auth/require_login.php";
require_once __DIR__ . "/../auth/global_security.php";
$active_page = 'upload_log.php';
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
      <?php require_once __DIR__ . "/sidebar.php"; ?>

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
