<?php

defined('ABSPATH') || exit;

if (!class_exists('VMC_Repeater_Control') && class_exists('WP_Customize_Control')) {
    class VMC_Repeater_Control extends WP_Customize_Control
    {
        public $type = 'vmc_repeater';
        public $fields = [];
        public $item_label = '';
        public $add_button_label = '';

        public function __construct($manager, $id, $args = [], $options = [])
        {
            if (isset($args['fields'])) {
                $this->fields = (array) $args['fields'];
                unset($args['fields']);
            }
            if (isset($args['item_label'])) {
                $this->item_label = (string) $args['item_label'];
                unset($args['item_label']);
            }
            if (isset($args['add_button_label'])) {
                $this->add_button_label = (string) $args['add_button_label'];
                unset($args['add_button_label']);
            }

            parent::__construct($manager, $id, $args, $options);
        }

        protected function render_content()
        {
            if (empty($this->fields)) {
                return;
            }

            $value = $this->value();
            if (is_string($value)) {
                $decoded = json_decode($value, true);
                $value = json_last_error() === JSON_ERROR_NONE ? $decoded : [];
            }
            if (!is_array($value)) {
                $value = [];
            }

            $encoded_value = wp_json_encode($value);
            if (!$encoded_value) {
                $encoded_value = '[]';
            }
            ?>
            <div class="vmc-repeater-control">
                <?php if (!empty($this->label)) : ?>
                    <span class="customize-control-title"><?php echo esc_html($this->label); ?></span>
                <?php endif; ?>

                <?php if (!empty($this->description)) : ?>
                    <p class="description"><?php echo wp_kses_post($this->description); ?></p>
                <?php endif; ?>

                <div class="vmc-repeater" data-fields="<?php echo esc_attr(wp_json_encode($this->fields)); ?>">
                    <input type="hidden" class="vmc-repeater-store" <?php $this->link(); ?> value="<?php echo esc_attr($encoded_value); ?>">
                    <div class="vmc-repeater-items">
                        <?php foreach ($value as $item) : ?>
                            <?php echo $this->item_markup($item); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                        <?php endforeach; ?>
                    </div>
                    <button type="button" class="button button-primary vmc-repeater-add">
                        <?php echo esc_html($this->add_button_label !== '' ? $this->add_button_label : __('Tambah Item', 'justg')); ?>
                    </button>
                    <script type="text/html" class="vmc-repeater-template">
                        <?php echo $this->item_markup([]); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                    </script>
                </div>
            </div>
            <?php
        }

        private function item_markup($values)
        {
            $values = is_array($values) ? $values : [];

            ob_start();
            ?>
            <div class="vmc-repeater-item">
                <div class="vmc-repeater-fields">
                    <?php foreach ($this->fields as $field_key => $field) : ?>
                        <?php
                        $type = isset($field['type']) ? (string) $field['type'] : 'text';
                        $label = isset($field['label']) ? (string) $field['label'] : '';
                        $description = isset($field['description']) ? (string) $field['description'] : '';
                        $default = isset($field['default']) ? (string) $field['default'] : '';
                        $current = isset($values[$field_key]) ? $values[$field_key] : $default;
                        ?>
                        <div class="vmc-repeater-field">
                            <label>
                                <span class="vmc-repeater-label"><?php echo esc_html($label); ?></span>
                                <?php if ($type === 'image') : ?>
                                    <?php $image_id = absint($current); ?>
                                    <?php $image_url = $image_id ? wp_get_attachment_image_url($image_id, 'thumbnail') : ''; ?>
                                    <input type="hidden" data-field="<?php echo esc_attr($field_key); ?>" value="<?php echo esc_attr((string) $image_id); ?>">
                                    <div class="vmc-repeater-media-preview<?php echo $image_url ? ' has-image' : ''; ?>">
                                        <?php if ($image_url) : ?>
                                            <img src="<?php echo esc_url($image_url); ?>" alt="">
                                        <?php endif; ?>
                                    </div>
                                    <div class="vmc-repeater-media-actions">
                                        <button type="button" class="button vmc-repeater-media-select"><?php esc_html_e('Pilih Gambar', 'justg'); ?></button>
                                        <button type="button" class="button-link-delete vmc-repeater-media-remove"><?php esc_html_e('Hapus', 'justg'); ?></button>
                                    </div>
                                <?php elseif ($type === 'select') : ?>
                                    <select data-field="<?php echo esc_attr($field_key); ?>">
                                        <?php foreach ((array) ($field['choices'] ?? []) as $choice_value => $choice_label) : ?>
                                            <option value="<?php echo esc_attr((string) $choice_value); ?>" <?php selected((string) $current, (string) $choice_value); ?>>
                                                <?php echo esc_html((string) $choice_label); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                <?php else : ?>
                                    <input type="<?php echo esc_attr($type); ?>" data-field="<?php echo esc_attr($field_key); ?>" value="<?php echo esc_attr((string) $current); ?>">
                                <?php endif; ?>
                            </label>
                            <?php if ($description !== '') : ?>
                                <p class="description"><?php echo wp_kses_post($description); ?></p>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="vmc-repeater-actions">
                    <button type="button" class="button vmc-repeater-clone"><?php esc_html_e('Clone', 'justg'); ?></button>
                    <button type="button" class="button button-secondary vmc-repeater-remove"><?php esc_html_e('Hapus', 'justg'); ?></button>
                </div>
            </div>
            <?php

            return ob_get_clean();
        }
    }
}

function vmc_get_no_image_url()
{
    return trailingslashit(get_stylesheet_directory_uri()) . 'img/no-image.webp';
}

function vmc_bootstrap_svg($slug, $class = '')
{
    $slug = sanitize_key((string) $slug);
    $class = trim((string) $class);

    $icons = [
        'search' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0"/></svg>',
        'plus' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M8 4a.5.5 0 0 1 .5.5V7.5H11.5a.5.5 0 0 1 0 1H8.5V11.5a.5.5 0 0 1-1 0V8.5H4.5a.5.5 0 0 1 0-1H7.5V4.5A.5.5 0 0 1 8 4"/></svg>',
        'star-fill' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187z"/></svg>',
    ];

    $svg = isset($icons[$slug]) ? $icons[$slug] : $icons['star-fill'];
    if ($class === '') {
        return $svg;
    }

    return preg_replace('/<svg /', '<svg class="' . esc_attr($class) . '" ', $svg, 1);
}

function vmc_top_menu_fallback()
{
    $terms = get_terms([
        'taxonomy' => 'vmp_product_cat',
        'hide_empty' => true,
        'number' => 6,
    ]);

    if (is_wp_error($terms) || empty($terms)) {
        return;
    }

    echo '<ul class="menu list-unstyled d-flex flex-wrap gap-4 justify-content-end mb-0">';
    foreach ($terms as $term) {
        echo '<li><a href="' . esc_url(get_term_link($term)) . '">' . esc_html($term->name) . '</a></li>';
    }
    echo '</ul>';
}

function vmc_thumbnail_html($post_id, $args = [])
{
    $post_id = absint($post_id);
    if ($post_id <= 0) {
        return '';
    }

    $defaults = [
        'ratio' => '1x1',
        'wrapper_class' => '',
        'image_class' => 'w-100 h-100 object-fit-cover',
        'size' => 'large',
        'link' => get_permalink($post_id),
        'alt' => get_the_title($post_id),
    ];
    $args = wp_parse_args($args, $defaults);

    $image_url = get_the_post_thumbnail_url($post_id, (string) $args['size']);
    if (!$image_url) {
        $image_url = vmc_get_no_image_url();
    }

    $ratio_class = 'ratio ratio-' . sanitize_html_class((string) $args['ratio']);
    $wrapper_class = trim($ratio_class . ' ' . (string) $args['wrapper_class']);

    return '<div class="' . esc_attr($wrapper_class) . '"><a href="' . esc_url((string) $args['link']) . '"><img src="' . esc_url($image_url) . '" class="' . esc_attr((string) $args['image_class']) . '" alt="' . esc_attr((string) $args['alt']) . '" loading="lazy" decoding="async"></a></div>';
}

function vmc_marketplace_setting_url($type)
{
    if (class_exists('\\VelocityMarketplace\\Support\\Settings')) {
        switch ($type) {
            case 'profile':
                return \VelocityMarketplace\Support\Settings::profile_url();
            case 'tracking':
                return \VelocityMarketplace\Support\Settings::tracking_url();
        }
    }

    switch ($type) {
        case 'profile':
            return home_url('/account/');
        case 'tracking':
            return home_url('/order-tracking/');
        default:
            return home_url('/');
    }
}

function vmc_product_search_url()
{
    $archive = get_post_type_archive_link('vmp_product');
    return $archive ?: home_url('/');
}

function vmc_product_card($product_id)
{
    $product_id = absint($product_id);
    if ($product_id <= 0 || get_post_type($product_id) !== 'vmp_product') {
        return '';
    }

    if (shortcode_exists('vmp_product_card')) {
        return do_shortcode('[vmp_product_card id="' . $product_id . '"]');
    }

    return '';
}

function vmc_products_query($args = [])
{
    $defaults = [
        'post_type' => 'vmp_product',
        'post_status' => 'publish',
        'posts_per_page' => 6,
        'ignore_sticky_posts' => true,
    ];

    return new WP_Query(wp_parse_args($args, $defaults));
}

function vmc_pagination($query)
{
    if (!($query instanceof WP_Query) || (int) $query->max_num_pages <= 1) {
        return '';
    }

    $links = paginate_links([
        'total' => (int) $query->max_num_pages,
        'current' => max(1, (int) get_query_var('paged')),
        'type' => 'array',
        'prev_text' => '&laquo;',
        'next_text' => '&raquo;',
    ]);

    if (empty($links)) {
        return '';
    }

    $html = '<nav class="vmc-pagination-nav mt-4"><ul class="pagination justify-content-center">';
    foreach ($links as $link) {
        $active = strpos($link, 'current') !== false ? ' active' : '';
        $html .= '<li class="page-item' . $active . '">' . str_replace('page-numbers', 'page-link', $link) . '</li>';
    }
    $html .= '</ul></nav>';

    return $html;
}

function vmc_shortcuts_html()
{
    $items = [];

    $sell_url = add_query_arg(['tab' => 'seller_home'], vmc_marketplace_setting_url('profile'));
    $items[] = '<a href="' . esc_url(is_user_logged_in() ? $sell_url : wp_login_url($sell_url)) . '" class="vmc-quick-link"><span class="vmc-quick-link__icon">' . vmc_bootstrap_svg('plus') . '</span><span>' . esc_html__('Jual Barang', 'justg') . '</span></a>';

    if (shortcode_exists('vmp_cart')) {
        $items[] = do_shortcode('[vmp_cart class="vmc-quick-icon"]'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }
    if (shortcode_exists('vmp_notifications_icon')) {
        $items[] = do_shortcode('[vmp_notifications_icon class="vmc-quick-icon"]'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }
    if (shortcode_exists('vmp_messages_icon')) {
        $items[] = do_shortcode('[vmp_messages_icon class="vmc-quick-icon"]'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }
    if (shortcode_exists('vmp_profile_icon')) {
        $items[] = do_shortcode('[vmp_profile_icon class="vmc-quick-icon"]'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }

    return implode('', $items);
}

function vmc_get_json_setting($key, $default = [])
{
    $value = get_theme_mod($key, null);
    if ($value === null) {
        return $default;
    }

    if (is_string($value)) {
        $decoded = json_decode($value, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return $decoded;
        }
    }

    return is_array($value) ? $value : $default;
}

function vmc_sanitize_repeater_json($value, $fields)
{
    if (is_string($value)) {
        $decoded = json_decode($value, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            $value = $decoded;
        }
    }

    if (!is_array($value)) {
        return wp_json_encode([]);
    }

    $clean = [];
    foreach ($value as $item) {
        if (!is_array($item)) {
            continue;
        }

        $row = [];
        foreach ($fields as $key => $type) {
            $raw = $item[$key] ?? '';
            switch ($type) {
                case 'image':
                    $row[$key] = absint($raw);
                    break;
                case 'url':
                    $row[$key] = esc_url_raw((string) $raw);
                    break;
                case 'text':
                    $row[$key] = sanitize_text_field((string) $raw);
                    break;
                default:
                    $row[$key] = absint($raw);
                    break;
            }
        }

        if (array_filter($row, static function ($field) {
            return $field !== '' && $field !== 0;
        })) {
            $clean[] = $row;
        }
    }

    return wp_json_encode($clean);
}

function vmc_feature_rows()
{
    return vmc_get_json_setting('vmc_feature_rows', []);
}

function vmc_category_rows()
{
    return vmc_get_json_setting('vmc_category_rows', []);
}

function vmc_slide_rows()
{
    return vmc_get_json_setting('vmc_slide_rows', []);
}
