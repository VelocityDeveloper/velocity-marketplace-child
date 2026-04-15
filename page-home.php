<?php

/*
* Template Name: Marketplace: Home Page
*/

defined('ABSPATH') || exit;

get_header();

$slides        = vmc_slide_rows();
$category_rows = vmc_category_rows();
$promo_image   = wp_get_attachment_image_url((int) get_theme_mod('vmc_promo_card_image', 0), 'full');
$promo_url     = (string) get_theme_mod('vmc_promo_card_url', home_url('/'));
$premium_title = (string) get_theme_mod('vmc_premium_title', __('Produk Premium', 'justg'));
$premium_limit = max(1, (int) get_theme_mod('vmc_premium_limit', 6));
$latest_title  = (string) get_theme_mod('vmc_latest_title', __('Produk Terbaru', 'justg'));
$latest_limit  = max(1, (int) get_option('posts_per_page', 10));
$about_title   = (string) get_theme_mod('vmc_about_title', __('Tentang Kami', 'justg'));
$about_content = (string) get_theme_mod('vmc_about_content', '');
$blog_title    = __('Blog', 'justg');
$blog_limit    = 2;
$paged = max(
    1,
    (int) get_query_var('paged'),
    (int) get_query_var('page')
);

$premium_query = vmc_products_query([
    'posts_per_page' => $premium_limit,
    'meta_key'       => 'is_premium',
    'meta_value'     => '1',
    'orderby'        => 'rand',
]);

$latest_query = vmc_products_query([
    'posts_per_page' => $latest_limit,
    'paged'          => $paged,
    'orderby'        => 'date',
    'order'          => 'DESC',
]);

$top_sellers_query = vmc_products_query([
    'posts_per_page' => 4,
    'meta_key'       => 'hit',
    'orderby'        => ['meta_value_num' => 'DESC', 'date' => 'DESC'],
    'order'          => 'DESC',
]);

$blog_args = [
    'post_type'      => 'post',
    'post_status'    => 'publish',
    'posts_per_page' => $blog_limit,
];
$blog_query = new WP_Query($blog_args);
$has_top_sellers = $top_sellers_query->have_posts();
$has_slider = !empty($slides);
$has_category_chips = !empty($category_rows);
$has_discovery_row = $promo_image || $has_top_sellers;
$promo_col_class = $has_top_sellers ? 'col-lg-6' : 'col-12';
$top_col_class = $promo_image ? 'col-lg-6' : 'col-12';
?>

<div class="wrapper" id="page-wrapper">
    <div class="container">
        <?php if ($has_slider) : ?>
            <section class="vmc-home__section mb-5">
                <div class="row g-3">
                    <div class="col-12">
                        <div id="vmc-home-slider" class="carousel slide" data-bs-ride="carousel">
                            <div class="carousel-inner">
                                <?php foreach ($slides as $index => $row) : ?>
                                    <?php $image_url = !empty($row['image_id']) ? wp_get_attachment_image_url((int) $row['image_id'], 'full') : ''; ?>
                                    <?php if (!$image_url) { continue; } ?>
                                    <div class="carousel-item<?php echo $index === 0 ? ' active' : ''; ?>">
                                        <a href="<?php echo esc_url((string) ($row['url'] ?? home_url('/'))); ?>" class="d-block text-decoration-none">
                                            <img src="<?php echo esc_url($image_url); ?>" class="w-100 rounded" alt="" loading="lazy" decoding="async">
                                        </a>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <button class="carousel-control-prev w-auto" type="button" data-bs-target="#vmc-home-slider" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="visually-hidden"><?php esc_html_e('Previous', 'justg'); ?></span>
                            </button>
                            <button class="carousel-control-next w-auto" type="button" data-bs-target="#vmc-home-slider" data-bs-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="visually-hidden"><?php esc_html_e('Next', 'justg'); ?></span>
                            </button>
                        </div>
                    </div>
                </div>
            </section>
        <?php endif; ?>

        <?php if ($has_discovery_row || $has_category_chips) : ?>
            <section class="vmc-home__section mb-5">
                <div class="card shadow border-light">
                    <div class="card-body p-3">
                        <?php if ($has_discovery_row) : ?>
                            <div class="row g-4 align-items-stretch">
                                <?php if ($promo_image) : ?>
                                    <div class="<?php echo esc_attr($promo_col_class); ?>">
                                        <a href="<?php echo esc_url($promo_url); ?>" class="d-block text-decoration-none">
                                            <img src="<?php echo esc_url($promo_image); ?>" class="w-100 rounded" alt="" loading="lazy" decoding="async">
                                        </a>
                                    </div>
                                <?php endif; ?>
                                <?php if ($has_top_sellers) : ?>
                                    <div class="<?php echo esc_attr($top_col_class); ?>">
                                        <div class="h-100">
                                            <div class="d-flex align-items-center justify-content-between mb-3">
                                                <h2 class="h4 fw-bold mb-0"><?php esc_html_e('Banyak Dilihat', 'justg'); ?></h2>
                                            </div>
                                            <div class="row row-cols-2 row-cols-md-4 g-3">
                                                <?php while ($top_sellers_query->have_posts()) : $top_sellers_query->the_post(); ?>
                                                    <?php
                                                    $top_product_id = get_the_ID();
                                                    $top_product_link = get_permalink($top_product_id);
                                                    $top_product_title = get_the_title($top_product_id);
                                                    ?>
                                                    <div class="col">
                                                        <article class="h-100">
                                                            <a href="<?php echo esc_url($top_product_link); ?>" class="d-block text-decoration-none">
                                                                <div class="ratio ratio-1x1 border rounded overflow-hidden bg-white mb-2">
                                                                    <img src="<?php echo esc_url(get_the_post_thumbnail_url($top_product_id, 'medium') ?: vmc_get_no_image_url()); ?>" class="w-100 h-100 object-fit-cover" alt="<?php echo esc_attr($top_product_title); ?>" loading="lazy" decoding="async">
                                                                </div>
                                                                <span class="d-block small fw-semibold text-dark vmc-line-clamp-2"><?php echo esc_html($top_product_title); ?></span>
                                                            </a>
                                                        </article>
                                                    </div>
                                                <?php endwhile; ?>
                                                <?php wp_reset_postdata(); ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($has_category_chips) : ?>
                            <div class="vmc-home__chips-wrap">
                                <div class="vmc-home__chips" role="list" aria-label="<?php esc_attr_e('Kategori populer', 'justg'); ?>">
                                    <?php foreach ($category_rows as $row) : ?>
                                        <?php
                                        $term_id = isset($row['term_id']) ? (int) $row['term_id'] : 0;
                                        $term = $term_id > 0 ? get_term($term_id, 'store_product_cat') : null;
                                        $icon = isset($row['icon']) ? sanitize_html_class((string) $row['icon']) : '';
                                        if (!$term || is_wp_error($term)) {
                                            continue;
                                        }
                                        ?>
                                        <a href="<?php echo esc_url(get_term_link($term)); ?>" class="vmc-home__chip" role="listitem">
                                            <?php if ($icon !== '') : ?>
                                                <span class="vmc-home__chip-icon" aria-hidden="true"><i class="bi bi-<?php echo esc_attr($icon); ?>"></i></span>
                                            <?php endif; ?>
                                            <span><?php echo esc_html($term->name); ?></span>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </section>
        <?php endif; ?>

        <?php if ($premium_query->have_posts()) : ?>
            <section class="vmc-home__section mb-5">
                <h2 class="h4 fw-bold mb-3"><?php echo vmc_bootstrap_svg('star-fill', 'me-2 text-warning'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php echo esc_html($premium_title); ?></h2>
                <div class="vmc-product-grid">
                    <?php while ($premium_query->have_posts()) : $premium_query->the_post(); ?>
                        <?php echo vmc_product_card(get_the_ID()); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                    <?php endwhile; ?>
                    <?php wp_reset_postdata(); ?>
                </div>
            </section>
        <?php endif; ?>

        <?php if ($latest_query->have_posts()) : ?>
            <section class="vmc-home__section mb-5">
                <h2 class="h4 fw-bold mb-3"><?php echo esc_html($latest_title); ?></h2>
                <div class="vmc-product-grid">
                    <?php while ($latest_query->have_posts()) : $latest_query->the_post(); ?>
                        <?php echo vmc_product_card(get_the_ID()); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                    <?php endwhile; ?>
                    <?php wp_reset_postdata(); ?>
                </div>
                <?php echo vmc_pagination($latest_query); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
            </section>
        <?php endif; ?>

        <?php if ($blog_query->have_posts()) : ?>
            <section class="vmc-home__section mb-5">
                <h2 class="h4 fw-bold mb-3"><?php echo esc_html($blog_title); ?></h2>
                <div class="row g-4">
                    <?php while ($blog_query->have_posts()) : $blog_query->the_post(); ?>
                        <div class="col-md-6">
                            <article class="card h-100 shadow-sm overflow-hidden">
                                <div class="row g-0">
                                    <div class="col-4 col-sm-5">
                                        <?php echo vmc_thumbnail_html(get_the_ID(), ['ratio' => '4x3', 'wrapper_class' => 'h-100']); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                                    </div>
                                    <div class="col-8 col-sm-7">
                                        <div class="card-body p-4">
                                            <h3 class="h5 mb-2 fw-bold"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                                                <div class="text-muted small mb-2">
                                                    <?php echo get_the_date(); ?>
                                                </div>
                                            <div class="d-none d-md-block"><?php echo esc_html(wp_trim_words(wp_strip_all_tags(get_the_excerpt()), 13)); ?></div>
                                        </div>
                                    </div>
                                </div>
                            </article>
                        </div>
                    <?php endwhile; ?>
                    <?php wp_reset_postdata(); ?>
                </div>
            </section>
        <?php endif; ?>

        <?php
        $terms = get_terms([
            'taxonomy'   => 'store_product_cat',
            'hide_empty' => false,
            'orderby'    => 'name',
            'order'      => 'ASC',
            'parent'     => 0,
        ]);

        if (!is_wp_error($terms) && !empty($terms)) :
        ?>
        <div class="vmc-categories__section my-4 pt-5 border-top">
            <h2 class="h4 fw-bold mb-3">Kategori</h2>
            <div class="row">
                <?php foreach ($terms as $term) : ?>
                    <div class="col-6 col-lg-3">
                        <a href="<?php echo esc_url(get_term_link($term)); ?>" class="text-decoration-none text-secondary d-block border-bottom pb-2 mb-2">
                            <?php echo esc_html($term->name); ?>
                            <?php echo ' (' . $term->count . ')'; ?>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <?php if (trim(wp_strip_all_tags($about_content)) !== '') : ?>
            <section class="vmc-home__section pt-5">
                <h2 class="h4 fw-bold mb-3"><?php echo esc_html($about_title); ?></h2>
                <div class="vmc-about-content">
                    <?php echo wp_kses_post(wpautop($about_content)); ?>
                </div>
            </section>
        <?php endif; ?>
    </div>
</div>

<?php
get_footer();
