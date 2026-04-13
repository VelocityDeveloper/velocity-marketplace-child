<?php

defined('ABSPATH') || exit;

add_action('after_setup_theme', 'vmc_override_parent_shell', 20);

function vmc_override_parent_shell()
{
    remove_action('justg_header_open', 'justg_header_open', 20);
    remove_action('justg_header', 'justg_header_menu');
    remove_action('justg_header_close', 'justg_header_close');
    remove_action('justg_do_footer', 'justg_the_footer_open');
    remove_action('justg_do_footer', 'justg_the_footer_content');
    remove_action('justg_do_footer', 'justg_the_footer_close');
    remove_action('justg_before_content', 'justg_left_sidebar_check', 9);
    remove_action('justg_after_content', 'justg_right_sidebar_check', 9);
    remove_action('justg_before_title', 'justg_breadcrumb');
    remove_action('justg_top_content', 'justg_breadcrumb');

    add_action('justg_header_open', 'vmc_render_header_open', 20);
    add_action('justg_header', 'vmc_render_header');
    add_action('justg_header_close', 'vmc_render_header_close');
    add_action('justg_do_footer', 'vmc_render_footer');
}

function vmc_render_header_open()
{
    echo '<header id="wrapper-header" class="bg-header header-full relative">';
    echo '<div id="wrapper-navbar" class="vmc-header-shell" itemscope itemtype="http://schema.org/WebSite">';
}

function vmc_render_header()
{
    require get_stylesheet_directory() . '/inc/part-header.php';
}

function vmc_render_header_close()
{
    echo '</div></header>';
}

function vmc_render_footer()
{
    require get_stylesheet_directory() . '/inc/part-footer.php';
}


function vmc_unregister_sidebar() {
	unregister_sidebar( 'main-sidebar' );
}
add_action( 'widgets_init', 'vmc_unregister_sidebar', 11 );