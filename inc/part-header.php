<?php

defined('ABSPATH') || exit;
?>
<div class="vmc-top-strip bg-light border-bottom">
    <div class="container">
        <nav class="navbar navbar-light px-0 py-2">
            <div class="d-none d-lg-flex w-100">
                <?php
                wp_nav_menu([
                    'theme_location' => 'primary',
                    'container' => false,
                    'menu_class' => 'menu navbar-nav flex-row ms-auto mb-0 gap-3 align-items-center',
                    'fallback_cb' => 'vmc_top_menu_fallback',
                    'depth' => 4,
                    'walker' => class_exists('justg_WP_Bootstrap_Navwalker') ? new justg_WP_Bootstrap_Navwalker() : null,
                ]);
                ?>
            </div>

            <div class="d-flex d-lg-none w-100 align-items-center justify-content-between">
                <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#vmcHeaderPrimaryNavMobile" aria-controls="vmcHeaderPrimaryNavMobile" aria-label="<?php echo esc_attr__('Buka menu', 'justg'); ?>">
                    <span class="navbar-toggler-icon"></span>
                </button>
            </div>
        </nav>

        <div class="offcanvas offcanvas-end d-lg-none border-0" tabindex="-1" id="vmcHeaderPrimaryNavMobile" aria-labelledby="vmcHeaderPrimaryNavMobileLabel">
            <div class="offcanvas-header border-bottom">
                <h2 class="offcanvas-title h6 mb-0" id="vmcHeaderPrimaryNavMobileLabel"><?php esc_html_e('Menu', 'justg'); ?></h2>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" data-bs-target="#vmcHeaderPrimaryNavMobile" aria-label="<?php echo esc_attr__('Tutup menu', 'justg'); ?>"></button>
            </div>
            <div class="offcanvas-body">
                <?php
                wp_nav_menu([
                    'theme_location' => 'primary',
                    'container' => false,
                    'menu_class' => 'menu navbar-nav flex-column mb-0',
                    'fallback_cb' => 'vmc_top_menu_fallback',
                    'depth' => 4,
                    'walker' => class_exists('justg_WP_Bootstrap_Navwalker') ? new justg_WP_Bootstrap_Navwalker() : null,
                ]);
                ?>
            </div>
        </div>
    </div>
</div>

<div class="vmc-main-strip py-3">
    <div class="container">
        <div class="row g-3 align-items-center">
            <div class="col-12 col-lg-3 text-center text-lg-start">
                <div class="vmc-brand d-inline-block">
                <?php if (has_custom_logo()) : ?>
                    <?php echo get_custom_logo(); ?>
                <?php else : ?>
                    <a href="<?php echo esc_url(home_url('/')); ?>" class="vmc-brand__link"><span class="vmc-brand__text"><?php bloginfo('name'); ?></span></a>
                <?php endif; ?>
                </div>
            </div>

            <div class="col-12 col-lg">
                <?php echo vmc_search_form(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
            </div>

            <div class="col-12 col-lg-auto">
                <div class="vmc-main-strip__actions d-flex align-items-center justify-content-center justify-content-lg-end flex-nowrap gap-1 text-primary pb-1">
                    <?php echo vmc_shortcuts_html(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                </div>
            </div>
        </div>
    </div>
</div>
