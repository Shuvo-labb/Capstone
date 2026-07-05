<?php
// Programmer Name : MD ABU SAYED SHUVO
// Program Name    : index.php
// Description     : Project source file
// First Commit Date: Sunday,21-Jun-2026
// Last Commit Date : Sunday,21-Jun-2026
session_start();
require_once __DIR__ . "/auth/global_security.php";
require_once __DIR__ . "/../../database/db_connect.php";

// Ensure AttackEvents table exists
$conn->query("CREATE TABLE IF NOT EXISTS AttackEvents (
    attack_id INT(11) AUTO_INCREMENT PRIMARY KEY,
    source_ip VARCHAR(45) NOT NULL,
    attack_type ENUM('SQL Injection','XSS','Directory Traversal','Brute Force','Other') NOT NULL,
    payload TEXT NOT NULL,
    target_endpoint VARCHAR(255) NOT NULL,
    attempted_username VARCHAR(100),
    user_agent VARCHAR(500),
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_attack_type (attack_type),
    INDEX idx_source_ip (source_ip),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

// Get live security metrics from database
$totalAttacks = 0;
$rogueIPs = 0;
$logsAnalyzed = 0;
$systemUptime = "99.9%";

// Count total attacks from both AttackEvents (direct input) and Threats (log parsing)
$stmt1 = $conn->query("SELECT COUNT(*) as count FROM AttackEvents");
if ($stmt1) {
    $row1 = $stmt1->fetch_assoc();
    $totalAttacks += $row1['count'] ?? 0;
    $stmt1->close();
}

$stmt2 = $conn->query("SELECT COUNT(*) as count FROM Threats");
if ($stmt2) {
    $row2 = $stmt2->fetch_assoc();
    $totalAttacks += $row2['count'] ?? 0;
    $stmt2->close();
}

// Count unique rogue IPs
$stmt3 = $conn->query("SELECT COUNT(DISTINCT source_ip) as count FROM AttackEvents");
if ($stmt3) {
    $row3 = $stmt3->fetch_assoc();
    $rogueIPs += $row3['count'] ?? 0;
    $stmt3->close();
}

$stmt4 = $conn->query("SELECT COUNT(DISTINCT ip_address) as count FROM Threats");
if ($stmt4) {
    $row4 = $stmt4->fetch_assoc();
    $rogueIPs += $row4['count'] ?? 0;
    $stmt4->close();
}

// Count logs analyzed
$stmt5 = $conn->query("SELECT COUNT(*) as count FROM Logs WHERE parse_status = 'Completed'");
if ($stmt5) {
    $row5 = $stmt5->fetch_assoc();
    $logsAnalyzed = $row5['count'] ?? 0;
    $stmt5->close();
}

$conn->close();

$isLoggedIn = isset($_SESSION["user_id"]);
$ctaText = $isLoggedIn ? "Return to Console" : "Access Dashboard";
$ctaLink = $isLoggedIn ? "../dashboard/index.php" : "auth/login.php";
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Security Threat Dashboard - Enterprise SIEM Threat Engine</title>
  <style>
    :root {
      --bg-primary: #0d1117;
      --bg-secondary: #05050a;
      --accent-green: #00ff66;
      --accent-blue: #00d4ff;
      --accent-amber: #ffaa00;
      --accent-red: #ff4757;
      --text-primary: #ffffff;
      --text-secondary: #8b949e;
      --border-color: #30363d;
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
      background: var(--bg-primary);
      color: var(--text-primary);
      overflow-x: hidden;
      line-height: 1.6;
    }

    /* Matrix/Radar Backdrop */
    .hero-backdrop {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      z-index: -1;
      background: 
        radial-gradient(ellipse at 20% 20%, rgba(0, 255, 102, 0.05) 0%, transparent 50%),
        radial-gradient(ellipse at 80% 80%, rgba(0, 212, 255, 0.05) 0%, transparent 50%),
        radial-gradient(ellipse at 50% 50%, rgba(255, 170, 0, 0.03) 0%, transparent 70%),
        var(--bg-primary);
    }

    .grid-overlay {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      z-index: -1;
      background-image: 
        linear-gradient(rgba(48, 54, 61, 0.1) 1px, transparent 1px),
        linear-gradient(90deg, rgba(48, 54, 61, 0.1) 1px, transparent 1px);
      background-size: 50px 50px;
      animation: gridMove 20s linear infinite;
    }

    @keyframes gridMove {
      0% { transform: translate(0, 0); }
      100% { transform: translate(50px, 50px); }
    }

    /* Navigation */
    nav {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      padding: 20px 40px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      z-index: 100;
      background: linear-gradient(to bottom, rgba(13, 17, 23, 0.95), transparent);
    }

    .logo {
      font-size: 1.5rem;
      font-weight: 700;
      background: linear-gradient(135deg, var(--accent-green), var(--accent-blue));
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }

    .nav-links a {
      color: var(--text-secondary);
      text-decoration: none;
      margin-left: 30px;
      transition: color 0.3s ease;
    }

    .nav-links a:hover {
      color: var(--accent-green);
    }

    /* Hero Section */
    .hero {
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 120px 40px 80px;
      text-align: center;
    }

    .hero-content {
      max-width: 900px;
      animation: fadeInUp 1s ease-out;
    }

    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translateY(30px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .hero h1 {
      font-size: 3.5rem;
      font-weight: 800;
      margin-bottom: 20px;
      background: linear-gradient(135deg, var(--text-primary), var(--accent-green));
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
      line-height: 1.2;
    }

    .hero p {
      font-size: 1.25rem;
      color: var(--text-secondary);
      margin-bottom: 40px;
    }

    .cta-group {
      display: flex;
      gap: 20px;
      justify-content: center;
      flex-wrap: wrap;
    }

    .btn {
      padding: 16px 40px;
      border-radius: 8px;
      font-size: 1rem;
      font-weight: 600;
      text-decoration: none;
      transition: all 0.3s ease;
      cursor: pointer;
      border: none;
      position: relative;
      overflow: hidden;
    }

    .btn-primary {
      background: linear-gradient(135deg, var(--accent-green), #00cc55);
      color: var(--bg-primary);
      box-shadow: 0 4px 20px rgba(0, 255, 102, 0.3);
    }

    .btn-primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 30px rgba(0, 255, 102, 0.5);
    }

    .btn-secondary {
      background: transparent;
      color: var(--accent-green);
      border: 2px solid var(--accent-green);
    }

    .btn-secondary:hover {
      background: rgba(0, 255, 102, 0.1);
      transform: translateY(-2px);
    }

    /* Metrics Grid */
    .metrics-section {
      padding: 80px 40px;
      max-width: 1400px;
      margin: 0 auto;
    }

    .metrics-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      gap: 30px;
    }

    .metric-card {
      background: rgba(22, 27, 34, 0.8);
      border: 1px solid var(--border-color);
      border-radius: 16px;
      padding: 30px;
      text-align: center;
      transition: all 0.3s ease;
      opacity: 0;
      transform: translateY(30px);
    }

    .metric-card.visible {
      opacity: 1;
      transform: translateY(0);
    }

    .metric-card:hover {
      border-color: var(--accent-green);
      transform: translateY(-5px);
      box-shadow: 0 10px 40px rgba(0, 255, 102, 0.1);
    }

    .metric-value {
      font-size: 3rem;
      font-weight: 800;
      background: linear-gradient(135deg, var(--accent-green), var(--accent-blue));
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
      margin-bottom: 10px;
    }

    .metric-label {
      font-size: 1rem;
      color: var(--text-secondary);
      text-transform: uppercase;
      letter-spacing: 2px;
    }

    /* Features Section */
    .features-section {
      padding: 80px 40px;
      max-width: 1400px;
      margin: 0 auto;
    }

    .section-title {
      font-size: 2.5rem;
      font-weight: 700;
      text-align: center;
      margin-bottom: 60px;
      opacity: 0;
      transform: translateY(30px);
      transition: all 0.6s ease;
    }

    .section-title.visible {
      opacity: 1;
      transform: translateY(0);
    }

    .features-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
      gap: 30px;
    }

    .feature-card {
      background: rgba(22, 27, 34, 0.6);
      border: 1px solid var(--border-color);
      border-radius: 16px;
      padding: 40px;
      transition: all 0.3s ease;
      opacity: 0;
      transform: translateY(30px);
    }

    .feature-card.visible {
      opacity: 1;
      transform: translateY(0);
    }

    .feature-card:hover {
      border-color: var(--accent-blue);
      transform: translateY(-5px);
      box-shadow: 0 10px 40px rgba(0, 212, 255, 0.1);
    }

    .feature-icon {
      width: 60px;
      height: 60px;
      background: linear-gradient(135deg, var(--accent-blue), var(--accent-green));
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-bottom: 20px;
      font-size: 1.5rem;
    }

    .feature-title {
      font-size: 1.25rem;
      font-weight: 600;
      margin-bottom: 15px;
    }

    .feature-desc {
      color: var(--text-secondary);
      line-height: 1.6;
    }

    /* Footer */
    footer {
      padding: 40px;
      text-align: center;
      border-top: 1px solid var(--border-color);
      color: var(--text-secondary);
    }

    /* Responsive */
    @media (max-width: 768px) {
      .hero h1 {
        font-size: 2.5rem;
      }

      .hero p {
        font-size: 1rem;
      }

      .cta-group {
        flex-direction: column;
        align-items: center;
      }

      .btn {
        width: 100%;
        max-width: 300px;
      }

      nav {
        padding: 15px 20px;
      }

      .nav-links {
        display: none;
      }

      .metrics-section,
      .features-section {
        padding: 60px 20px;
      }
    }
  </style>
</head>
<body>
  <div class="hero-backdrop"></div>
  <div class="grid-overlay"></div>

  <nav>
    <div class="logo">SECURITY THREAT DASHBOARD</div>
    <div class="nav-links">
      <a href="#features">Features</a>
      <a href="#metrics">Metrics</a>
      <a href="<?php echo $ctaLink; ?>"><?php echo $ctaText; ?></a>
    </div>
  </nav>

  <section class="hero">
    <div class="hero-content">
      <h1>Enterprise SIEM Threat Engine</h1>
      <p>Advanced real-time threat detection, log analysis, and automated security monitoring for modern infrastructure.</p>
      <div class="cta-group">
        <a href="<?php echo $ctaLink; ?>" class="btn btn-primary"><?php echo $ctaText; ?></a>
        <a href="auth/login.php" class="btn btn-secondary">Analyze System Logs</a>
      </div>
    </div>
  </section>

  <section class="metrics-section" id="metrics">
    <div class="metrics-grid">
      <div class="metric-card">
        <div class="metric-value" data-target="<?php echo $totalAttacks; ?>">0</div>
        <div class="metric-label">Total Attacks Blocked</div>
      </div>
      <div class="metric-card">
        <div class="metric-value" data-target="<?php echo $rogueIPs; ?>">0</div>
        <div class="metric-label">Rogue IPs Identified</div>
      </div>
      <div class="metric-card">
        <div class="metric-value" data-target="<?php echo $logsAnalyzed; ?>">0</div>
        <div class="metric-label">Logs Analyzed</div>
      </div>
      <div class="metric-card">
        <div class="metric-value" data-target="99">0</div>
        <div class="metric-label">System Uptime %</div>
      </div>
    </div>
  </section>

  <section class="features-section" id="features">
    <h2 class="section-title">Core Capabilities</h2>
    <div class="features-grid">
      <div class="feature-card">
        <div class="feature-icon">Ã°Å¸â€œÂ¡</div>
        <h3 class="feature-title">Real-Time Log Upload</h3>
        <p class="feature-desc">Upload Apache/Nginx access logs for instant parsing and threat detection with automated pattern recognition.</p>
      </div>
      <div class="feature-card">
        <div class="feature-icon">Ã°Å¸â€Â</div>
        <h3 class="feature-title">Heuristic Pattern Parsing</h3>
        <p class="feature-desc">Advanced regex-based detection for SQL injection, XSS, brute force, and directory traversal attacks.</p>
      </div>
      <div class="feature-card">
        <div class="feature-icon">Ã°Å¸â€ºÂ¡Ã¯Â¸Â</div>
        <h3 class="feature-title">Deep SQLi/XSS Detection</h3>
        <p class="feature-desc">Real-time input interception on login forms and public endpoints with automatic threat logging.</p>
      </div>
      <div class="feature-card">
        <div class="feature-icon">Ã°Å¸â€œÅ </div>
        <h3 class="feature-title">Filtered Export Engines</h3>
        <p class="feature-desc">Generate comprehensive security reports in CSV/PDF format with custom date ranges and threat filters.</p>
      </div>
    </div>
  </section>

  <footer>
    <p>&copy; 2026 Security Threat Dashboard. Enterprise SIEM Threat Engine.</p>
  </footer>

  <script>
    // Intersection Observer for scroll animations
    const observerOptions = {
      threshold: 0.1,
      rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('visible');
          
          // Trigger counter animation if it's a metric card
          if (entry.target.classList.contains('metric-card')) {
            const counter = entry.target.querySelector('.metric-value');
            if (counter) {
              animateCounter(counter);
            }
          }
        }
      });
    }, observerOptions);

    // Observe all animated elements
    document.querySelectorAll('.metric-card, .feature-card, .section-title').forEach(el => {
      observer.observe(el);
    });

    // Counter animation function
    function animateCounter(element) {
      const target = parseInt(element.getAttribute('data-target'));
      const duration = 2000;
      const steps = 60;
      const increment = target / steps;
      let current = 0;

      const timer = setInterval(() => {
        current += increment;
        if (current >= target) {
          element.textContent = target.toLocaleString();
          clearInterval(timer);
        } else {
          element.textContent = Math.floor(current).toLocaleString();
        }
      }, duration / steps);
    }

    // Smooth scroll for navigation links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
      anchor.addEventListener('click', function(e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
          target.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
          });
        }
      });
    });
  </script>
</body>
</html>
