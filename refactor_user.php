<?php
$content = file_get_contents('resources/views/user.blade.php');

// 1. Extract CSS
preg_match('/<style>(.*?)<\/style>/s', $content, $cssMatch);
if (!empty($cssMatch)) {
    $css = trim($cssMatch[1]);
    @mkdir('public/css/user', 0777, true);
    file_put_contents('public/css/user/style.css', $css);
    $content = preg_replace('/<style>.*?<\/style>/s', '<link rel="stylesheet" href="{{ asset(\'css/user/style.css\') }}?v=' . time() . '">', $content);
}

// 2. Extract JS
preg_match('/<script>(.*?)<\/script>/s', $content, $jsMatch);
if (!empty($jsMatch)) {
    $js = trim($jsMatch[1]);
    @mkdir('public/js/user', 0777, true);
    file_put_contents('public/js/user/script.js', $js);
    $content = preg_replace('/<script>.*?<\/script>/s', '<script src="{{ asset(\'js/user/script.js\') }}?v=' . time() . '"></script>', $content);
}

// 3. Extract HTML parts based on comments
$parts = [
    'user_inst_bar' => '/<!-- INSTITUTIONAL TOP BAR -->(.*?)<!-- TOPBAR -->/s',
    'user_topbar' => '/<!-- TOPBAR -->(.*?)<!-- HERO -->/s',
    'user_hero' => '/<!-- HERO -->(.*?)<!-- MAIN -->/s',
    'user_footer' => '/<!-- FOOTER -->(.*?)(<script|<link|<\/body>)/s'
];

@mkdir('resources/views/user', 0777, true);

// Extract footer first to avoid messing up the end
preg_match('/<!-- FOOTER -->(.*?)((?:<script|<link|<\/body>))/s', $content, $footerMatch);
if (!empty($footerMatch)) {
    file_put_contents('resources/views/user/footer.blade.php', trim($footerMatch[1]));
    $content = str_replace($footerMatch[1], "\n@include('user.footer')\n", $content);
}

// Extract hero
preg_match('/<!-- HERO -->(.*?)<!-- MAIN -->/s', $content, $heroMatch);
if (!empty($heroMatch)) {
    file_put_contents('resources/views/user/hero.blade.php', trim($heroMatch[1]));
    $content = str_replace($heroMatch[1], "\n@include('user.hero')\n", $content);
}

// Extract topbar
preg_match('/<!-- TOPBAR -->(.*?)<!-- HERO -->/s', $content, $topbarMatch);
if (!empty($topbarMatch)) {
    file_put_contents('resources/views/user/topbar.blade.php', trim($topbarMatch[1]));
    $content = str_replace($topbarMatch[1], "\n@include('user.topbar')\n", $content);
}

// Extract inst_bar
preg_match('/<!-- INSTITUTIONAL TOP BAR -->(.*?)<!-- TOPBAR -->/s', $content, $instMatch);
if (!empty($instMatch)) {
    file_put_contents('resources/views/user/inst_bar.blade.php', trim($instMatch[1]));
    $content = str_replace($instMatch[1], "\n@include('user.inst_bar')\n", $content);
}

// Save back
file_put_contents('resources/views/user.blade.php', $content);
echo "User page refactored successfully.\n";
