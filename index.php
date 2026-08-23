<?php

declare(strict_types=1);

/**
 * STN-Labz API (v1) — Production Stable
 * Restores original look/feel while adding Live Analysis and Dynamic Patterns.
 * Updated: Latest-first reporting across all endpoints.
 */
 
 /**
  * Debug
  * Set to true for development
  * Set to false for production
*/
$debug=true;
if($debug) {
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
}

// --- Bootstrap ---------------------------------------------------------------
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/processor.php';

if (!function_exists('str_starts_with')) {
    function str_starts_with(string $h, string $n): bool { return $n === '' || strpos($h, $n) === 0; }
}

// Global Headers
header('X-Powered-By: STN-Labz');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: X-API-KEY, Content-Type');

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
if ($method === 'OPTIONS') exit;

// --- Helpers & Analysis ------------------------------------------------------

/**
 * Loads threats from storage and reverses them to ensure latest-first order.
 */
function load_threats(): array {
    $file = __DIR__ . '/data/threats.json';
    if (!file_exists($file)) return [];
    $data = @file_get_contents($file);
    $decoded = json_decode((string)$data, true);
    if (!is_array($decoded)) return [];
    // Ensure newest items are at index 0 for all GET requests
    return array_reverse($decoded);
}

/**
 * Live Analysis: Calculates velocity and impact across the 25 businesses
 */
function get_live_analysis(array $threats): array {
    $now = time();
    $day_ago = $now - 86400;
    $analysis = ['total_24h' => 0, 'trending' => [], 'active_sites' => []];

    foreach ($threats as $t) {
        $ts = strtotime($t['created_at'] ?? 'now');
        if ($ts > $day_ago) {
            $analysis['total_24h']++;
            $p = $t['details']['matched_pattern'] ?? 'probe';
            $sid = $t['details']['site_id'] ?? 'unknown';
            $analysis['trending'][$p] = ($analysis['trending'][$p] ?? 0) + 1;
            $analysis['active_sites'][$sid] = ($analysis['active_sites'][$sid] ?? 0) + 1;
        }
    }
    arsort($analysis['trending']);
    arsort($analysis['active_sites']);
    return $analysis;
}

function get_dynamic_patterns(array $threats): array {
    $patterns = ['wp-admin', 'xmlrpc', '.env', 'eval(', 'config.php', 'cgi-bin'];
    foreach ($threats as $t) {
        $target = $t['details']['request_url'] ?? ($t['details']['matched_pattern'] ?? '');
        if (!empty($target) && preg_match('#(\.env|wp-login|xmlrpc|eval\(|phpinfo|cgi-bin)#i', $target, $m)) {
            $patterns[] = strtolower($m[0]);
        }
    }
    return array_values(array_unique($patterns));
}

// --- Routing ------------------------------------------------------------------

$uri = $_SERVER['REQUEST_URI'] ?? '/';
$path = parse_url($uri, PHP_URL_PATH) ?: '/';

// Legacy path cleaning to support /api/v1 or root calls
$clean_path = preg_replace('#^/(api|v1)#i', '', $path);
$clean_path = '/' . ltrim($clean_path, '/');

// 1. GET /intel (Restored for widget.php + Sentinel)
if ($method === 'GET' && preg_match('#^/intell?$#i', $clean_path)) {
    header('Content-Type: application/json');
    $threats = load_threats();
    $by_type = [];
    foreach ($threats as $t) { $type = $t['type'] ?? 'unknown'; $by_type[$type] = ($by_type[$type] ?? 0) + 1; }

    echo json_encode([
        'version' => '1.0.6',
        'generated_at' => gmdate('c'),
        'stats' => ['total_threats' => count($threats), 'by_type' => $by_type],
        'analysis' => get_live_analysis($threats),
        'patterns' => get_dynamic_patterns($threats),
        'blocklists' => ['ips' => array_slice(array_unique(array_column($threats, 'ip')), 0, 100)]
    ], JSON_PRETTY_PRINT);
    exit;
}

// 2. GET /patterns (Dedicated endpoint)
if ($method === 'GET' && $clean_path === '/patterns') {
    header('Content-Type: application/json');
    echo json_encode(['patterns' => get_dynamic_patterns(load_threats())]);
    exit;
}

// 3. GET /threats & /events (RAW DATA - Latest First)
if ($method === 'GET' && preg_match('#^/(threats|events)$#i', $clean_path)) {
    header('Content-Type: application/json');
    echo json_encode(['threats' => load_threats()], JSON_PRETTY_PRINT);
    exit;
}

// 4. POST /threats (Ingestion)
if ($method === 'POST' && $clean_path === '/threats') {
    header('Content-Type: application/json');
    $key = $_SERVER['HTTP_X_API_KEY'] ?? '';
    if (!hash_equals(API_SECRET, $key)) { http_response_code(401); die(json_encode(['error' => 'Unauthorized'])); }
    
    $input = json_decode(file_get_contents('php://input'), true);
    if ($input) {
        // Load threats (already reversed by load_threats())
        $feed = load_threats();
        // Reverse back to chronological order for storage
        $feed = array_reverse($feed);
        $feed[] = [
            'id' => uniqid('t_'),
            'ip' => $_SERVER['REMOTE_ADDR'],
            'type' => $input['type'] ?? 'unknown',
            'details' => $input['details'] ?? [],
            'created_at' => date('Y-m-d H:i:s')
        ];
        file_put_contents(__DIR__ . '/data/threats.json', json_encode($feed));
        echo json_encode(['status' => 'success']);
    }
    exit;
}

// 5. Second Life Threat Feed
if ($method == 'GET' && $clean_path=== '/sl-feed') {
      header('Content-Type: text/plain');

    $file = __DIR__ . '/data/threats.json';

    if (!file_exists($file))
    {
        echo "ERROR: FILE";
        exit;
    }

    $raw = file_get_contents($file);
    $data = json_decode($raw, true);

    if (!$data)
    {
        echo "ERROR: JSON";
        exit;
    }

    // handle BOTH formats safely
    $threats = isset($data['threats']) ? $data['threats'] : $data;

    $lines = [];

    foreach ($threats as $t)
    {
        $domain = $t['details']['domain'] ?? 'unknown';
        $url    = $t['details']['request_url'] ?? '';

        if (strpos($url, 'wp-admin') !== false)
        {
            $label = 'wp-admin hit';
        }
        elseif (strpos($url, 'xmlrpc') !== false)
        {
            $label = 'xmlrpc probe';
        }
        elseif (strpos($url, 'install') !== false)
        {
            $label = 'install attempt';
        }
        else
        {
            $label = $url ? trim($url, '/') : 'unknown activity';
        }

        $lines[] = "[SCAN] " . $label . " | " . $domain;
    }

    echo implode("\n", array_slice($lines, 0, 10));
    exit;
}

// 6. ROOT DOMAIN - RESTORED ORIGINAL HYPERLINKS
if ($clean_path === '/') {
    header('Content-Type: text/html; charset=utf-8');
    ?>
    <!DOCTYPE html>
    <html>
    <head><title>STN-Labz API</title></head>
    <body>
        <h3>STN-Labz Intelligence API v1</h3>
        <ul>
            <li><a href="threats">/threats</a> - Raw Data Feed (Latest First)</li>
            <li><a href="events">/events</a> - Legacy Feed</li>
            <li><a href="intel">/intel</a> - Intelligence & Analysis</li>
            <li><a href="patterns">/patterns</a> - Dynamic Firewall Patterns</li>
        </ul>
    </body>
    </html>
    <?php
    exit;
}

http_response_code(404);
echo json_encode(['error' => 'Not Found', 'path' => $clean_path]);
