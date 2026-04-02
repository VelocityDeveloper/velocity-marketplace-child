<?php

defined('ABSPATH') || exit;

add_action('after_setup_theme', 'vmc_theme_setup', 9);
add_action('customize_register', 'vmc_customize_register', 40);

function vmc_theme_setup()
{
    register_nav_menus([
        'vmc_top_menu' => __('Marketplace Top Menu', 'justg'),
    ]);
}

function vmc_sanitize_slide_rows($value)
{
    return vmc_sanitize_repeater_json($value, [
        'image_id' => 'image',
        'url' => 'url',
    ]);
}

function vmc_sanitize_feature_rows($value)
{
    return vmc_sanitize_repeater_json($value, [
        'image_id' => 'image',
        'title' => 'text',
        'url' => 'url',
    ]);
}

function vmc_sanitize_category_rows($value)
{
    return vmc_sanitize_repeater_json($value, [
        'image_id' => 'image',
        'term_id' => 'term',
    ]);
}

function vmc_product_category_choices()
{
    $choices = [];

    $terms = get_terms([
        'taxonomy' => 'vmp_product_cat',
        'hide_empty' => false,
    ]);

    if (is_wp_error($terms) || empty($terms)) {
        return $choices;
    }

    foreach ($terms as $term) {
        $choices[(string) $term->term_id] = $term->name;
    }

    return $choices;
}

function vmc_customize_register(WP_Customize_Manager $wp_customize)
{
    $panel = 'vmc_marketplace_panel';

    $wp_customize->add_panel($panel, [
        'title' => __('Velocity Marketplace', 'justg'),
        'priority' => 22,
    ]);

    $wp_customize->add_section('vmc_home_hero', [
        'title' => __('Home Hero', 'justg'),
        'panel' => $panel,
        'priority' => 10,
    ]);

    $wp_customize->add_setting('vmc_slide_rows', [
        'default' => wp_json_encode([]),
        'sanitize_callback' => 'vmc_sanitize_slide_rows',
    ]);

    $wp_customize->add_control(new VMC_Repeater_Control($wp_customize, 'vmc_slide_rows', [
        'label' => __('Slide Utama', 'justg'),
        'section' => 'vmc_home_hero',
        'item_label' => __('Slide', 'justg'),
        'add_button_label' => __('Tambah Slide', 'justg'),
        'fields' => [
            'image_id' => ['type' => 'image', 'label' => __('Gambar', 'justg')],
            'title' => ['type' => 'text', 'label' => __('Judul', 'justg')],
            'url' => ['type' => 'url', 'label' => __('Link', 'justg')],
        ],
    ]));

    $hero_visuals = [
        'vmc_side_visual_top_image' => __('Visual Kanan Atas', 'justg'),
        'vmc_side_visual_bottom_image' => __('Visual Kanan Bawah', 'justg'),
        'vmc_spotlight_image' => __('Visual Atas Kategori', 'justg'),
    ];

    foreach ($hero_visuals as $setting_id => $label) {
        $wp_customize->add_setting($setting_id, [
            'default' => '',
            'sanitize_callback' => 'absint',
        ]);

        $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize, $setting_id, [
            'label' => $label,
            'section' => 'vmc_home_hero',
            'mime_type' => 'image',
        ]));

        $wp_customize->add_setting($setting_id . '_url', [
            'default' => '',
            'sanitize_callback' => 'esc_url_raw',
        ]);

        $wp_customize->add_control($setting_id . '_url', [
            'type' => 'url',
            'label' => sprintf(__('Link %s', 'justg'), $label),
            'section' => 'vmc_home_hero',
        ]);
    }

    $wp_customize->add_section('vmc_home_features', [
        'title' => __('Home Features', 'justg'),
        'panel' => $panel,
        'priority' => 20,
    ]);

    $wp_customize->add_setting('vmc_feature_rows', [
        'default' => wp_json_encode([]),
        'sanitize_callback' => 'vmc_sanitize_feature_rows',
    ]);

    $wp_customize->add_control(new VMC_Repeater_Control($wp_customize, 'vmc_feature_rows', [
        'label' => __('Gambar Fitur', 'justg'),
        'section' => 'vmc_home_features',
        'item_label' => __('Fitur', 'justg'),
        'add_button_label' => __('Tambah Fitur', 'justg'),
        'fields' => [
            'image_id' => ['type' => 'image', 'label' => __('Gambar', 'justg')],
            'url' => ['type' => 'url', 'label' => __('Link', 'justg')],
        ],
    ]));

    $wp_customize->add_section('vmc_home_categories', [
        'title' => __('Home Categories', 'justg'),
        'panel' => $panel,
        'priority' => 30,
    ]);

    $wp_customize->add_setting('vmc_category_title', [
        'default' => __('Kategori', 'justg'),
        'sanitize_callback' => 'sanitize_text_field',
    ]);

    $wp_customize->add_control('vmc_category_title', [
        'label' => __('Judul Kategori', 'justg'),
        'section' => 'vmc_home_categories',
        'type' => 'text',
    ]);

    $wp_customize->add_setting('vmc_category_rows', [
        'default' => wp_json_encode([]),
        'sanitize_callback' => 'vmc_sanitize_category_rows',
    ]);

    $wp_customize->add_control(new VMC_Repeater_Control($wp_customize, 'vmc_category_rows', [
        'label' => __('Grid Kategori', 'justg'),
        'section' => 'vmc_home_categories',
        'item_label' => __('Kategori', 'justg'),
        'add_button_label' => __('Tambah Kategori', 'justg'),
        'fields' => [
            'image_id' => ['type' => 'image', 'label' => __('Gambar', 'justg')],
            'term_id' => ['type' => 'select', 'label' => __('Kategori Produk', 'justg'), 'choices' => vmc_product_category_choices()],
        ],
    ]));

    $wp_customize->add_section('vmc_home_products', [
        'title' => __('Home Products', 'justg'),
        'panel' => $panel,
        'priority' => 40,
    ]);

    $product_settings = [
        'vmc_premium_title' => [__('Judul Produk Premium', 'justg'), 'text', __('Produk Premium', 'justg')],
        'vmc_premium_limit' => [__('Jumlah Produk Premium', 'justg'), 'number', 6],
        'vmc_latest_title' => [__('Judul Produk Terbaru', 'justg'), 'text', __('Produk Terbaru', 'justg')],
    ];

    foreach ($product_settings as $setting_id => $config) {
        [$label, $type, $default] = $config;
        $wp_customize->add_setting($setting_id, [
            'default' => $default,
            'sanitize_callback' => $type === 'number' ? 'absint' : 'sanitize_text_field',
        ]);

        $wp_customize->add_control($setting_id, [
            'label' => $label,
            'section' => 'vmc_home_products',
            'type' => $type,
        ]);
    }

    $wp_customize->add_section('vmc_home_blog', [
        'title' => __('Home Blog', 'justg'),
        'panel' => $panel,
        'priority' => 50,
    ]);

    $blog_categories = ['' => __('Semua Kategori', 'justg')];
    foreach (get_categories(['hide_empty' => false]) as $category) {
        $blog_categories[(string) $category->term_id] = $category->name;
    }

    $wp_customize->add_setting('vmc_blog_title', [
        'default' => __('Blog', 'justg'),
        'sanitize_callback' => 'sanitize_text_field',
    ]);
    $wp_customize->add_control('vmc_blog_title', [
        'label' => __('Judul Blog', 'justg'),
        'section' => 'vmc_home_blog',
        'type' => 'text',
    ]);

    $wp_customize->add_setting('vmc_blog_category', [
        'default' => '',
        'sanitize_callback' => 'absint',
    ]);
    $wp_customize->add_control('vmc_blog_category', [
        'label' => __('Kategori Post', 'justg'),
        'section' => 'vmc_home_blog',
        'type' => 'select',
        'choices' => $blog_categories,
    ]);

    $wp_customize->add_setting('vmc_blog_limit', [
        'default' => 2,
        'sanitize_callback' => 'absint',
    ]);
    $wp_customize->add_control('vmc_blog_limit', [
        'label' => __('Jumlah Post', 'justg'),
        'section' => 'vmc_home_blog',
        'type' => 'number',
    ]);
}
