<?php
session_start();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Security Alert - Attack Detected</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }
    body {
      font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
      background: #0d1117;
      color: #ffffff;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 20px;
    }
    .warning-container {
      max-width: 600px;
      background: rgba(22, 27, 34, 0.95);
      border: 2px solid #ff4757;
      border-radius: 16px;
      padding: 40px;
      text-align: center;
      box-shadow: 0 20px 60px rgba(255, 71, 87, 0.3);
    }
    .warning-icon {
      font-size: 4rem;
      margin-bottom: 20px;
    }
    h1 {
      color: #ff4757;
      font-size: 2rem;
      margin-bottom: 15px;
    }
    .attack-type {
      background: rgba(255, 71, 87, 0.2);
      color: #ff4757;
      padding: 8px 16px;
      border-radius: 8px;
      font-weight: 600;
      margin: 15px 0;
      display: inline-block;
    }
    .payload {
      background: rgba(48, 54, 61, 0.5);
      padding: 15px;
      border-radius: 8px;
      font-family: 'Courier New', monospace;
      font-size: 0.9rem;
      word-break: break-all;
      margin: 20px 0;
      color: #f6c85f;
    }
    .message {
      color: #8b949e;
      margin-bottom: 25px;
      line-height: 1.6;
    }
    .actions {
      display: flex;
      gap: 15px;
      justify-content: center;
      flex-wrap: wrap;
    }
    .btn {
      padding: 12px 30px;
      border-radius: 8px;
      font-weight: 600;
      text-decoration: none;
      transition: all 0.3s ease;
      border: none;
      cursor: pointer;
    }
    .btn-primary {
      background: linear-gradient(135deg, #00ff66, #00cc55);
      color: #0d1117;
    }
    .btn-primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(0, 255, 102, 0.4);
    }
    .btn-secondary {
      background: transparent;
      color: #8b949e;
      border: 2px solid #30363d;
    }
    .btn-secondary:hover {
      border-color: #8b949e;
      color: #ffffff;
    }
    .logged-info {
      margin-top: 25px;
      padding-top: 20px;
      border-top: 1px solid #30363d;
      color: #4ade80;
      font-size: 0.9rem;
    }
  </style>
</head>
<body>
  <div class="warning-container">
    <div class="warning-icon">🚨</div>
    <h1>Security Alert</h1>
    <p class="message">Malicious activity has been detected in your request. This attack has been blocked and logged for security purposes.</p>
    
    <div class="attack-type">
      Attack Type: <?php echo htmlspecialchars($_GET['type'] ?? 'Unknown'); ?>
    </div>
    
    <div class="payload">
      Detected Payload: <?php echo htmlspecialchars($_GET['payload'] ?? 'Not available'); ?>
    </div>
    
    <div class="message">
      Your IP address and the attack details have been recorded in our security logs. Continued malicious activity may result in permanent access restrictions.
    </div>
    
    <div class="actions">
      <a href="../dashboard/index.php" class="btn btn-primary">Return to Dashboard</a>
      <a href="../index.php" class="btn btn-secondary">Go to Home</a>
    </div>
    
    <div class="logged-info">
      ✓ This attack has been logged to the security database
    </div>
  </div>
</body>
</html>
