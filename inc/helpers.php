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

                <div class="vmc-repeater" data-fields="<?php echo esc_attr(wp_json_encode($this->fields)); ?>" data-default-label="<?php echo esc_attr($this->item_label !== '' ? $this->item_label : __('Item', 'justg')); ?>">
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
            $summary = $this->item_label !== '' ? $this->item_label : __('Item', 'justg');

            ob_start();
            ?>
            <div class="vmc-repeater-item">
                <button type="button" class="vmc-repeater-toggle" aria-expanded="true">
                    <span class="vmc-repeater-item-label"><?php echo esc_html($summary); ?></span>
                    <span class="vmc-repeater-toggle-icon" aria-hidden="true"></span>
                </button>

                <div class="vmc-repeater-item-body">
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
                                        <?php $image_url = $image_id ? wp_get_attachment_image_url($image_id, 'medium_large') : ''; ?>
                                        <input type="hidden" data-field="<?php echo esc_attr($field_key); ?>" value="<?php echo esc_attr((string) $image_id); ?>">
                                        <div class="vmc-repeater-media-preview<?php echo $image_url ? ' has-image' : ''; ?>">
                                            <?php if ($image_url) : ?>
                                                <img src="<?php echo esc_url($image_url); ?>" alt="">
                                            <?php endif; ?>
                                        </div>
                                        <div class="vmc-repeater-media-actions">
                                            <button type="button" class="button vmc-repeater-media-select"><?php esc_html_e('Pilih Gambar', 'justg'); ?></button>
                                        </div>
                                    <?php elseif ($type === 'select') : ?>
                                        <select data-field="<?php echo esc_attr($field_key); ?>">
                                            <?php foreach ((array) ($field['choices'] ?? []) as $choice_value => $choice_label) : ?>
                                                <option value="<?php echo esc_attr((string) $choice_value); ?>" <?php selected((string) $current, (string) $choice_value); ?>>
                                                    <?php echo esc_html((string) $choice_label); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    <?php elseif ($type === 'textarea') : ?>
                                        <textarea data-field="<?php echo esc_attr($field_key); ?>"><?php echo esc_textarea((string) $current); ?></textarea>
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
        'geo-alt' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-geo-alt" viewBox="0 0 16 16"> <path d="M12.166 8.94c-.524 1.062-1.234 2.12-1.96 3.07A32 32 0 0 1 8 14.58a32 32 0 0 1-2.206-2.57c-.726-.95-1.436-2.008-1.96-3.07C3.304 7.867 3 6.862 3 6a5 5 0 0 1 10 0c0 .862-.305 1.867-.834 2.94M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10"/> <path d="M8 8a2 2 0 1 1 0-4 2 2 0 0 1 0 4m0 1a3 3 0 1 0 0-6 3 3 0 0 0 0 6"/> </svg>',
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
        'taxonomy' => 'store_product_cat',
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
            case 'catalog':
                return \VelocityMarketplace\Support\Settings::catalog_url();
            case 'cart':
                return \VelocityMarketplace\Support\Settings::cart_url();
            case 'checkout':
                return \VelocityMarketplace\Support\Settings::checkout_url();
        }
    }

    $settings = get_option('wp_store_settings', []);
    $page_map = [
        'catalog' => 'page_catalog',
        'profile' => 'page_profile',
        'cart' => 'page_cart',
        'checkout' => 'page_checkout',
        'tracking' => 'page_tracking',
    ];

    if (isset($page_map[$type])) {
        $page_id = isset($settings[$page_map[$type]]) ? absint($settings[$page_map[$type]]) : 0;
        if ($page_id > 0) {
            $url = get_permalink($page_id);
            if ($url) {
                return $url;
            }
        }
    }

    switch ($type) {
        case 'catalog':
            return get_post_type_archive_link('store_product') ?: home_url('/');
        case 'profile':
            return home_url('/profil-saya/');
        case 'cart':
            return home_url('/keranjang/');
        case 'checkout':
            return home_url('/checkout/');
        case 'tracking':
            return home_url('/tracking-order/');
        default:
            return home_url('/');
    }
}

function vmc_product_search_url()
{
    return vmc_marketplace_setting_url('catalog');
}

function vmc_currency_symbol()
{
    if (class_exists('\\VelocityMarketplace\\Support\\Settings')) {
        return (string) \VelocityMarketplace\Support\Settings::currency_symbol();
    }

    $settings = get_option('wp_store_settings', []);
    return isset($settings['currency_symbol']) && is_string($settings['currency_symbol']) && $settings['currency_symbol'] !== ''
        ? (string) $settings['currency_symbol']
        : 'Rp';
}

function vmc_product_payload($product_id)
{
    $product_id = absint($product_id);
    if ($product_id <= 0 || get_post_type($product_id) !== 'store_product') {
        return null;
    }

    if (class_exists('\\VelocityMarketplace\\Modules\\Product\\ProductData')) {
        $item = \VelocityMarketplace\Modules\Product\ProductData::map_post($product_id);
        if (is_array($item) && !empty($item['id'])) {
            return $item;
        }
    }

    if (class_exists('\\WpStore\\Domain\\Product\\ProductData')) {
        $item = \WpStore\Domain\Product\ProductData::map_post($product_id);
        if (is_array($item) && !empty($item['id'])) {
            return $item;
        }
    }

    return null;
}

function vmc_product_price_html($item)
{
    $item = is_array($item) ? $item : [];
    $price = isset($item['price']) && is_numeric($item['price']) ? (float) $item['price'] : null;
    $regular_price = isset($item['regular_price']) && is_numeric($item['regular_price']) ? (float) $item['regular_price'] : null;
    $sale_price = isset($item['sale_price']) && is_numeric($item['sale_price']) ? (float) $item['sale_price'] : null;
    $currency = vmc_currency_symbol();

    if ($price === null || $price < 0) {
        return '';
    }

    $format = static function ($value) use ($currency) {
        return $currency . ' ' . number_format((float) $value, 0, ',', '.');
    };

    if ($sale_price !== null && $sale_price > 0 && $regular_price !== null && $regular_price > $sale_price) {
        return '<div class="d-flex align-items-baseline flex-wrap gap-1">'
            . '<span class="fw-bold text-dark">' . esc_html($format($sale_price)) . '</span>'
            . '<span class="small text-muted text-decoration-line-through opacity-75">' . esc_html($format($regular_price)) . '</span>'
            . '</div>';
    }

    return '<div class="fw-semibold text-dark">' . esc_html($format($price)) . '</div>';
}

function vmc_product_meta_html($item)
{
    $item = is_array($item) ? $item : [];

    $parts = [];

    // Data meta
    $review_count   = isset($item['review_count']) ? max(0, (int) $item['review_count']) : 0;
    $rating_average = isset($item['rating_average']) ? max(0.0, (float) $item['rating_average']) : 0.0;
    $sold_count     = isset($item['sold_count']) ? max(0, (int) $item['sold_count']) : 0;

    $meta_parts = [];

    // Rating
    if ($review_count > 0 && $rating_average > 0) {
        $meta_parts[] =
            '<span class="d-inline-flex align-items-center">' .
                '<span class="me-1 text-warning">' . vmc_bootstrap_svg('star', 'align-middle') . '</span>' .
                '<span class="align-middle lh-sm">' . esc_html(number_format_i18n($rating_average, 1)) . '</span>' .
            '</span>';
    } else {
        $meta_parts[] =
            '<span class="d-inline-flex align-items-center opacity-75">' .
                esc_html__('Belum ada ulasan', 'justg') .
            '</span>';
    }

    // Separator
    if (!empty($meta_parts) && $sold_count > 0) {
        $meta_parts[] = '<span class="mx-1 align-middle">·</span>';
    }

    // Sold count
    if ($sold_count > 0) {
        $meta_parts[] = '<span class="align-middle lh-sm">' . esc_html(vmc_format_sold_count($sold_count) . ' terjual') . '</span>';
    }

    // Baris 1: rating + sold
    if (!empty($meta_parts)) {
        $parts[] = '<div class="small text-muted d-flex align-items-center flex-wrap">' . implode('', $meta_parts) . '</div>';
    }

    // Baris 2: lokasi
    if (!empty($item['seller_city'])) {
        $icon = vmc_bootstrap_svg('geo-alt', 'me-1 align-middle');
        $parts[] =
            '<div class="small text-uppercase text-muted mt-1">' .
                $icon .
                '<span class="align-middle">' . esc_html((string) $item['seller_city']) . '</span>' .
            '</div>';
    }

    return implode('', $parts);
}

function vmc_format_sold_count($count)
{
    $count = (int) $count;

    if ($count >= 1000000000) {
        $value = floor($count / 100000000) / 10;
        return rtrim(rtrim(number_format($value, 1, '.', ''), '0'), '.') . 'rb+';
    }

    if ($count >= 1000) {
        $value = $count / 1000;

        if (floor($value) == $value) {
            return number_format_i18n($value, 0) . 'rb+';
        }

        return str_replace('.', ',', number_format($value, 1)) . 'rb+';
    }

    return number_format_i18n($count);
}

function vmc_product_card($product_id)
{
    $product_id = absint($product_id);
    $item = vmc_product_payload($product_id);
    if (!$item || empty($item['id'])) {
        return '';
    }

    $link = isset($item['link']) ? (string) $item['link'] : get_permalink($product_id);
    $title = isset($item['title']) && is_string($item['title']) && $item['title'] !== '' ? (string) $item['title'] : __('Produk', 'justg');
    $price_html = vmc_product_price_html($item);
    $meta_html = vmc_product_meta_html($item);

    if (shortcode_exists('vmp_add_to_cart')) {
        $action_html = do_shortcode('[vmp_add_to_cart id="' . $product_id . '" text="" class="btn btn-primary btn-sm w-100"]');
    } elseif (shortcode_exists('wp_store_add_to_cart')) {
        $action_html = do_shortcode('[wp_store_add_to_cart id="' . $product_id . '" text="" class="btn btn-primary btn-sm w-100 d-inline-flex align-items-center justify-content-center"]');
    } else {
        $action_html = '';
    }

    $thumb_url = '';
    if (!empty($item['image']) && is_string($item['image'])) {
        $thumb_url = (string) $item['image'];
    } else {
        $thumb_url = vmc_get_no_image_url();
    }

    $html = '<article class="card h-100 border-0 shadow-sm overflow-hidden vmc-product-card">';
    $html .= '<div class="vmp-product-image position-relative">';
        $html .= '<a href="' . esc_url($link) . '" class="ratio ratio-1x1 d-block text-decoration-none bg-light">';
            $html .= '<img src="' . esc_url($thumb_url) . '" class="w-100 h-100 object-fit-cover" alt="' . esc_attr($title) . '" loading="lazy" decoding="async">';
        $html .= '</a>';
        $html .= vmp_premium_badge_html([
            'post_id' => $product_id,
            'class' => 'badge bg-warning text-dark position-absolute start-0 top-0 ms-2 mt-2',
        ]);
    $html .= '</div>';
    $html .= '<div class="card-body">';
    $html .= '<h3 class="h6 mb-2 lh-sm"><a href="' . esc_url($link) . '" class="text-dark text-decoration-none vmc-line-clamp-2">' . esc_html($title) . '</a></h3>';
    if ($price_html !== '') {
        $html .= '<div class="mb-2 fw-bold">' . $price_html . '</div>';
    }
    if ($meta_html !== '') {
        $html .= '<div class="mb-3">' . $meta_html . '</div>';
    }
    if ($action_html !== '') {
        $html .= '<div class="mt-auto">' . $action_html . '</div>';
    }
    $html .= '</div></article>';

    return $html;
}

function vmc_top_seller_card($product_id)
{
    $product_id = absint($product_id);
    if ($product_id <= 0 || get_post_type($product_id) !== 'store_product') {
        return '';
    }

    $link = get_permalink($product_id);
    if (!$link) {
        return '';
    }

    $title = get_the_title($product_id);
    if ($title === '') {
        $title = __('Produk', 'justg');
    }

    $thumb = vmc_thumbnail_html($product_id, [
        'ratio' => '1x1',
        'wrapper_class' => 'vmc-top-seller-card__thumb',
        'image_class' => 'w-100 h-100 object-fit-cover',
        'size' => 'medium',
        'link' => $link,
        'alt' => $title,
    ]);

    return '<a href="' . esc_url($link) . '" class="vmc-top-seller-card">'
        . $thumb
        . '<span class="vmc-top-seller-card__title">' . esc_html($title) . '</span>'
        . '</a>';
}

function vmc_products_query($args = [])
{
    $defaults = [
        'post_type' => 'store_product',
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

    if (class_exists('\\VelocityMarketplace\\Modules\\Account\\Account')) {
        $sell_url = add_query_arg(['tab' => 'seller_products'], vmc_marketplace_setting_url('profile'));
    } else {
        $sell_url = admin_url('post-new.php?post_type=store_product');
    }
    $items[] = '<a href="' . esc_url(is_user_logged_in() ? $sell_url : wp_login_url($sell_url)) . '" class="vmc-quick-link btn btn-link text-primary text-decoration-none p-0 border-0 shadow-none d-inline-flex align-items-center justify-content-center gap-2" aria-label="' . esc_attr__('Jual Barang', 'justg') . '"><span class="vmc-quick-link__icon">' . vmc_bootstrap_svg('plus') . '</span><span class="d-none d-xl-inline">' . esc_html__('Jual Barang', 'justg') . '</span></a>';

    if (shortcode_exists('vmp_cart')) {
        $items[] = do_shortcode('[vmp_cart class="vmc-quick-icon"]'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    } else if (shortcode_exists('wp_store_cart')) {
        $items[] = '<span class="vmc-quick-icon vmc-quick-icon--cart">' . do_shortcode('[wp_store_cart]') . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }
    if (shortcode_exists('vmp_notifications_icon')) {
        $items[] = do_shortcode('[vmp_notifications_icon class="vmc-quick-icon"]'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }
    if (shortcode_exists('vmp_messages_icon')) {
        $items[] = do_shortcode('[vmp_messages_icon class="vmc-quick-icon"]'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }
    if (shortcode_exists('vmp_profile_icon')) {
        $items[] = do_shortcode('[vmp_profile_icon class="vmc-quick-icon"]'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    } elseif (shortcode_exists('wp_store_link_profile')) {
        $items[] = '<span class="vmc-quick-icon vmc-quick-icon--profile">' . do_shortcode('[wp_store_link_profile]') . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
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

function vmc_category_rows()
{
    return vmc_get_json_setting('vmc_category_rows', []);
}

function vmc_slide_rows()
{
    return vmc_get_json_setting('vmc_slide_rows', []);
}
