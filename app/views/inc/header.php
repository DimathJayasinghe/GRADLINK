<!DOCTYPE html>
 <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title><?php echo SITENAME?></title>
        <link rel="shortcut icon" href="<?php echo URLROOT ?>/img/favicon_white.png" type="image/x-icon">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="<?php echo URLROOT;?>/css/color-pallate.css">
        <link rel="stylesheet" href="<?php echo URLROOT;?>/css/style.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <?php if (!empty($styles)) { echo $styles; } ?>

    </head>
    <body>
<?php
// Provide small polyfill for older PHP versions
if (!function_exists('str_starts_with')) {
    function str_starts_with($haystack, $needle) {
        return substr($haystack, 0, strlen($needle)) === $needle;
    }
}
?>

<?php

// Only output FAB styles for logged-in admins on non-admin pages
$currentPath = $_SERVER['REQUEST_URI'];
$isAdminPage = strpos($currentPath, '/admin') !== false;


// Show FAB only if NOT an admin page and user is admin
if (!$isAdminPage && SessionManager::hasRole('admin')): ?>
    <button id="fab" class="fab" onclick="onFabClick()"><i class="fas fa-user-cog"></i></button>

    <script>
        function onFabClick() {
            window.location.href = '<?php echo URLROOT; ?>/admin/dashboard';
        }
    </script>

    <style>
        /* Use the project's system palette variables when available.
           Fallback colors are provided for environments without variables. */
        :root {
            --fab-bg: var(--btn);
            --fab-bg-hover: var(--link);
            --fab-text: var(--btn-text);
            --fab-size: 56px;
            --fab-offset: 25px;
        }

        .fab {
            position: fixed;
            z-index: 9999;
            bottom: var(--fab-offset);
            right: var(--fab-offset);
            background-color: var(--fab-bg);
            color: var(--fab-text);
            opacity: 0.9;
            border: none;
            border-radius: 15px;
            width: var(--fab-size);
            height: var(--fab-size);
            font-size: 28px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.12);
            cursor: pointer;
            transition: transform .18s ease, box-shadow .18s ease, background-color .18s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        /* Hover and keyboard focus styles */
        .fab:hover,
        .fab:focus {
            background-color: var(--fab-bg-hover);
            transform: scale(1.05);
            outline: none;
        }

        /* Prefer a subtle focus ring for keyboard users */
        .fab:focus-visible {
            box-shadow: 0 0 0 3px rgba(0,123,255,0.18);
        }

        /* Respect reduced motion preferences */
        @media (prefers-reduced-motion: reduce) {
            .fab {
                transition: none;
                transform: none;
            }
        }
    </style>
<?php endif; ?>
