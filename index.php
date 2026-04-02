<?php

defined('ABSPATH') || exit;

if (!is_front_page()) {
    get_header();
    $container = function_exists('velocitytheme_option') ? velocitytheme_option('justg_container_type', 'container') : 'container';
    ?>
    <div class="wrapper" id="index-wrapper">
        <div class="<?php echo esc_attr($container); ?>" id="content" tabindex="-1">
            <div class="row">
                <?php do_action('justg_before_content'); ?>
                <main class="site-main" id="main">
                    <?php if (have_posts()) : ?>
                        <?php while (have_posts()) : the_post(); ?>
                            <?php get_template_part('loop-templates/content', get_post_format()); ?>
                        <?php endwhile; ?>
                    <?php else : ?>
                        <?php get_template_part('loop-templates/content', 'none'); ?>
                    <?php endif; ?>
                    <?php if (function_exists('justg_pagination')) { justg_pagination(); } ?>
                </main>
                <?php do_action('justg_after_content'); ?>
            </div>
        </div>
    </div>
    <?php
    get_footer();
    return;
}

get_header();

$slides        = vmc_slide_rows();
$feature_rows  = vmc_feature_rows();
$category_rows = vmc_category_rows();
$premium_title = (string) get_theme_mod('vmc_premium_title', __('Produk Premium', 'justg'));
$premium_limit = max(1, (int) get_theme_mod('vmc_premium_limit', 6));
$latest_title  = (string) get_theme_mod('vmc_latest_title', __('Produk Terbaru', 'justg'));
$latest_limit  = max(1, (int) get_option('posts_per_page', 10));
$blog_title    = (string) get_theme_mod('vmc_blog_title', __('Blog', 'justg'));
$blog_limit    = max(1, (int) get_theme_mod('vmc_blog_limit', 2));
$blog_category = (int) get_theme_mod('vmc_blog_category', 0);
$paged         = max(1, (int) get_query_var('paged'));

$premium_query = vmc_products_query([
    'posts_per_page' => $premium_limit,
    'meta_key'       => 'is_premium',
    'meta_value'     => '1',
    'orderby'        => ['meta_value_num' => 'DESC', 'date' => 'DESC'],
]);

$latest_query = vmc_products_query([
    'posts_per_page' => $latest_limit,
    'paged'          => $paged,
    'orderby'        => 'date',
    'order'          => 'DESC',
]);

$blog_args = [
    'post_type'      => 'post',
    'post_status'    => 'publish',
    'posts_per_page' => $blog_limit,
];
if ($blog_category > 0) {
    $blog_args['cat'] = $blog_category;
}
$blog_query = new WP_Query($blog_args);
?>

<div class="vmc-home">
    <div class="container">
        <section class="vmc-home__section">
            <div class="row g-3 align-items-stretch">
                <div class="col-lg-8">
                    <?php if (!empty($slides)) : ?>
                        <div id="vmc-home-slider" class="carousel slide" data-bs-ride="carousel">
                            <div class="carousel-inner">
                                <?php foreach ($slides as $index => $row) : ?>
                                    <?php $image_url = !empty($row['image_id']) ? wp_get_attachment_image_url((int) $row['image_id'], 'full') : ''; ?>
                                    <?php if (!$image_url) { continue; } ?>
                                    <div class="carousel-item<?php echo $index === 0 ? ' active' : ''; ?>">
                                        <a href="<?php echo esc_url((string) ($row['url'] ?? home_url('/'))); ?>" class="vmc-home__visual-link">
                                            <div class="ratio ratio-16x9">
                                                <img src="<?php echo esc_url($image_url); ?>" class="w-100 h-100 object-fit-cover" alt="" loading="lazy" decoding="async">
                                            </div>
                                        </a>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <button class="carousel-control-prev" type="button" data-bs-target="#vmc-home-slider" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="visually-hidden"><?php esc_html_e('Previous', 'justg'); ?></span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#vmc-home-slider" data-bs-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="visually-hidden"><?php esc_html_e('Next', 'justg'); ?></span>
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="col-lg-4">
                    <div class="d-grid gap-3 h-100">
                        <?php foreach (['vmc_side_visual_top_image', 'vmc_side_visual_bottom_image'] as $setting_id) : ?>
                            <?php $image_url = wp_get_attachment_image_url((int) get_theme_mod($setting_id, 0), 'full'); ?>
                            <?php if (!$image_url) { continue; } ?>
                            <a href="<?php echo esc_url((string) get_theme_mod($setting_id . '_url', home_url('/'))); ?>" class="vmc-home__visual-link h-100">
                                <div class="ratio ratio-16x9">
                                    <img src="<?php echo esc_url($image_url); ?>" class="w-100 h-100 object-fit-cover" alt="" loading="lazy" decoding="async">
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </section>

        <?php if (!empty($feature_rows)) : ?>
            <section class="vmc-home__section vmc-home__features">
                <div class="vmc-home__feature-grid">
                    <?php foreach ($feature_rows as $row) : ?>
                        <?php $image_url = !empty($row['image_id']) ? wp_get_attachment_image_url((int) $row['image_id'], 'medium') : ''; ?>
                        <?php if (!$image_url) { continue; } ?>
                        <a href="<?php echo esc_url((string) ($row['url'] ?? home_url('/'))); ?>" class="vmc-home__feature-card">
                            <img src="<?php echo esc_url($image_url); ?>" class="img-fluid" alt="" loading="lazy" decoding="async">
                            <?php if (!empty($row['title'])) : ?>
                                <div class="mt-2"><?php echo esc_html((string) $row['title']); ?></div>
                            <?php endif; ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>

        <?php $category_banner = wp_get_attachment_image_url((int) get_theme_mod('vmc_spotlight_image', 0), 'full'); ?>
        <?php if ($category_banner) : ?>
            <section class="vmc-home__section">
                <a href="<?php echo esc_url((string) get_theme_mod('vmc_spotlight_image_url', home_url('/'))); ?>" class="vmc-home__spotlight-link">
                    <div class="ratio ratio-21x9">
                        <img src="<?php echo esc_url($category_banner); ?>" class="w-100 h-100 object-fit-cover" alt="" loading="lazy" decoding="async">
                    </div>
                </a>
            </section>
        <?php endif; ?>

        <?php if (!empty($category_rows)) : ?>
            <section class="vmc-home__section">
                <h2 class="h3 mb-3"><?php echo esc_html((string) get_theme_mod('vmc_category_title', __('Kategori', 'justg'))); ?></h2>
                <div class="vmc-home__categories">
                    <?php foreach ($category_rows as $row) : ?>
                        <?php
                        $term_id   = isset($row['term_id']) ? (int) $row['term_id'] : 0;
                        $term      = $term_id > 0 ? get_term($term_id, 'vmp_product_cat') : null;
                        $image_url = !empty($row['image_id']) ? wp_get_attachment_image_url((int) $row['image_id'], 'medium') : '';
                        if (!$term || is_wp_error($term) || !$image_url) {
                            continue;
                        }
                        ?>
                        <a href="<?php echo esc_url(get_term_link($term)); ?>" class="vmc-home__category-card">
                            <div class="ratio ratio-1x1 mb-3">
                                <img src="<?php echo esc_url($image_url); ?>" class="w-100 h-100 object-fit-contain" alt="<?php echo esc_attr($term->name); ?>" loading="lazy" decoding="async">
                            </div>
                            <div><?php echo esc_html($term->name); ?></div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>

        <?php if ($premium_query->have_posts()) : ?>
            <section class="vmc-home__section">
                <h2 class="h3 mb-3"><?php echo vmc_bootstrap_svg('star-fill', 'me-2 text-warning'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php echo esc_html($premium_title); ?></h2>
                <div class="vmc-product-grid">
                    <?php while ($premium_query->have_posts()) : $premium_query->the_post(); ?>
                        <?php echo vmc_product_card(get_the_ID()); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                    <?php endwhile; ?>
                    <?php wp_reset_postdata(); ?>
                </div>
            </section>
        <?php endif; ?>

        <?php if ($latest_query->have_posts()) : ?>
            <section class="vmc-home__section">
                <h2 class="h3 mb-3"><?php echo esc_html($latest_title); ?></h2>
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
            <section class="vmc-home__section">
                <h2 class="h3 mb-3"><?php echo esc_html($blog_title); ?></h2>
                <div class="row g-4">
                    <?php while ($blog_query->have_posts()) : $blog_query->the_post(); ?>
                        <div class="col-md-6">
                            <article class="vmc-home__blog-card">
                                <div class="row g-0">
                                    <div class="col-sm-5">
                                        <?php echo vmc_thumbnail_html(get_the_ID(), ['ratio' => '4x3', 'wrapper_class' => 'h-100']); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                                    </div>
                                    <div class="col-sm-7">
                                        <div class="vmc-home__blog-body">
                                            <h3 class="h5 mb-2"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                                            <div><?php echo esc_html(wp_trim_words(wp_strip_all_tags(get_the_excerpt()), 16)); ?></div>
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
    </div>
</div>

<?php
get_footer();
