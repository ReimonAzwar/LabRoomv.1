<?php
$c = file_get_contents('resources/views/admin_panel.blade.php');
echo "Styles: " . preg_match_all('/<style[^>]*>/i', $c) . "\n";
echo "Scripts: " . preg_match_all('/<script[^>]*>/i', $c) . "\n";
