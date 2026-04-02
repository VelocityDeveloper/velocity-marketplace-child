<?php

defined('ABSPATH') || exit;

add_action('wp_enqueue_scripts', 'vmc_enqueue_assets', 30);
add_action('customize_controls_enqueue_scripts', 'vmc_enqueue_customizer_assets');

function vmc_enqueue_assets()
{
    $theme = wp_get_theme();
    $parent = $theme->parent();
    $parent_version = $parent ? $parent->get('Version') : '1.0.0';
    $child_version = $theme->get('Version');
    $custom_css = get_stylesheet_directory() . '/css/custom.css';

    wp_enqueue_style(
        'velocity-parent-style',
        get_template_directory_uri() . '/style.css',
        [],
        $parent_version
    );

    wp_enqueue_style(
        'velocity-marketplace-child-style',
        get_stylesheet_directory_uri() . '/css/custom.css',
        ['velocity-parent-style'],
        file_exists($custom_css) ? (string) filemtime($custom_css) : $child_version
    );

    wp_enqueue_style(
        'velocity-marketplace-child-root',
        get_stylesheet_uri(),
        ['velocity-marketplace-child-style'],
        $child_version
    );
}

function vmc_enqueue_customizer_assets()
{
    $theme = wp_get_theme();
    $version = $theme ? $theme->get('Version') : '1.0.0';
    $repeater_js = get_stylesheet_directory() . '/js/customizer-repeater.js';
    $repeater_css = get_stylesheet_directory() . '/css/customizer-repeater.css';

    wp_enqueue_media();

    wp_enqueue_script(
        'vmc-customizer-repeater',
        get_stylesheet_directory_uri() . '/js/customizer-repeater.js',
        ['jquery', 'customize-controls', 'media-editor', 'media-views'],
        file_exists($repeater_js) ? (string) filemtime($repeater_js) : $version,
        true
    );

    wp_enqueue_style(
        'vmc-customizer-repeater',
        get_stylesheet_directory_uri() . '/css/customizer-repeater.css',
        [],
        file_exists($repeater_css) ? (string) filemtime($repeater_css) : $version
    );
}
