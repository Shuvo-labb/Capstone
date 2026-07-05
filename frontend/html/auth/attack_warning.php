<?php
// Programmer Name : MD ABU SAYED SHUVO
// Program Name    : attack_warning.php
// Description     : Authentication page or handler
// First Commit Date: Sunday,21-Jun-2026
// Last Commit Date : Wednesday,24-Jun-2026
// Start PHP session context
session_start();
?>
<!-- Declare HTML5 document type -->
<!doctype html>
<!-- HTML container block in English -->
<html lang="en">
<!-- Start head container for metadata and CSS styles -->
<head>
  <!-- Set encoding scheme to UTF-8 -->
  <meta charset="utf-8">
  <!-- Define responsive design viewport scaling settings -->
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Set page title string -->
  <title>Security Alert - Attack Detected</title>
  <!-- Start inline stylesheet styles -->
  <style>
    /* Reset margins and paddings for all elements */
    * {
      /* Remove default margin values */
      margin: 0;
      /* Remove default padding values */
      padding: 0;
      /* Include border and padding in element total size calculations */
      box-sizing: border-box;
    }
    /* Define overall page styling on body selector */
    body {
      /* Apply system font family fallback stacks */
      font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
      /* Set dark-themed body background hex color */
      background: #0d1117;
      /* Set default text color to white */
      color: #ffffff;
      /* Ensure body covers full viewport height */
      min-height: 100vh;
      /* Set display layout mode to flex */
      display: flex;
      /* Center flex items along cross-axis vertically */
      align-items: center;
      /* Center flex items along main-axis horizontally */
      justify-content: center;
      /* Set default padding value around container */
      padding: 20px;
    }
    /* Style container box holding warning information */
    .warning-container {
      /* Set maximum container width constraint */
      max-width: 600px;
      /* Set dark container background color with transparency */
      background: rgba(22, 27, 34, 0.95);
      /* Apply prominent red border indicating danger state */
      border: 2px solid #ff4757;
      /* Add border rounded corner spacing radius */
      border-radius: 16px;
      /* Set internal padding spacing of container */
      padding: 40px;
      /* Center align text strings inside container */
      text-align: center;
      /* Apply warning glowing box shadow layout */
      box-shadow: 0 20px 60px rgba(255, 71, 87, 0.3);
    }
    /* Style security warning emoji placeholder */
    .warning-icon {
      /* Set icon scale font size */
      font-size: 4rem;
      /* Add margin separation below warning icon */
      margin-bottom: 20px;
    }
    /* Style main warning header */
    h1 {
      /* Apply danger red hex color code to text */
      color: #ff4757;
      /* Set bold heading size */
      font-size: 2rem;
      /* Add margin space below heading text */
      margin-bottom: 15px;
    }
    /* Style badge block that shows category of attack */
    .attack-type {
      /* Set transparent red background color */
      background: rgba(255, 71, 87, 0.2);
      /* Set text color to red */
      color: #ff4757;
      /* Apply custom padding sizes to badge */
      padding: 8px 16px;
      /* Set rounded corners on badge element */
      border-radius: 8px;
      /* Set font weight value to bold */
      font-weight: 600;
      /* Set vertical margins around the badge */
      margin: 15px 0;
      /* Define display block type as inline block */
      display: inline-block;
    }
    /* Style container showing raw attack payload */
    .payload {
      /* Set dark code background hex color */
      background: rgba(48, 54, 61, 0.5);
      /* Apply internal padding to code container */
      padding: 15px;
      /* Apply small border radius corners */
      border-radius: 8px;
      /* Use monospace font stack for payload text */
      font-family: 'Courier New', monospace;
      /* Set slightly smaller font size for code */
      font-size: 0.9rem;
      /* Force break of long continuous payload strings */
      word-break: break-all;
      /* Set vertical margins around payload box */
      margin: 20px 0;
      /* Apply golden hex color code to payload text */
      color: #f6c85f;
    }
    /* Style status description paragraph message */
    .message {
      /* Set muted grey color scheme to text */
      color: #8b949e;
      /* Add margin spacing below the message */
      margin-bottom: 25px;
      /* Set comfortable line height text spacing */
      line-height: 1.6;
    }
    /* Style actions buttons layout alignment */
    .actions {
      /* Set display layout to flex */
      display: flex;
      /* Apply gap spacing between action buttons */
      gap: 15px;
      /* Center align buttons along main-axis horizontally */
      justify-content: center;
      /* Enable flex wrap formatting to allow wrap-around */
      flex-wrap: wrap;
    }
    /* Style base layout class for buttons */
    .btn {
      /* Set default padding spacing values on button */
      padding: 12px 30px;
      /* Set button rounded corner border radius */
      border-radius: 8px;
      /* Set font weight value to bold */
      font-weight: 600;
      /* Remove standard anchor text underline decoration */
      text-decoration: none;
      /* Set transition duration configuration for animations */
      transition: all 0.3s ease;
      /* Remove default button borders */
      border: none;
      /* Change cursor indicator to pointer pointer */
      cursor: pointer;
    }
    /* Style primary button variant */
    .btn-primary {
      /* Apply green gradient background colors */
      background: linear-gradient(135deg, #00ff66, #00cc55);
      /* Set text color to match dark background */
      color: #0d1117;
    }
    /* Style primary button hover state animations */
    .btn-primary:hover {
      /* Translate button position upward on hover */
      transform: translateY(-2px);
      /* Apply green glowing box shadow on hover */
      box-shadow: 0 8px 25px rgba(0, 255, 102, 0.4);
    }
    /* Style secondary button variant */
    .btn-secondary {
      /* Make background transparent */
      background: transparent;
      /* Set text color to muted grey */
      color: #8b949e;
      /* Apply custom border outline styles */
      border: 2px solid #30363d;
    }
    /* Style secondary button hover state changes */
    .btn-secondary:hover {
      /* Highlight border color on hover */
      border-color: #8b949e;
      /* Set text color to white on hover */
      color: #ffffff;
    }
    /* Style bottom log notification details block */
    .logged-info {
      /* Add margin separation on top of logging info */
      margin-top: 25px;
      /* Set padding space above log message */
      padding-top: 20px;
      /* Add top border divider line style */
      border-top: 1px solid #30363d;
      /* Apply green color indicating logged event status */
      color: #4ade80;
      /* Set smaller font size for detail text */
      font-size: 0.9rem;
    }
  </style>
<!-- End head container block -->
</head>
<!-- Start document body container -->
<body>
  <!-- Create warning dialog box card wrapper -->
  <div class="warning-container">
    <!-- Render alert icon graphic symbol -->
    <div class="warning-icon">ðŸš¨</div>
    <!-- Render main card header label -->
    <h1>Security Alert</h1>
    <!-- Render detailed warning messages -->
    <p class="message">Malicious activity has been detected in your request. This attack has been blocked and logged for security purposes.</p>
    
    <!-- Render category label printing attack type safely from GET parameters -->
    <div class="attack-type">
      Attack Type: <?php echo htmlspecialchars($_GET['type'] ?? 'Unknown'); ?>
    </div>
    
    <!-- Render custom payload box showing detected malicious text safely from GET parameters -->
    <div class="payload">
      Detected Payload: <?php echo htmlspecialchars($_GET['payload'] ?? 'Not available'); ?>
    </div>
    
    <!-- Render warning about IP restrictions and security logs -->
    <div class="message">
      Your IP address and the attack details have been recorded in our security logs. Continued malicious activity may result in permanent access restrictions.
    </div>
    
    <!-- Render navigation action links -->
    <div class="actions">
      <!-- Anchor link back to main dashboard -->
      <a href="../dashboard/index.php" class="btn btn-primary">Return to Dashboard</a>
      <!-- Anchor link routing to home screen landing page -->
      <a href="../index.php" class="btn btn-secondary">Go to Home</a>
    <!-- Close action layout container -->
    </div>
    
    <!-- Render confirmation text indicating successful DB event record -->
    <div class="logged-info">
      âœ“ This attack has been logged to the security database
    <!-- Close logged info block -->
    </div>
  <!-- Close warning container box wrapper -->
  </div>
<!-- End document body container -->
</body>
<!-- Close main html container block -->
</html>
