<?php

// Extract IP address
if (isset($_SERVER['HTTP_CLIENT_IP'])) {
    $ipaddr = $_SERVER['HTTP_CLIENT_IP'];
} elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    $ipaddr = $_SERVER['HTTP_X_FORWARDED_FOR'];
} else {
    $ipaddr = $_SERVER['REMOTE_ADDR'];
}

// Handle multiple IPs in case of proxies
if (strpos($ipaddr, ',') !== false) {
    $ipaddr = preg_split("/\,/", $ipaddr)[0];
}

// Extract User-Agent
$userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';

// Fail-safe: Skip blocking if User-Agent is missing
if ($userAgent !== 'Unknown') {
    // Block if User-Agent contains Chrome or Chromium
    if (stripos($userAgent, 'Chrome') !== false || stripos($userAgent, 'Chromium') !== false) {
        // Allow specific User-Agents or paths to avoid blocking critical services
        $allowedPaths = ['/api/healthcheck', '/status']; // Example of allowed paths
        $currentPath = $_SERVER['REQUEST_URI'] ?? '/';

        if (!in_array($currentPath, $allowedPaths)) {
            // Send a 403 Forbidden response
            header('HTTP/1.1 403 Forbidden');
            echo "Access Denied: Your browser is not allowed.";
            exit;
        }
    }
}

// Parse User-Agent for browser and platform
function getBrowserInfo($userAgent) {
    $browsers = ['Firefox', 'Chrome', 'Safari', 'Edge', 'Opera', 'MSIE', 'Trident'];
    $platforms = ['Windows', 'Macintosh', 'Linux', 'Android', 'iPhone', 'iPad'];

    $browser = 'Unknown';
    foreach ($browsers as $b) {
        if (stripos($userAgent, $b) !== false) {
            $browser = $b;
            if ($b == 'Trident') {
                $browser = 'Internet Explorer'; // Trident is IE's engine
            }
            break;
        }
    }

    $platform = 'Unknown';
    foreach ($platforms as $p) {
        if (stripos($userAgent, $p) !== false) {
            $platform = $p;
            break;
        }
    }

    return [
        'browser' => $browser,
        'platform' => $platform
    ];
}

$browserInfo = getBrowserInfo($userAgent);

// Geolocation using a free API
$geoData = @file_get_contents("http://ip-api.com/json/$ipaddr");
$geoInfo = $geoData ? json_decode($geoData, true) : ['error' => 'Geolocation unavailable'];

// Prepare data for logging
$logData = "IP: " . $ipaddr . "\r\n";
$logData .= "User-Agent: " . $userAgent . "\r\n";
$logData .= "Browser: " . $browserInfo['browser'] . "\r\n";
$logData .= "Platform: " . $browserInfo['platform'] . "\r\n";

if (!isset($geoInfo['error'])) {
    $logData .= "Geolocation:\r\n";
    $logData .= "  Country: " . $geoInfo['country'] . "\r\n";
    $logData .= "  Region: " . $geoInfo['regionName'] . "\r\n";
    $logData .= "  City: " . $geoInfo['city'] . "\r\n";
    $logData .= "  Latitude: " . $geoInfo['lat'] . "\r\n";
    $logData .= "  Longitude: " . $geoInfo['lon'] . "\r\n";
    $logData .= "  ISP: " . $geoInfo['isp'] . "\r\n";
} else {
    $logData .= "Geolocation: " . $geoInfo['error'] . "\r\n";
}

// Log data to file
$fp = fopen('ip.txt', 'a');
fwrite($fp, $logData . "\r\n");
fclose($fp);

// If not blocked, display a welcome message
echo "Welcome! Your browser and platform details have been logged.";

?>
