<?php
// Registry of processor functions
$threat_processors = [
    'malware' => 'process_malware',
    'injection' => 'process_injection',
    // Add more: 'ddos' => 'process_ddos',
];

// Processor for malware threats
function process_malware($threat_id, $source, $type, $details, $ip) {
    // Example: Log to file (extend with email, external API, etc.)
    $severity = $details['severity'] ?? 'low';
    $log_message = "[$threat_id] Malware from $source: Severity $severity\n";
    file_put_contents('logs/threats.log', $log_message, FILE_APPEND);
}

// Processor for SQL injection threats
function process_injection($threat_id, $source, $type, $details, $ip) {
    // Example: Log and simulate alerting
    $query = $details['query'] ?? 'unknown';
    $log_message = "[$threat_id] Injection from $source: Query $query\n";
    file_put_contents('logs/threats.log', $log_message, FILE_APPEND);
}
