<?php

defined('ABSPATH') || exit;

add_action('after_setup_theme', 'vmc_theme_setup', 9);
add_action('customize_register', 'vmc_customize_register', 40);

function vmc_theme_setup()
{
}

function vmc_sanitize_slide_rows($value)
{
    return vmc_sanitize_repeater_json($value, [
        'image_id' => 'image',
        'url' => 'url',
    ]);
}

function vmc_sanitize_category_rows($value)
{
    return vmc_sanitize_repeater_json($value, [
        'term_id' => 'term',
        'icon' => 'text',
    ]);
}

function vmc_product_category_choices()
{
    $choices = [];

    $terms = get_terms([
        'taxonomy' => 'store_product_cat',
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
        'title' => __('Slider & Banner', 'justg'),
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
            'url' => ['type' => 'url', 'label' => __('Link', 'justg')],
        ],
    ]));

    foreach ([
        'vmc_spotlight_image' => __('Banner Tengah', 'justg'),
    ] as $setting_id => $label) {
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
        'title' => __('Promo & Terlaris', 'justg'),
        'panel' => $panel,
        'priority' => 20,
    ]);

    $wp_customize->add_setting('vmc_promo_card_image', [
        'default' => '',
        'sanitize_callback' => 'absint',
    ]);
    $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize, 'vmc_promo_card_image', [
        'label' => __('Gambar Blok Kiri', 'justg'),
        'section' => 'vmc_home_features',
        'mime_type' => 'image',
    ]));

    $wp_customize->add_setting('vmc_promo_card_url', [
        'default' => '',
        'sanitize_callback' => 'esc_url_raw',
    ]);
    $wp_customize->add_control('vmc_promo_card_url', [
        'type' => 'url',
        'label' => __('Link Blok Kiri', 'justg'),
        'section' => 'vmc_home_features',
    ]);

    $wp_customize->add_section('vmc_home_categories', [
        'title' => __('Kategori Chips', 'justg'),
        'panel' => $panel,
        'priority' => 30,
    ]);

    $wp_customize->add_setting('vmc_category_rows', [
        'default' => wp_json_encode([]),
        'sanitize_callback' => 'vmc_sanitize_category_rows',
    ]);

    $wp_customize->add_control(new VMC_Repeater_Control($wp_customize, 'vmc_category_rows', [
        'label' => __('Chips Kategori', 'justg'),
        'section' => 'vmc_home_categories',
        'item_label' => __('Kategori', 'justg'),
        'add_button_label' => __('Tambah Kategori', 'justg'),
        'fields' => [
            'term_id' => ['type' => 'select', 'label' => __('Kategori Produk', 'justg'), 'choices' => vmc_product_category_choices()],
            'icon' => ['type' => 'text', 'label' => __('Nama Icon Bootstrap', 'justg'), 'description' => __('Contoh: chat, bag, lightning, headset.<br> Daftar icon tersedia di https://icons.getbootstrap.com/', 'justg')],
        ],
    ]));

    $wp_customize->add_section('vmc_home_products', [
        'title' => __('Produk Home', 'justg'),
        'panel' => $panel,
        'priority' => 40,
    ]);

    $wp_customize->add_section('vmc_home_about', [
        'title' => __('Tentang Kami', 'justg'),
        'panel' => $panel,
        'priority' => 50,
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

    $wp_customize->add_setting('vmc_about_title', [
        'default' => __('Tentang Kami', 'justg'),
        'sanitize_callback' => 'sanitize_text_field',
    ]);

    $wp_customize->add_control('vmc_about_title', [
        'label' => __('Judul Section', 'justg'),
        'section' => 'vmc_home_about',
        'type' => 'text',
    ]);

    $wp_customize->add_setting('vmc_about_content', [
        'default' => '',
        'sanitize_callback' => 'sanitize_textarea_field',
    ]);

    $wp_customize->add_control('vmc_about_content', [
        'label' => __('Isi Tentang Kami', 'justg'),
        'description' => __('Konten ini akan tampil di bagian paling bawah halaman depan.', 'justg'),
        'section' => 'vmc_home_about',
        'type' => 'textarea',
    ]);
}
