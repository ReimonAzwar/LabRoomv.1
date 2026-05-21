<?php
$c = file_get_contents('resources/views/admin_panel.blade.php');
preg_match_all('/<!--(.*?)-->/', $c, $m);
print_r($m[1]);
