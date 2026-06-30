<?php
// Include the database connection file
require_once __DIR__ . '/../../../database/db_connect.php';
// Set the response content type header to JSON
header('Content-Type: application/json');
// Load the gemini configuration array from the config file
$config = require __DIR__ . '/gemini_config.php';
// Parse the incoming JSON request payload
$input = json_decode(file_get_contents('php://input'), true);
// Retrieve the user message from the input or default to empty string
$message = $input['message'] ?? '';
// Check if the user message or Gemini API key is missing
if (empty($message) || empty($config['api_key'])) {
    // Output error message and terminate script
    echo json_encode(['reply' => 'Configuration Error: Missing message or API Key.']);
    // Stop execution
    exit;
}
// Retrieve the security state from the database to build system context
$context = getSecurityContext($conn);
// Define the system instructions for the AI assistant incorporating the database context
$systemPrompt = "You are a Security Threat Dashboard AI assistant. Provide concise, actionable security advice based on the following data:\n\n" . $context;
// Call the Gemini API with key, model, prompt, and user message to get response
$response = callGeminiAPI($config['api_key'], $config['model'], $systemPrompt, $message);
// Return the generated AI assistant response to the client
echo json_encode(['reply' => $response]);

// Declare helper function to assemble security context from current DB state
function getSecurityContext($conn) {
    // Set initial context string header
    $context = "Current Security Status:\n";
    // Query count of unresolved threats grouped by type and severity
    $result = $conn->query("SELECT threat_type, severity, COUNT(*) as count FROM Threats WHERE is_resolved=0 GROUP BY threat_type, severity");
    // Check if query was successful and returned database rows
    if ($result && $result->num_rows > 0) {
        // Append section header for unresolved threats
        $context .= "Unresolved Threats:\n";
        // Loop through each database row in unresolved threats result set
        while ($row = $result->fetch_assoc()) {
            // Append formatted details of unresolved threat count
            $context .= "- {$row['threat_type']} ({$row['severity']}): {$row['count']}\n";
        }
    }
    // Query count of attack events logged in the last 24 hours
    $result = $conn->query("SELECT attack_type, COUNT(*) as count FROM AttackEvents WHERE created_at > DATE_SUB(NOW(), INTERVAL 24 HOUR) GROUP BY attack_type");
    // Check if query was successful and returned database rows
    if ($result && $result->num_rows > 0) {
        // Append section header for recent attacks context
        $context .= "\nLast 24 Hours Attacks:\n";
        // Loop through each database row in recent attacks result set
        while ($row = $result->fetch_assoc()) {
            // Append formatted details of recent attack count
            $context .= "- {$row['attack_type']}: {$row['count']}\n";
        }
    }
    // Query count of active unblocked suspicious IP addresses
    $result = $conn->query("SELECT COUNT(*) as count FROM SuspiciousIPs WHERE is_blocked=0");
    // Check if query was successful and returned results
    if ($result) {
        // Fetch row and append the suspicious IP count to context
        $context .= "\nUnblocked Suspicious IPs: " . ($result->fetch_assoc()['count'] ?? 0) . "\n";
    }
    // Return the completed context string
    return $context;
}

// Declare helper function to call Google Gemini generateContent API endpoint
function callGeminiAPI($apiKey, $model, $systemPrompt, $userMessage) {
    // Construct the endpoint URL with model and api key query parameter
    $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";
    // Set up request payload containing system prompt and user message structure
    $data = [
        'contents' => [['parts' => [['text' => $systemPrompt . "\n\nUser: " . $userMessage]]]],
        'generationConfig' => ['temperature' => 0.7, 'maxOutputTokens' => 500]
    ];
    // Initialize cURL transfer session
    $ch = curl_init($url);
    // Set cURL option to perform standard HTTP POST request
    curl_setopt($ch, CURLOPT_POST, 1);
    // Bind JSON-encoded request payload to cURL options
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    // Set content type header to application/json
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    // Request return of transfer response as a string
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    // Execute cURL session and get returned response
    $response = curl_exec($ch);
    // Retrieve HTTP status response code of request
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    // Terminate and close active cURL session handle
    curl_close($ch);
    // Check if HTTP status response code indicates success
    if ($httpCode === 200) {
        // Decode response body JSON as associative array
        $json = json_decode($response, true);
        // Extract and return text from response content candidates
        return $json['candidates'][0]['content']['parts'][0]['text'] ?? 'No response generated.';
    }
    // Return standard error message with status code on failure
    return "API Error (Status Code {$httpCode}): Unable to get response from Gemini.";
}
?>