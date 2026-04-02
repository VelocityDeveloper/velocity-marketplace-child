<?php

defined('ABSPATH') || exit;
?>
<div class="vmc-top-strip">
    <div class="container">
        <div class="d-flex justify-content-end align-items-center min-vh-0">
            <?php
            wp_nav_menu([
                'theme_location' => 'vmc_top_menu',
                'container' => false,
                'menu_class' => 'menu list-unstyled d-flex flex-wrap gap-4 justify-content-end mb-0',
                'fallback_cb' => 'vmc_top_menu_fallback',
                'depth' => 1,
            ]);
            ?>
        </div>
    </div>
</div>

<div class="vmc-main-strip">
    <div class="container">
        <div class="vmc-main-strip__inner">
            <div class="vmc-brand">
                <?php if (has_custom_logo()) : ?>
                    <?php echo get_custom_logo(); ?>
                <?php else : ?>
                    <a href="<?php echo esc_url(home_url('/')); ?>" class="vmc-brand__link"><span class="vmc-brand__text"><?php bloginfo('name'); ?></span></a>
                <?php endif; ?>
            </div>

            <form class="vmc-search-form" action="<?php echo esc_url(vmc_product_search_url()); ?>" method="get">
                <div class="input-group">
                    <input type="text" class="form-control border-0" name="search" value="<?php echo isset($_GET['search']) ? esc_attr((string) wp_unslash($_GET['search'])) : ''; ?>" placeholder="<?php echo esc_attr__('Cari Produk', 'justg'); ?>">
                    <button type="submit" class="btn btn-light text-primary fw-semibold">
                        <span class="me-1"><?php echo vmc_bootstrap_svg('search'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
                        <span><?php esc_html_e('Cari', 'justg'); ?></span>
                    </button>
                </div>
            </form>

            <div class="vmc-main-strip__actions">
                <?php echo vmc_shortcuts_html(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
            </div>
        </div>
    </div>
</div>
