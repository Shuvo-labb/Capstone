<?php
// Programmer Name : MD ABU SAYED SHUVO
// Program Name    : email_config.php
// Description     : Shared include or helper
// First Commit Date: Wednesday,24-Jun-2026
// Last Commit Date : Wednesday,24-Jun-2026
/**
 * Email Configuration for Mailtrap
 * 
 * Update these credentials with your Mailtrap account details
 * Get credentials from: https://mailtrap.io/signin
 */

return [
    'host' => 'sandbox.smtp.mailtrap.io',
    'port' => 2525,
    'username' => '96d23b45f0181e', // Add your Mailtrap username here
    'password' => '351b0833974a02', // Add your Mailtrap password here
    'from_email' => 'gg@gg.com',
    'from_name' => 'Security Threat Dashboard',
    'admin_email' => 'ggg@gg.com' // Email to receive critical alerts
];
