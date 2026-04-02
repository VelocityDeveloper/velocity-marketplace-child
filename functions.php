<?php

defined('ABSPATH') || exit;

$vmc_inc = get_stylesheet_directory() . '/inc';
$vmc_includes = [
    '/helpers.php',
    '/enqueue.php',
    '/customizer.php',
    '/template-hooks.php',
];

foreach ($vmc_includes as $vmc_file) {
    $vmc_path = $vmc_inc . $vmc_file;
    if (file_exists($vmc_path)) {
        require_once $vmc_path;
    }
}
