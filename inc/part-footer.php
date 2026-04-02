<?php

defined('ABSPATH') || exit;

$widget_areas = [
    'footer-widget-1',
    'footer-widget-2',
    'footer-widget-3',
    'footer-widget-4',
];

$active_areas = array_values(array_filter($widget_areas, 'is_active_sidebar'));
if (empty($active_areas)) {
    return;
}
?>
<div id="wrapper-footer" class="vmc-footer-shell">
    <div class="container">
        <footer class="vmc-footer row g-4">
            <?php
            $column_class = 'col-lg-3 col-md-6';
            if (count($active_areas) === 3) {
                $column_class = 'col-lg-4 col-md-6';
            } elseif (count($active_areas) === 2) {
                $column_class = 'col-lg-6 col-md-6';
            } elseif (count($active_areas) === 1) {
                $column_class = 'col-12';
            }
            ?>
            <?php foreach ($active_areas as $sidebar_id) : ?>
                <div class="<?php echo esc_attr($column_class); ?>">
                    <?php dynamic_sidebar($sidebar_id); ?>
                </div>
            <?php endforeach; ?>
        </footer>
    </div>
</div>
