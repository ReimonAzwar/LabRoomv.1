<?php
$content = file_get_contents('resources/views/admin_panel.blade.php');

// 1. Extract CSS
preg_match('/<style>(.*?)<\/style>/s', $content, $cssMatch);
if (!empty($cssMatch)) {
    $css = trim($cssMatch[1]);
    @mkdir('public/css/admin', 0777, true);
    file_put_contents('public/css/admin/style.css', $css);
    $content = preg_replace('/<style>.*?<\/style>/s', '<link rel="stylesheet" href="{{ asset(\'css/admin/style.css\') }}?v=' . time() . '">', $content);
}

// 2. Extract JS
preg_match('/<script>(.*?)<\/script>/s', $content, $jsMatch);
if (!empty($jsMatch)) {
    $js = trim($jsMatch[1]);
    @mkdir('public/js/admin', 0777, true);
    file_put_contents('public/js/admin/script.js', $js);
    $content = preg_replace('/<script>.*?<\/script>/s', '<script src="{{ asset(\'js/admin/script.js\') }}?v=' . time() . '"></script>', $content);
}

// 3. Extract Modals
@mkdir('resources/views/admin', 0777, true);
preg_match('/<!-- MODALS -->(.*?)(?:<script|<link|<\/body>)/s', $content, $modalMatch);
if (!empty($modalMatch)) {
    $modals = trim($modalMatch[1]);
    file_put_contents('resources/views/admin/modals.blade.php', $modals);
    $content = str_replace($modalMatch[1], "\n@include('admin.modals')\n", $content);
}

// Save back
file_put_contents('resources/views/admin_panel.blade.php', $content);
echo "Admin page refactored successfully.\n";
