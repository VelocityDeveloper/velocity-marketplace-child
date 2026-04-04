<?php

defined('ABSPATH') || exit;

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
