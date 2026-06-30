<?php
/**
 * PHPMailer Email Helper for Mailtrap
 * 
 * Uses PHPMailer library for reliable SMTP email sending
 * Designed specifically for Mailtrap integration
 */

// Load PHPMailer classes
require_once __DIR__ . '/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/PHPMailer/src/SMTP.php';
require_once __DIR__ . '/PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class EmailHelper {
    private $config;
    
    public function __construct() {
        $this->config = require __DIR__ . '/email_config.php';
    }
    
    /**
     * Send email using PHPMailer with SMTP
     * 
     * @param string $to Recipient email
     * @param string $subject Email subject
     * @param string $body HTML email body
     * @param string $altBody Plain text alternative
     * @return bool Success status
     */
    public function send($to, $subject, $body, $altBody = '') {
        // Check if credentials are configured
        if (empty($this->config['username']) || empty($this->config['password'])) {
            error_log("Email not sent: Mailtrap credentials not configured");
            return false;
        }
        
        $mail = new PHPMailer(true);
        
        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host = $this->config['host'];
            $mail->SMTPAuth = true;
            $mail->Username = $this->config['username'];
            $mail->Password = $this->config['password'];
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = $this->config['port'];
            
            // Recipients
            $mail->setFrom($this->config['from_email'], $this->config['from_name']);
            $mail->addAddress($to);
            
            // Content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $body;
            $mail->AltBody = $altBody ?: strip_tags($body);
            
            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Email failed to send: " . $mail->ErrorInfo);
            return false;
        }
    }
    
    /**
     * Send password reset email
     * 
     * @param string $email User's email
     * @param string $username User's username
     * @param string $resetToken Password reset token
     * @return bool Success status
     */
    public function sendPasswordReset($email, $username, $resetToken) {
        // Build reset link - adjust base path to match your project structure
        $resetLink = "http://" . $_SERVER['HTTP_HOST'] . "/Security%20Threat%20Dashboardd/frontend/html/auth/reset_password.php?token=" . $resetToken;
        
        $subject = "Password Reset Request - Security Threat Dashboard";
        
        $body = "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #1a1a2e; color: #fff; padding: 20px; text-align: center; }
                .content { background: #f5f5f5; padding: 30px; border-radius: 5px; }
                .button { display: inline-block; padding: 12px 30px; background: #4CAF50; color: #fff; text-decoration: none; border-radius: 5px; margin: 20px 0; }
                .footer { text-align: center; margin-top: 20px; color: #666; font-size: 12px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h2>Security Threat Dashboard</h2>
                </div>
                <div class='content'>
                    <h3>Password Reset Request</h3>
                    <p>Hello $username,</p>
                    <p>We received a request to reset your password. Click the button below to reset it:</p>
                    <p style='text-align: center;'>
                        <a href='$resetLink' class='button'>Reset Password</a>
                    </p>
                    <p>Or copy this link into your browser:</p>
                    <p style='word-break: break-all; color: #4CAF50;'>$resetLink</p>
                    <p><strong>This link will expire in 1 hour.</strong></p>
                    <p>If you didn't request this, please ignore this email.</p>
                </div>
                <div class='footer'>
                    <p>&copy; " . date('Y') . " Security Threat Dashboard. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>
        ";
        
        return $this->send($email, $subject, $body);
    }
    
    /**
     * Send critical attack alert email to admin
     * 
     * @param string $attackType Type of attack
     * @param string $ipAddress Attacker's IP
     * @param string $payload Attack payload
     * @param string $targetEndpoint Target endpoint
     * @return bool Success status
     */
    public function sendAttackAlert($attackType, $ipAddress, $payload, $targetEndpoint) {
        $subject = "⚠️ CRITICAL ATTACK ALERT: $attackType detected";
        
        $body = "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #d32f2f; color: #fff; padding: 20px; text-align: center; }
                .content { background: #f5f5f5; padding: 30px; border-radius: 5px; }
                .alert-box { background: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin: 20px 0; }
                .label { font-weight: bold; color: #555; }
                .value { color: #d32f2f; font-family: monospace; }
                .footer { text-align: center; margin-top: 20px; color: #666; font-size: 12px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h2>🚨 CRITICAL SECURITY ALERT</h2>
                </div>
                <div class='content'>
                    <div class='alert-box'>
                        <p><strong>A $attackType attack has been detected!</strong></p>
                    </div>
                    
                    <h3>Attack Details:</h3>
                    <p><span class='label'>Attack Type:</span> <span class='value'>$attackType</span></p>
                    <p><span class='label'>Source IP:</span> <span class='value'>$ipAddress</span></p>
                    <p><span class='label'>Target Endpoint:</span> <span class='value'>$targetEndpoint</span></p>
                    <p><span class='label'>Payload:</span> <span class='value'>" . htmlspecialchars(substr($payload, 0, 200)) . "</span></p>
                    <p><span class='label'>Time:</span> <span class='value'>" . date('Y-m-d H:i:s') . "</span></p>
                    
                    <p style='margin-top: 30px;'><strong>Immediate Action Required:</strong></p>
                    <ul>
                        <li>Review the attack details in the dashboard</li>
                        <li>Block the IP address if necessary</li>
                        <li>Check for any successful breaches</li>
                    </ul>
                    
                    <p style='text-align: center; margin-top: 30px;'>
                        <a href='http://" . $_SERVER['HTTP_HOST'] . "/Security%20Threat%20Dashboardd/frontend/html/dashboard/index.php' style='display: inline-block; padding: 12px 30px; background: #d32f2f; color: #fff; text-decoration: none; border-radius: 5px;'>View Dashboard</a>
                    </p>
                </div>
                <div class='footer'>
                    <p>&copy; " . date('Y') . " Security Threat Dashboard. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>
        ";
        
        return $this->send($this->config['admin_email'], $subject, $body);
    }
}
