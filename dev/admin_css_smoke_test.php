<?php
// Simple smoke tester for admin CSS wiring.
// Usage: php admin_css_smoke_test.php http://localhost/GRADLINK

if ($argc < 2) {
    echo "Usage: php admin_css_smoke_test.php <BASE_URL> [--auth=user:pass]\n";
    exit(1);
}

$base = rtrim($argv[1], '/');

// optional auth arg: --auth=user:pass
$auth = null;
foreach ($argv as $a) {
    if (strpos($a, '--auth=') === 0) {
        $auth = substr($a, 7);
    }
}

$pages = [
    '/admin' => ['must' => ['css/admin/common.css','css/admin/dashboard-common.css','css/admin/dashboard-overview.css']],
    '/admin/posts' => ['must' => ['css/admin/common.css','css/admin/posts.css']],
    '/admin/users' => ['must' => ['css/admin/common.css','css/admin/dashboard-common.css']],
    '/admin/reports' => ['must' => ['css/admin/common.css','css/admin/dashboard-common.css']],
    '/admin/engagement' => ['must' => ['css/admin/common.css','css/admin/dashboard-common.css']],
    '/admin/verifications' => ['must' => ['css/admin/common.css','css/admin/dashboard-common.css']],
    '/admin/fundraisers' => ['must' => ['css/admin/common.css','css/admin/dashboard-common.css','css/admin/posts.css']],
    // login page: require common.css (login.css is preferred but common.css is critical)
    '/adminlogin' => ['must' => ['css/admin/common.css']],
];

echo "Admin CSS smoke test against base URL: $base\n";
if ($auth) echo "Using auth: [REDACTED]\n";
echo "\n";

$summary = ['pass'=>0,'fail'=>0];

// helper: fetch a URL, follow redirects and return ['html'=>..., 'final_url'=>...]
function fetch_url($url, $cookieFile = null, $post = null) {
    if (function_exists('curl_version')) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 8);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Gradlink-SmokeTest/1.0');
        if ($cookieFile) {
            curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
            curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
        }
        if ($post !== null) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        }
        $html = curl_exec($ch);
        $final = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
        $err = curl_error($ch);
        curl_close($ch);
        if ($html === false) return ['error' => $err ?: 'cURL error', 'html' => '', 'final' => $final ?? $url];
        return ['error' => null, 'html' => $html, 'final' => $final];
    }

    // fallback to file_get_contents (no redirect following)
    $opts = ['http'=>['timeout'=>8, 'method'=>'GET']];
    $context = stream_context_create($opts);
    $html = @file_get_contents($url, false, $context);
    return ['error' => $html === false ? 'fetch error' : null, 'html' => $html === false ? '' : $html, 'final' => $url];
}

// if auth provided, attempt a login and persist cookies in temp file
$cookieFile = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'gradlink_admin_cookie.txt';
if ($auth) {
    list($user, $pass) = explode(':', $auth) + [null, null];
    if (!$user || !$pass) {
        echo "Invalid --auth format. Use --auth=user:pass\n";
        exit(1);
    }
    $loginUrl = $base . '/adminlogin';
    echo "Attempting login to: $loginUrl ... ";
    $post = http_build_query(['email' => $user, 'password' => $pass]);
    $r = fetch_url($loginUrl, $cookieFile, $post);
    if ($r['error']) {
        echo "ERROR during login: {$r['error']}\n";
        // continue, maybe pages are public
    } else {
        // check if login succeeded by checking if returned page contains typical login marker
        if (stripos($r['html'], 'ADMINISTRATIVE LOGIN') !== false || stripos($r['final'], '/admin') !== false) {
            // if we still see login form, warn but continue
            echo "(login form returned)\n";
        } else {
            echo "OK\n";
        }
    }
}

foreach ($pages as $path => $expect) {
    $url = $base . $path;
    echo "Checking: $url ... ";
    $r = fetch_url($url, $cookieFile);
    if ($r['error']) {
        echo "ERROR fetching ({$r['error']})\n";
        $summary['fail']++;
        continue;
    }
    $html = $r['html'];
    // detect if admin login was returned (not authenticated)
    $isLogin = false;
    if ($html && (stripos($html, 'ADMINISTRATIVE LOGIN') !== false || stripos($html, 'admin-badge') !== false && stripos($html, 'ADMIN PANEL') !== false)) {
        // quick heuristic: page contains the login form text
        $isLogin = true;
    }

    // find stylesheet hrefs (links anywhere)
    preg_match_all('/<link[^>]+rel=["\']stylesheet["\'][^>]*>/i', $html, $links);
    $hrefs = [];
    foreach ($links[0] as $tag) {
        if (preg_match('/href=["\']([^"\']+)["\']/i', $tag, $m)) {
            $hrefs[] = $m[1];
        }
    }

    if ($isLogin && $path !== '/admin/login') {
        echo "REDIRECTED TO LOGIN (not authenticated)\n";
        $summary['fail']++;
        continue;
    }

    $missing = [];
    foreach ($expect['must'] as $need) {
        $found = false;
        foreach ($hrefs as $h) {
            if (strpos($h, $need) !== false) { $found = true; break; }
        }
        if (!$found) $missing[] = $need;
    }
    if (empty($missing)) {
        echo "OK\n";
        $summary['pass']++;
    } else {
        echo "MISSING: " . implode(', ', $missing) . "\n";
        echo "  Styles found: \n" . implode("\n  - ", $hrefs) . "\n";
        $summary['fail']++;
    }
}

echo "\nSummary: Passed {$summary['pass']}, Failed {$summary['fail']}\n";

if ($summary['fail'] > 0) exit(2);
exit(0);
