<?php

defined('ABSPATH') || exit;
?>
<div class="vmc-top-strip bg-light py-2">
    <div class="container d-flex align-items-center">
        <div class="d-flex align-items-center justify-content-end justify-content-md-end justify-content-start overflow-auto min-vh-0 w-100">
            <?php
            wp_nav_menu([
                'theme_location' => 'primary',
                'container' => false,
                'menu_class' => 'menu list-unstyled d-flex flex-nowrap flex-md-wrap gap-3 gap-md-4 justify-content-start justify-content-md-end mb-0 w-100 small',
                'fallback_cb' => 'vmc_top_menu_fallback',
                'depth' => 1,
            ]);
            ?>
        </div>
    </div>
</div>

<div class="vmc-main-strip py-3">
    <div class="container">
        <div class="row g-3 align-items-center">
            <div class="col-lg-3 col-md-4 col-12 text-center text-md-start">
                <div class="vmc-brand d-inline-block">
                <?php if (has_custom_logo()) : ?>
                    <?php echo get_custom_logo(); ?>
                <?php else : ?>
                    <a href="<?php echo esc_url(home_url('/')); ?>" class="vmc-brand__link"><span class="vmc-brand__text"><?php bloginfo('name'); ?></span></a>
                <?php endif; ?>
                </div>
            </div>

            <div class="col-lg col-md-8">
                <form class="vmc-search-form" action="<?php echo esc_url(vmc_product_search_url()); ?>" method="get">
                    <div class="input-group border rounded-3 overflow-hidden bg-white">
                        <input type="text" class="form-control border-0" name="search" value="<?php echo isset($_GET['search']) ? esc_attr((string) wp_unslash($_GET['search'])) : ''; ?>" placeholder="<?php echo esc_attr__('Cari Produk', 'justg'); ?>" required>
                        <button type="submit" class="btn btn-light text-primary fw-semibold">
                            <span class="me-1"><?php echo vmc_bootstrap_svg('search'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
                        </button>
                    </div>
                </form>
            </div>

            <div class="col-lg-auto col-12">
                <div class="vmc-main-strip__actions d-flex align-items-center justify-content-center justify-content-lg-end flex-wrap gap-3 text-primary overflow-auto pb-1">
                    <?php echo vmc_shortcuts_html(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                </div>
            </div>
        </div>
    </div>
</div>
