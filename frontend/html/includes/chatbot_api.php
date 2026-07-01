<?php
require_once __DIR__ . '/../../../database/db_connect.php';
header('Content-Type: application/json');
$config = require __DIR__ . '/gemini_config.php';
$input = json_decode(file_get_contents('php://input'), true);
$message = $input['message'] ?? '';
if (empty($message) || empty($config['api_key'])) {
    echo json_encode(['reply' => 'Configuration Error: Missing message or API Key.']);
    exit;
}
// Retrieve the security state from the database to build system context
$context = getSecurityContext($conn);
$systemPrompt = "You are a Security Threat Dashboard AI assistant. Provide concise, actionable security advice based on the following data:\n\n" . $context;
$response = callGeminiAPI($config['api_key'], $config['model'], $systemPrompt, $message);
echo json_encode(['reply' => $response]);

// Declare helper function to assemble security context from current DB state
function getSecurityContext($conn) {
    $context = "Current Security Status:\n";
    $result = $conn->query("SELECT threat_type, severity, COUNT(*) as count FROM Threats WHERE is_resolved=0 GROUP BY threat_type, severity");
    if ($result && $result->num_rows > 0) {
        $context .= "Unresolved Threats:\n";
        while ($row = $result->fetch_assoc()) {
            $context .= "- {$row['threat_type']} ({$row['severity']}): {$row['count']}\n";
        }
    }
    // Query count of attack events logged in the last 24 hours
    $result = $conn->query("SELECT attack_type, COUNT(*) as count FROM AttackEvents WHERE created_at > DATE_SUB(NOW(), INTERVAL 24 HOUR) GROUP BY attack_type");
    if ($result && $result->num_rows > 0) {
        $context .= "\nLast 24 Hours Attacks:\n";
        while ($row = $result->fetch_assoc()) {
            $context .= "- {$row['attack_type']}: {$row['count']}\n";
        }
    }
    // Query count of active unblocked suspicious IP addresses
    $result = $conn->query("SELECT COUNT(*) as count FROM SuspiciousIPs WHERE is_blocked=0");
    if ($result) {
        $context .= "\nUnblocked Suspicious IPs: " . ($result->fetch_assoc()['count'] ?? 0) . "\n";
    }
    return $context;
}

// Declare helper function to call Google Gemini generateContent API endpoint
function callGeminiAPI($apiKey, $model, $systemPrompt, $userMessage) {
    $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";
    $data = [
        'contents' => [['parts' => [['text' => $systemPrompt . "\n\nUser: " . $userMessage]]]],
        'generationConfig' => ['temperature' => 0.7, 'maxOutputTokens' => 500]
    ];
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($httpCode === 200) {
        $json = json_decode($response, true);
        return $json['candidates'][0]['content']['parts'][0]['text'] ?? 'No response generated.';
    }
    return "API Error (Status Code {$httpCode}): Unable to get response from Gemini.";
}
?>