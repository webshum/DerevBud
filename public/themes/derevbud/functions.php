<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

// Register theme defaults.
add_action('after_setup_theme', function () {
    show_admin_bar(false);

    add_theme_support('post-thumbnails');
    add_theme_support('title-tag');
    add_theme_support('editor-styles');
    add_theme_support('wp-block-styles');
    add_theme_support('align-wide');
    add_theme_support('woocommerce');

    register_nav_menus([
        'navigation' => __('Navigation'),
        'header' => __('Navigation header'),
    ]);

    register_sidebar(array(
        'name' => 'Соціальні мережі',
        'id'=> 'social',
        'before_widget' => '',
        'after_widget'  => '',
        'before_title'  => '',
        'after_title'   => ''
    ));
});

// Register scripts and styles.
add_action('wp_enqueue_scripts', function () {
    $manifestPath = get_theme_file_path('assets/.vite/manifest.json');

    if (
        wp_get_environment_type() === 'local' &&
        is_array(wp_remote_get('http://localhost:5173/')) // is Vite.js running
    ) {
        wp_enqueue_script('vite', 'http://localhost:5173/@vite/client');
        wp_enqueue_script('wordplate', 'http://localhost:5173/resources/js/index.js');
    } elseif (file_exists($manifestPath)) {
        $manifest = json_decode(file_get_contents($manifestPath), true);
        wp_enqueue_script('wordplate', get_theme_file_uri('assets/' . $manifest['resources/js/index.js']['file']));
        wp_enqueue_style('wordplate', get_theme_file_uri('assets/' . $manifest['resources/js/index.js']['css'][0]));
    }
});

// Load scripts as modules.
add_filter('script_loader_tag', function (string $tag, string $handle, string $src) {
    if (in_array($handle, ['vite', 'wordplate'])) {
        return '<script type="module" src="' . esc_url($src) . '" defer></script>';
    }

    return $tag;
}, 10, 3);

// Show dashboard
add_filter( 'show_admin_bar', 'custom_show_admin_bar' );

function custom_show_admin_bar( $show ) {
    if ( is_user_logged_in() ) {
        return true;
    } else {
        return false;
    }
}

// Remove admin menu items.
add_action('admin_init', function () {
    remove_menu_page('edit-comments.php'); // Comments
    // remove_menu_page('edit.php?post_type=page'); // Pages
    // remove_menu_page('edit.php'); // Posts
    remove_menu_page('index.php'); // Dashboard
    // remove_menu_page('upload.php'); // Media
});

// Remove admin toolbar menu items.
add_action('admin_bar_menu', function (WP_Admin_Bar $menu) {
    $menu->remove_node('archive'); // Archive
    $menu->remove_node('comments'); // Comments
    $menu->remove_node('customize'); // Customize
    $menu->remove_node('dashboard'); // Dashboard
    $menu->remove_node('edit'); // Edit
    $menu->remove_node('menus'); // Menus
    $menu->remove_node('new-content'); // New Content
    $menu->remove_node('search'); // Search
    // $menu->remove_node('site-name'); // Site Name
    $menu->remove_node('themes'); // Themes
    $menu->remove_node('updates'); // Updates
    $menu->remove_node('view-site'); // Visit Site
    $menu->remove_node('view'); // View
    $menu->remove_node('widgets'); // Widgets
    $menu->remove_node('wp-logo'); // WordPress Logo
}, 999);

// Remove admin dashboard widgets.
add_action('wp_dashboard_setup', function () {
    remove_meta_box('dashboard_activity', 'dashboard', 'normal'); // Activity
    // remove_meta_box('dashboard_right_now', 'dashboard', 'normal'); // At a Glance
    remove_meta_box('dashboard_site_health', 'dashboard', 'normal'); // Site Health Status
    remove_meta_box('dashboard_primary', 'dashboard', 'side'); // WordPress Events and News
    remove_meta_box('dashboard_quick_press', 'dashboard', 'side'); // Quick Draft
});

// Remove actions woocommerce
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40);

// Add custom login form logo.
add_action('login_head', function () {
    $url = get_theme_file_uri('favicon.svg');

    $styles = [
        sprintf('background-image: url(%s)', $url),
        'width: 200px',
        'background-position: center',
        'background-size: contain',
    ];

    printf(
        '<style> .login h1 a { %s } </style>',
        implode(';', $styles),
    );
});

// Register custom SMTP credentials.
add_action('phpmailer_init', function (PHPMailer $mailer) {
    $mailer->isSMTP();
    $mailer->SMTPAutoTLS = false;
    $mailer->SMTPAuth = env('MAIL_USERNAME') && env('MAIL_PASSWORD');
    $mailer->SMTPDebug = env('WP_DEBUG') ? SMTP::DEBUG_SERVER : SMTP::DEBUG_OFF;
    $mailer->SMTPSecure = env('MAIL_ENCRYPTION', 'tls');
    $mailer->Debugoutput = 'error_log';
    $mailer->Host = env('MAIL_HOST');
    $mailer->Port = env('MAIL_PORT', 587);
    $mailer->Username = env('MAIL_USERNAME');
    $mailer->Password = env('MAIL_PASSWORD');
    return $mailer;
});

add_filter('wp_mail_from', fn() => env('MAIL_FROM_ADDRESS', 'hello@example.com'));
add_filter('wp_mail_from_name', fn() => env('MAIL_FROM_NAME', 'Example'));

// Update permalink structure.
add_action('after_setup_theme', function () {
    if (get_option('permalink_structure') !== '/%postname%/') {
        update_option('permalink_structure', '/%postname%/');
        flush_rewrite_rules();
    }
});

// Polylang
pll_register_string( 'consultation', 'Consultation', 'DerevBud' );
pll_register_string( 'call_us', 'Call us', 'DerevBud' );
pll_register_string( 'or_leave_number', 'Or live number', 'DerevBud' );
pll_register_string( 'back', 'Back', 'DerevBud' );
pll_register_string( 'send', 'Send', 'DerevBud' );
pll_register_string( 'read_more', 'Read more', 'DerevBud' );
pll_register_string( 'manager_fedback', 'Manager fedback', 'DerevBud' );
pll_register_string( 'when_collback', 'When collback', 'DerevBud' );
pll_register_string( 'tnaks', 'Thanks', 'DerevBud' );
pll_register_string( 'close', 'Close', 'DerevBud' );

// API
add_action('rest_api_init', function() {
    register_rest_route('derevbud/v1', '/attributes', [
        'methods' => 'GET',
        'callback' => 'get_attributes'
    ]);

    register_rest_route('derevbud/v1', 'price', [
        'methods' => 'GET',
        'callback' => 'get_price'
    ]);
});

// Price
function get_price($request) {
    $params = $request->get_params();
    $price_product = get_post_meta($params['id'], 'table_price_product_custom', true);
    $count_product = get_post_meta($params['id'], 'table_count_product_custom', true);

    $arr_data_price = array();

    if ($price_product) {
        $count = 0;
        foreach ($price_product as $price) {
            $arr_data_price[$count]['price'] = $price;
            $count++;
        }
    }

    if ($count_product) {
        $count = 0;
        foreach ($count_product as $val) {
            $arr_data_price[$count]['count'] = array_diff($val, array(null));
            $count++;
        }
    }

    $table_price = get_option('_cs_options');
    $arr_price = array();

    foreach ($arr_data_price as $data) {
        if (!empty($data['price']) && is_array($data['price'])) {
            foreach ($data['price'] as $key => $prc) {
                if (!empty($table_price['product_price_table']) && is_array($table_price['product_price_table'])) {
                    foreach ($table_price['product_price_table'] as $price) {
                        if (in_array($prc, $price)) {
                            $arr_price[] = $price['derevo_price'] * $data['count'][$key];
                        }
                    }
                }
            }
        }
    }

    $result = "<span class='price'>" . min($arr_price) . " <span class='currency-symbol'>грн</span></span>";

    return new WP_REST_Response($result, 200);
}

// Filters attribute
function get_attributes() {
    $attributes = wc_get_attribute_taxonomies();
    $arr_attributes = [];
    
    if ( ! empty( $attributes ) ) {
        foreach ( $attributes as $attribute ) {
            $terms = get_terms( array( 
                'taxonomy' => 'pa_' . $attribute->attribute_name,
                'hide_empty' => true 
            ) );

            $arr_attributes[$attribute->attribute_id] = get_object_vars($attribute);

            if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
                foreach ( $terms as $term ) {
                    $arr_attributes[$attribute->attribute_id]['terms'][] = get_object_vars($term);
                }
            }
        }
    }

    return new WP_REST_Response($arr_attributes, 200);
}

function get_price_html_by_id($id) {
    $price_product = get_post_meta($id, 'table_price_product_custom', true);
    $count_product = get_post_meta($id, 'table_count_product_custom', true);

    $arr_data_price = array();

    if ($price_product) {
        $count = 0;
        foreach ($price_product as $price) {
            $arr_data_price[$count]['price'] = $price;
            $count++;
        }
    }

    if ($count_product) {
        $count = 0;
        foreach ($count_product as $val) {
            $arr_data_price[$count]['count'] = array_diff($val, array(null));
            $count++;
        }
    }

    $table_price = get_option('_cs_options');
    $arr_price = array();

    foreach ($arr_data_price as $data) {
        if (!empty($data['price'])) {
            foreach ($data['price'] as $key => $prc) {
                foreach ($table_price['product_price_table'] as $price) {
                    if (in_array($prc, $price)) {
                        $arr_price[] = $price['derevo_price'] * $data['count'][$key];
                    }
                }
            }
        }
        
    }

    $result = "<span class='price'>" . min($arr_price) . " <span class='currency-symbol'> грн</span></span>";

    return $result;
}

/**
 * ADD PAGE PRICE
 */
function add_admin_menu() {
    add_menu_page(
        'Вартість дерева',
        'Вартість дерева',
        'manage_options',
        'price-tree',
        'price_tree_settings_page',
        'dashicons-menu-alt3'
    );
}
add_action('admin_menu', 'add_admin_menu');

function price_tree_register_settings() {
    register_setting('price_tree_options_group', '_cs_options');
}
add_action('admin_init', 'price_tree_register_settings');

function price_tree_settings_page() {
    $price_product_table = get_option('_cs_options');

    echo '<div class="wrap"><h1>Загальна таблиця цін</h1>';
    echo '<form method="post" action="options.php">';
    settings_fields('price_tree_options_group');
    do_settings_sections('price_tree_options_group');

    if (!empty($price_product_table['product_price_table'])) {
        $prices = $price_product_table['product_price_table'];

        echo "<div class='js-inputs-wrap'>";

        foreach ($prices as $index => $price) {
            ?>

            <div class="js-inputs" data-index="<?php echo $index ?>">
                <h3><?php echo esc_html($price['title_price_urk']); ?></h3>
                <div>
                    <strong>Назва (укр)</strong>
                    <input type="text" name="_cs_options[product_price_table][<?php echo $index; ?>][title_price_urk]" value="<?php echo esc_attr($price['title_price_urk']); ?>">
                </div>
                <div>
                    <strong>Назва (рус)</strong>
                    <input type="text" name="_cs_options[product_price_table][<?php echo $index; ?>][title_price_rus]" value="<?php echo esc_attr($price['title_price_rus']); ?>">
                </div>
                <div>
                    <strong>Ціна</strong>
                    <input type="text" name="_cs_options[product_price_table][<?php echo $index; ?>][derevo_price]" value="<?php echo esc_attr($price['derevo_price']); ?>">
                </div>

                <div class="remove">
                    <button type="button" class="button js-remove-price">Видалити</button>
                </div>
            </div>

            <?php
        }

        echo "</div>";
    }

    echo "<div style='margin-top: 30px; display: flex; gap: 0 15px;'>";
    echo '<button name="add" class="button js-add-price">Додати</button>';
    echo '<input type="submit" name="save" class="button button-primary" value="Зберегти" />';
    echo "</div>";
    echo '</form>';
    echo '</div>';

    ?>
    <style>
        .js-inputs-wrap {
            font-size: 13px;
            font-family: Tahoma, sans-serif;
            max-width: 500px;
        }
        .js-inputs {
            padding-bottom: 15px;
            margin-bottom: 15px;
            border-bottom: 1px solid #ccc;
        }
        .js-inputs h3 {
            font-size: 15px;
            font-weight: 500;
        }
        .js-inputs > div {
            display: flex;
            align-items: center;
        }
        .js-inputs > div + div {margin-top: 10px;}
        .js-inputs > div strong {
            min-width: 100px;
            font-weight: 500;
        }
        .js-inputs > div input {
            width: 100%;
            color: #656565;
            font-size: 13px;
        }
        .js-inputs > div input::placeholder {color: #656565;}
        .js-inputs .remove {
            display: flex;
            justify-content: end;
            margin-top: 15px;
        }
    </style>

    <script>
        window.onload = () => {
            const wrap = document.querySelector('.js-inputs-wrap');
            const btnAdd = document.querySelector('.js-add-price');

            btnAdd.addEventListener('click', e => {
                e.preventDefault();

                let index = Array.from(wrap.querySelectorAll('.js-inputs'))
                .map(input => Number(input.dataset.index))
                .reduce((max, current) => Math.max(max, current), 0) + 1;

                wrap.insertAdjacentHTML('beforeend', `
                    <div class="js-inputs" data-index="${index}">
                        <h3>Новий елемент</h3>
                        <div>
                            <strong>Назва (укр)</strong>
                            <input type="text" name="_cs_options[product_price_table][${index}][title_price_urk]">
                        </div>
                        <div>
                            <strong>Назва (рус)</strong>
                            <input type="text" name="_cs_options[product_price_table][${index}][title_price_rus]">
                        </div>
                        <div>
                            <strong>Ціна</strong>
                            <input type="text" name="_cs_options[product_price_table][${index}][derevo_price]">
                        </div>
                        <div class="remove">
                            <button type="button" class="button js-remove-price">Видалити</button>
                        </div>
                    </div>
                `);

                addRemoveEvent();
            });

            function addRemoveEvent() {
                const removeButtons = document.querySelectorAll('.js-remove-price');
                removeButtons.forEach(button => {
                    button.removeEventListener('click', handleRemove);
                    button.addEventListener('click', handleRemove);
                });
            }

            function handleRemove(e) {
                e.preventDefault();
                e.target.closest('.js-inputs').remove();
            }

            addRemoveEvent();
        };
    </script>

    <?php
}

// Ajax form
function javascript_variables(){ ?>
    <script type="text/javascript">
        const ajax_url = '<?php echo admin_url( "admin-ajax.php" ); ?>';
        const ajax_nonce = '<?php echo wp_create_nonce( "secure_nonce_name" ); ?>';
    </script><?php
}
add_action ( 'wp_head', 'javascript_variables' );

add_action('wp_ajax_send', 'send_form');
add_action('wp_ajax_nopriv_send', 'send_form');

function send_form() {
    $title = '';
    $project = '';
    $phone = '';
    $email = '';
    $message = '';
    $complectation = '';
    $slug = '';
    $link = '';

    if (!empty($_POST['title'])) $title = $_POST['title'];
    if (!empty($_POST['project'])) $project = $_POST['project'];
    if (!empty($_POST['phone'])) $phone = $_POST['phone'];
    if (!empty($_POST['email'])) $email = $_POST['email'];
    if (!empty($_POST['message'])) $message = $_POST['message'];
    if (!empty($_POST['complectation'])) $complectation = $_POST['complectation'];
    if (!empty($_POST['slug'])) $slug = $_POST['slug'];
    if (!empty($_POST['link'])) $link = $_POST['link'];

    // $to = get_option('admin_email');
    $to = "shumjachi@gmail.com";
    $subject = 'Повідомлення з derevbud.com.ua';

    $body = '<html>
        <head>
          <title>Повідомлення з derevbud.com.ua</title>
        </head>
        <body>';

    if (!empty($title)) $body .= 'Заголовок: ' . $title . '<br>';
    if (!empty($project)) $body .= "Проект: $project $project<br>";
    if (!empty($phone)) $body .= 'Телефон: ' . $phone . '<br>';
    if (!empty($email)) $body .= 'Email: ' . $email . '<br>';
    if (!empty($message)) $body .= 'Повідомлення: ' . $message . '<br>';
    if (!empty($slug)) $body .= 'Сторіка: ' . $slug . '<br>';
    if (!empty($link)) $body .= 'Посилання: ' . $link . '<br>';

    $body .= '</body></html>';
    
    $headers = [
        'From' => $to,
        'Reply-To' => $to,
        'Content-Type' => 'text/html; charset=UTF-8'
    ];
     
    mail( $to, $subject, $body, $headers );
    
    echo 'Done!';
    wp_die();
}

// Add new option
function mobius_add_option_admin() {
    register_setting('general', 'range_min');
    register_setting('general', 'range_max');
    register_setting('general', 'phone');

    add_settings_field(
        'range_min',
        'Діапазон цін (min)',
        'setting_range_min_callback',
        'general',
        'default',
        array(
            'id' => 'range_min',
            'option_name' => 'range_min'
        )
    );

    add_settings_field(
        'range_max',
        'Діапазон цін (max)',
        'setting_range_max_callback',
        'general',
        'default',
        array(
            'id' => 'range_max',
            'option_name' => 'range_max'
        )
    );

    add_settings_field(
        'phone',
        'Телефон (в такому форматі: +38 067 341 40 81:Юрий;+38 067 341 40 81:Андрей)',
        'setting_phone_callback',
        'general',
        'default',
        array(
            'id' => 'phone',
            'option_name' => 'phone'
        )
    );
}
add_action('admin_menu', 'mobius_add_option_admin');

function setting_range_min_callback($val) {
    $id = $val['id'];
    $option_name = $val['option_name'];

    ?>

        <input name="<?php echo $option_name; ?>" value="<?php echo esc_attr(get_option($option_name));?>" id="<?php $id; ?>">

    <?php
}

function setting_range_max_callback($val) {
    $id = $val['id'];
    $option_name = $val['option_name'];

    ?>

        <input name="<?php echo $option_name; ?>" value="<?php echo esc_attr(get_option($option_name));?>" id="<?php $id; ?>">

    <?php
}

function setting_phone_callback($val) {
    $id = $val['id'];
    $option_name = $val['option_name'];

    ?>

    <input class="regular-text code" name="<?php echo $option_name; ?>" value="<?php echo esc_attr(get_option($option_name));?>" id="<?php $id; ?>">

    <?php
}

/**
 * Додатковий функціонал в адмінку для продука
 */
add_action('woocommerce_product_options_general_product_data', 'woo_add_custom_functionality');
function woo_add_custom_functionality() {
    global $woocommerce, $post;

    $name_attribute = get_post_meta($post->ID, 'additional_attribute_name', true);
    $value_attribute = get_post_meta($post->ID, 'additional_attribute_value', true);
    $attribute = array();

    $count = 0;
    foreach ($name_attribute as $name) {
        $attribute[$count]['name'] = $name;
        $count++;
    }

    $count = 0;
    foreach ($value_attribute as $value) {
        $attribute[$count]['value'] = $value;
        $count++;
    }

    // Додаткові атрибути
    echo "<div class='options_group wrap-attribute'>";
    echo "<h3 style='text-align: center;'>Додаткові атрибути</h3>";
    ?>

    <a href="#" class="add-btn-attribute button">Додати атрибут</a>

    <?php $count = 0; foreach ($attribute as $attr) : ?>
        <div class="attribute-block">
            <a href="#" class="remove-attribute">Видалити</a>
            <input type="text" name="additional_attribute_name[<?php echo $count; ?>]" placeholder="Назва атрибуту" value="<?php echo $attr['name']; ?>">
            <input type="text" name="additional_attribute_value[<?php echo $count; ?>]" placeholder="Значення атрибуту" value="<?php echo $attr['value']; ?>">
        </div>
        <?php $count++; endforeach; ?>

    <script>
        jQuery(document).ready(function($){
            function remove_attribute() {
                $('.remove-attribute').click(function(e) {
                    e.preventDefault();
                    $(this).closest('.attribute-block').remove();
                });
            }

            remove_attribute();

            $('.add-btn-attribute').click(function(e) {
                e.preventDefault();

                var _this = $(this),
                    size = $('.options_group.wrap-attribute .attribute-block').size(),
                    html = $('<div class="attribute-block"><a href="#" class="remove-attribute">Видалити</a><input type="text" name="additional_attribute_name[' + size + ']" placeholder="Назва атрибуту"><input type="text" name="additional_attribute_value[' + size + ']" placeholder="Значення атрибуту"></div>').insertAfter(_this);

                remove_attribute();
            });
        });
    </script>

    <?php

    $text_bottom_prict = get_post_meta($post->ID, 'additional_text', true);

    echo "</div>";

    // Текст під ціною
    echo "<div class='options_group wrap-text'>";
    echo "<h3 style='text-align: center;'>Текст під ціною</h3>";

    ?>

    <div class="text-block">
        <input type="text" name="additional_text" value="<?php echo (!empty($text_bottom_prict)) ? $text_bottom_prict : 'Введіть текст який буде показуватися на сторінці проекту, під ціною'; ?>" placeholder="Введіть текст який буде показуватися на сторінці проекту, під ціною">
    </div>

    <?php

    echo "</div>";

    $text_botton = get_post_meta($post->ID, 'additional_text_button', true);

    // Текст кнопки
    echo "<div class='options_group wrap-text-button'>";
    echo "<h3 style='text-align: center;'>Текст кнопки</h3>";

    ?>

    <div class="text-button-block">
        <input type="text" name="additional_text_button" value="<?php echo (!empty($text_botton)) ? $text_botton : 'Текст кнопки'; ?>" placeholder="Текст кнопки">
    </div>

    <?php

    echo "</div>";

    // Вартість дерева
    echo "<div class='options_group wrap-price'>";
    echo "<h3 style='text-align: center;'>Вартість дерева</h3>";
    ?>

    <style>
        .options_group.wrap-price,
        .options_group.wrap-attribute,
        .options_group.wrap-text,
        .options_group.wrap-text-button {
            padding: 20px;
        }
        .options_group.wrap-price .price-block,
        .options_group.wrap-attribute .attribute-block,
        .options_group.wrap-text .text-block,
        .options_group.wrap-text-button .text-button-block {
            background: #f6f6f6;
            margin-top: 10px;
            overflow: hidden;
            padding: 10px;
        }
        .options_group.wrap-text .text-block input,
        .options_group.wrap-text-button .text-button-block input {width: 100%;}
        .options_group.wrap-attribute .attribute-block input {width: 48%;}
        .options_group.wrap-attribute .attribute-block input:first-child {float: left;}
        .options_group.wrap-attribute .attribute-block input:last-child {float: right;}

        .options_group.wrap-price .price-block .img {
            display: inline-block;
            vertical-align: middle;
            text-align: center;
        }
        .options_group.wrap-price .price-block .img img {
            display: inline-block;
            width: 50px;
            height: 50px;
            background: #f0f0f0;
        }
        .options_group.wrap-price .price-block .img a {
            display: block;
        }

        .options_group.wrap-price .price-block .right {
            display: inline-block !important;
            vertical-align: middle;
            margin-left: 30px;
            width: auto;
            float: none;
            max-width: 300px;
        }

        .options_group.wrap-price .price-block .link-price-product,
        .options_group.wrap-price .price-block .title-price-product {
            height: 30px;
            width: 300px;
            background: #fff;
            display: block;
            margin: 5px 0;
        }

        .options_group.wrap-price .price-block .remove {
            float: right;
        }

        .remove-attribute {
            float: none;
            display: block;
            margin-bottom: 15px;
        }

        .options_group.wrap-price .price-block .select-price {
            display: block;
            padding: 10px;
            max-height: 200px;
            overflow-y: auto;
            margin-top: 15px;
            background: #f0f0f0;
        }

        .options_group.wrap-price .select-price label.count-cube {
            display: inline-block;
            margin: 5px 0 5px 0;
            float: none;
            width: 34%;
        }

        .options_group.wrap-price .select-price label.count-cube input,
        .options_group.wrap-price .select-price label.count-cube span {
            display: inline-block;
            vertical-align: middle;
            margin-left: 15px;
        }
        .options_group.wrap-price .select-price label.title {
            display: inline-block;
            float: none;
            margin: 5px 0 5px 0;
            width: 60%;
        }
        .options_group.wrap-price .select-price label.title input,
        .options_group.wrap-price .select-price label.title span {
            display: inline-block;
            vertical-align: middle;
        }
        .options_group.wrap-price .select-price label.title span {
            margin-left: 10px;
        }

        .add-btn-price {
            float: left;
        }
        .online-display {
            float: right !important;
            margin: 0 !important;
            width: auto !important;
            background: #f6f6f6;
            padding: 15px !important;
            font-size: 15px;
            color: #000;
        }
        .online-display input {
            display: inline-block;
            margin-right: 10px;
            vertical-align: middle;
        }
        .online-display span {
            display: inline-block;
            vertical-align: middle;
        }
        .wrap-price-online {
            overflow: hidden;
            position: relative;
            top: 10px;
        }

    </style>

    <?php
    $online_display = get_post_meta($post->ID, 'online_display', true);
    ?>

    <div class="wrap-price-online">
        <a href="#" class="add-btn-price button">Додати ціну</a>
        <label class="online-display">
            <input type="checkbox" name="online_display" <?php echo ($online_display) ? 'checked' : ''; ?>>
            <span>Відображати на сайті</span>
        </label>
    </div>

    <?php

    $title_price_product = get_post_meta($post->ID, 'title_price_product', true);
    $link_price_product = get_post_meta($post->ID, 'link_price_product', true);
    $id_img_price_product = get_post_meta($post->ID, 'id_img_price_product', true);
    $price_product = get_post_meta($post->ID, 'table_price_product_custom', true);
    $count_product = get_post_meta($post->ID, 'table_count_product_custom', true);

    $arr_data_price = array();

    if ($title_price_product) {
        $count = 0;
        foreach ($title_price_product as $title) {
            $arr_data_price[$count]['title'] = $title;
            $count++;
        }
    }

    if ($link_price_product) {
        $count = 0;
        foreach ($link_price_product as $link) {
            $arr_data_price[$count]['link'] = $link;
            $count++;
        }
    }

    if ($id_img_price_product) {
        $count = 0;
        foreach ($id_img_price_product as $id) {
            $arr_data_price[$count]['id'] = $id;
            $count++;
        }
    }

    if ($price_product) {
        $count = 0;
        foreach ($price_product as $price) {
            $arr_data_price[$count]['price'] = $price;
            $count++;
        }
    }

    if ($count_product) {
        $count = 0;
        foreach ($count_product as $count_price) {
            $arr_data_price[$count]['count'] = $count_price;
            $count++;
        }
    }


    $price_product_table = get_option('_cs_options');

    ?>

    <?php $count = 0; foreach ($arr_data_price as $val) : ?>

        <div class="price-block">
            <div class="inner">
                <div class="img">
                    <?php if (!empty(wp_get_attachment_url($val['id']))) : ?>
                        <img src="<?php echo wp_get_attachment_url($val['id']); ?>" alt="">
                    <?php else : ?>
                        <img src="<?php echo bloginfo('template_url') ?>/img/no-image.png" alt="">
                    <?php endif; ?>
                    <a href="<?php echo wp_get_attachment_url($val['id']); ?>" class="upload_image">Зображення</a>
                </div>
                <div class="right">
                    <input type="text" name="title_price_product[<?php echo $count; ?>]" class="title-price-product" placeholder="Заголовок" value="<?php echo $val['title']; ?>">
                    <input type="text" name="link_price_product[<?php echo $count; ?>]" class="link-price-product" placeholder="http://" value="<?php echo $val['link']; ?>">
                </div>

                <input type="hidden" name="id_img_price_product[<?php echo $count; ?>]" class="input-id-img" value="<?php echo $val['id']; ?>">
                <a href="#" class="remove">Видалити</a>
            </div>

            <div class="select-price">

                <?php $count_price = 0; foreach ($price_product_table['product_price_table'] as $price_key) : ?>

                    <?php if (!empty($val['price']) && in_array($price_key['title_price_urk'], $val['price'])) : ?>
                        <div class="grop-price">
                            <label class="title">
                                <input type="checkbox" name="table_price_product_custom[<?php echo $count; ?>][<?php echo $count_price; ?>]" value="<?php echo $price_key['title_price_urk']; ?>" checked>
                                <span><?php echo $price_key['title_price_urk']; ?></span>
                            </label>


                            <label class="count-cube">
                                <input type="number" name="table_count_product_custom[<?php echo $count; ?>][<?php echo $count_price; ?>]" value="<?php echo $val['count'][$count_price]; ?>">
                                <span>к-сть кубів</span>
                            </label>

                        </div>
                    <?php else : ?>
                        <div class="grop-price">
                            <label class="title">
                                <input type="checkbox" name="table_price_product_custom[<?php echo $count; ?>][<?php echo $count_price; ?>]" value="<?php echo $price_key['title_price_urk']; ?>"><span><?php echo $price_key['title_price_urk']; ?></span>
                            </label>

                            <label class="count-cube">
                                <input type="number" name="table_count_product_custom[<?php echo $count; ?>][<?php echo $count_price; ?>]" value="<?php echo $val['count'][$count_price]; ?>">
                                <span>к-сть кубів</span>
                            </label>
                        </div>
                    <?php endif; ?>
                    <?php $count_price++; endforeach; ?>

            </div>
        </div>

        <?php $count++; endforeach; ?>

    <script>

        jQuery(document).ready(function($) {
            var count = 0,
                img_id_array = new Array();

            $('.add-btn-price').click(function(e) {
                e.preventDefault();

                var _this = $(this),
                    size = $('.options_group.wrap-price .price-block').size(),
                    priceBlock = $('<div class="price-block"><div class="inner"><div class="img"><img src="" alt=""><a href="#" class="upload_image">Зображення</a></div><div class="right"><input type="text" name="title_price_product[' + size + ']" class="title-price-product" placeholder="Заголовок"><input type="text" name="link_price_product[' + size + ']" class="link-price-product" placeholder="http://"></div><input type="hidden" name="id_img_price_product[' + size + ']" class="input-id-img"><a href="#" class="remove">Видалити</a></div><div class="select-price"></div></div>').insertAfter(_this);

                $.ajax({
                    url: '/wp-admin/admin-ajax.php',
                    type: 'POST',
                    data: 'action=get_price&size=' + size,
                    success: function(data) {
                        _this.next().find('.select-price').html(data);
                    }
                });

                upload_img();
                remove_block_price();
            });

            function remove_block_price() {
                $('.options_group.wrap-price .remove').click(function(e) {
                    e.preventDefault();
                    $(this).closest('.price-block').remove();
                });
            }
            remove_block_price();


            function upload_img() {
                $('.upload_image').click(function(e) {
                    e.preventDefault();

                    var _this = $(this);

                    var file_frame = wp.media({
                        title: 'Зображення товару',
                        button: {text: 'Вставити зображення товару'},
                        multiple: false
                    });

                    file_frame.open();

                    file_frame.on('select', function() {
                        var attachment = file_frame.state().get('selection').first().toJSON();

                        _this.closest('.price-block').find('.input-id-img').val(attachment.id);
                        _this.closest('.price-block').find('.img img').attr('src', attachment.url);
                    });
                });
            }
            upload_img();
        });

    </script>

    <?php
    echo "</div>";
}

add_action('save_post', 'add_price_product');

function add_price_product() {
    global $post;

    if (isset($_POST['online_display'])) {
        update_post_meta($post->ID, 'online_display', 1);
    } else {
        update_post_meta($post->ID, 'online_display', 0);
    }

    if (isset($_POST['additional_text_button'])) {
        update_post_meta($post->ID, 'additional_text_button', $_POST['additional_text_button']);
    }

    // Текст під ціною
    if (isset($_POST['additional_text'])) {
        update_post_meta($post->ID, 'additional_text', $_POST['additional_text']);
    }

    // Додаткові атрибути
    if (isset($_POST['additional_attribute_name'])) {
        update_post_meta($post->ID, 'additional_attribute_name', $_POST['additional_attribute_name']);
    }
    if (isset($_POST['additional_attribute_value'])) {
        update_post_meta($post->ID, 'additional_attribute_value', $_POST['additional_attribute_value']);
    }

    // Додаткова ціна
    if (isset($_POST['title_price_product'])) {
        update_post_meta($post->ID, 'title_price_product', $_POST['title_price_product']);
    }

    if (isset($_POST['link_price_product'])) {
        update_post_meta($post->ID, 'link_price_product', $_POST['link_price_product']);
    }

    if (isset($_POST['id_img_price_product'])) {
        update_post_meta($post->ID, 'id_img_price_product', $_POST['id_img_price_product']);
    }

    if (isset($_POST['table_price_product_custom'])) {
        update_post_meta($post->ID, 'table_price_product_custom', $_POST['table_price_product_custom']);
    }

    if (isset($_POST['table_count_product_custom'])) {
        update_post_meta($post->ID, 'table_count_product_custom', $_POST['table_count_product_custom']);
    }
}

add_action('wp_ajax_get_price', 'get_price_product');
add_action('wp_ajax_nopriv_get_price', 'get_price_product');

function get_price_product() {
    $price_product = get_option('_cs_options');
    $size = $_POST['size'];
    ?>

    <?php $count = 0; foreach ($price_product['product_price_table'] as $price) : ?>
        <div class="grop-price">
            <label class="title">
                <input type="checkbox" name="table_price_product_custom[<?php echo $size; ?>][<?php echo $count; ?>]" value="<?php echo $price['title_price_urk']; ?>"><span><?php echo $price['title_price_urk']; ?></span>
            </label>

            <label class="count-cube">
                <input type="number" name="table_count_product_custom[<?php echo $size; ?>][<?php echo $count; ?>]" value="1">
                <span>к-сть кубів</span>
            </label>
        </div>
        <?php $count++; endforeach; ?>

    <?php
    die;
}

function is_price_table($price) {
    $price_talbe = get_option('_cs_options');
    foreach ($price_talbe['product_price_table'] as $val) {
        if (array_key_exists($price, $val)) return true;
    }
}

/**
 * Отримуємо html код мінімальної ціни із таблиці цін
 * @return [html]
 */
function get_price_html() {
    global $post;

    $price_product = get_post_meta($post->ID, 'table_price_product_custom', true);
    $count_product = get_post_meta($post->ID, 'table_count_product_custom', true);

    $arr_data_price = array();

    if ($price_product) {
        $count = 0;
        foreach ($price_product as $price) {
            $arr_data_price[$count]['price'] = $price;
            $count++;
        }
    }

    if ($count_product) {
        $count = 0;
        foreach ($count_product as $val) {
            $arr_data_price[$count]['count'] = array_diff($val, array(null));
            $count++;
        }
    }

    $table_price = get_option('_cs_options');
    $arr_price = array();

    foreach ($arr_data_price as $data) {
        if (!empty($data['price'])) {
            foreach ($data['price'] as $key => $prc) {
                foreach ($table_price['product_price_table'] as $price) {
                    if (in_array($prc, $price)) {
                        $arr_price[] = $price['derevo_price'] * $data['count'][$key];
                    }
                }
            }
        }
    }

    return "<span class='amount'>" . min($arr_price) . "<span class='woocommerce-Price-currencySymbol'>грн</span></span>";
}

/**
 * Додаємо мета поля для (Рубрик продукту)
 */
$taxname = 'post_tag';

// Поля при добавлении элемента таксономии
add_action("{$taxname}_add_form_fields", 'add_new_custom_fields');
// Поля при редактировании элемента таксономии
add_action("{$taxname}_edit_form_fields", 'edit_new_custom_fields');

// Сохранение при добавлении элемента таксономии
add_action("create_{$taxname}", 'save_custom_taxonomy_meta');
// Сохранение при редактировании элемента таксономии
add_action("edited_{$taxname}", 'save_custom_taxonomy_meta');

function edit_new_custom_fields( $term ) {
    ?>
    <table class="form-table" style="margin-bottom: 40px; width: 100%;">
        <tr class="form-field">
            <th scope="row" valign="top"><label>Заголовок</label></th>
            <td>
                <input type="text" name="extra[title]" value="<?php echo esc_attr( get_term_meta( $term->term_id, 'title', 1 ) ) ?>"><br />
                <span class="description">Заголовок категорії</span>
            </td>
        </tr>
        <tr class="form-field">
            <th scope="row" valign="top"><label>Опис</label></th>
            <td>
                <?php

                $description = get_term_meta( $term->term_id, 'description', 1 );
                wp_editor($description, 'cateditor', array(
                    'wpautop'       => 1,
                    'media_buttons' => 1,
                    'textarea_name' => 'extra[description]', //нужно указывать!
                    'textarea_rows' => 5,
                    'tabindex'      => null,
                    'editor_css'    => '',
                    'editor_class'  => '',
                    'teeny'         => 0,
                    'dfw'           => 0,
                    'tinymce'       => 1,
                    'quicktags'     => 1,
                    'drag_drop_upload' => false
                ) );
                ?>
                <!-- <textarea name="extra[description]"></textarea><br /> -->
                <span class="description">Опис категорії</span>
            </td>
        </tr>
    </table>
<?php
}

function save_custom_taxonomy_meta( $term_id ) {
    if ( ! isset($_POST['extra']) )
        return;

    // Все ОК! Теперь, нужно сохранить/удалить данные
    $extra = array_map('trim', $_POST['extra']);

    foreach( $extra as $key => $value ){
        if( empty($value) ){
            delete_term_meta( $term_id, $key ); // удаляем поле если значение пустое
            continue;
        }

        update_term_meta( $term_id, $key, $value ); // add_term_meta() работает автоматически
    }

    return $term_id;
}

/**
 * Додаємо мета поля для (Міток поста)
 */
$taxname = 'product_cat';

// Поля при добавлении элемента таксономии
add_action("{$taxname}_add_form_fields", 'add_new_custom_fields_tag_post');
// Поля при редактировании элемента таксономии
add_action("{$taxname}_edit_form_fields", 'edit_new_custom_fields_tag_pos');

// Сохранение при добавлении элемента таксономии
add_action("create_{$taxname}", 'save_custom_taxonomy_meta_tag_pos');
// Сохранение при редактировании элемента таксономии
add_action("edited_{$taxname}", 'save_custom_taxonomy_meta_tag_pos');

function edit_new_custom_fields_tag_pos( $term ) {
    ?>
    <table class="form-table" style="margin-bottom: 40px; width: 100%;">
        <tr class="form-field">
            <th scope="row" valign="top"><label>Заголовок</label></th>
            <td>
                <input type="text" name="extra[title]" value="<?php echo esc_attr( get_term_meta( $term->term_id, 'title', 1 ) ) ?>"><br />
                <span class="description">Заголовок категорії</span>
            </td>
        </tr>
        <tr class="form-field">
            <th scope="row" valign="top"><label>Опис</label></th>
            <td>
                <?php

                $description = get_term_meta( $term->term_id, 'description', 1 );
                wp_editor($description, 'cateditor', array(
                    'wpautop'       => 1,
                    'media_buttons' => 1,
                    'textarea_name' => 'extra[description]', //нужно указывать!
                    'textarea_rows' => 5,
                    'tabindex'      => null,
                    'editor_css'    => '',
                    'editor_class'  => '',
                    'teeny'         => 0,
                    'dfw'           => 0,
                    'tinymce'       => 1,
                    'quicktags'     => 1,
                    'drag_drop_upload' => false
                ) );
                ?>
                <!-- <textarea name="extra[description]"></textarea><br /> -->
                <span class="description">Опис категорії</span>
            </td>
        </tr>
    </table>
<?php
}

function save_custom_taxonomy_meta_tag_pos( $term_id ) {
    if ( ! isset($_POST['extra']) )
        return;

    // Все ОК! Теперь, нужно сохранить/удалить данные
    $extra = array_map('trim', $_POST['extra']);

    foreach( $extra as $key => $value ){
        if( empty($value) ){
            delete_term_meta( $term_id, $key ); // удаляем поле если значение пустое
            continue;
        }

        update_term_meta( $term_id, $key, $value ); // add_term_meta() работает автоматически
    }

    return $term_id;
}


/**
 * Наступний пост AJAX
 */
add_action('wp_ajax_action_next_post', 'next_post_ajax');
add_action('wp_ajax_nopriv_action_next_post', 'next_post_ajax');

function next_post_ajax() {
    $direction = $_POST['direction'];

    if ($direction) {
        $next_post = get_next_post('next', 'next', true, '', 'product');
        prt($next_post);
    } else {
        $prev_post = previous_post_link();
        prt($prev_post);
    }



    die;
}

add_action( 'woocommerce_single_product_next_previous', 'wc_next_prev_products_links', 60 );
function wc_next_prev_products_links() {
    previous_post_link( '%link', '' );
    next_post_link( '%link', 'Следущий проект' );
}

function getCurrentCatID(){
    global $wp_query;
    global $post;
    $cats = get_queried_object()->term_id;
    $cat_ID = $cats[0]->cat_ID;
    return $cat_ID;
}

/**
 * Загрузка постів AJAX
 */
add_action('wp_ajax_loadmore', 'true_load_posts');
add_action('wp_ajax_nopriv_loadmore', 'true_load_posts');

function true_load_posts() {
    $args = unserialize(stripslashes($_POST['query']));
    $args['paged'] = $_POST['page'] + 1;
    $args['post_status'] = 'publish';
    $q = new WP_Query($args);

    if ($q->have_posts()) :
        while ($q->have_posts()) : $q->the_post(); global $post;
            ?>

            <li class="product">
                <div class="thumb">
                    <a href="<?php the_permalink(); ?>" target="_blank" class="preloader-img">
                        <?php the_post_thumbnail(); ?>
                    </a>
                </div>

                <div class="bottom">
                    <div class="clearfix">
                        <p class="cust-title"><?php the_title(); ?></p>

                        <?php
                        global $post;
                        $price = get_post_meta($post->ID, '_price', true);
                        ?>
                        <p class="cust-price"> <?php echo ($price) ? $price . ' грн.' : get_price_html(); ?></p>
                    </div>

                    <div class="clearfix">

                        <?php
                        global $product;
                        $attributes = $product->get_attributes();
                        ?>

                        <a href="<?php the_permalink(); ?>"  target="_blank" class="read"><span><?php pll_e('Детальніше'); ?></span></a>
                    </div>
                </div>
            </li>

        <?php
        endwhile;
    endif;

    wp_reset_postdata();

    die();
}

/**
 * Укравління попапами проекта
 */
add_action('add_meta_boxes', 'box_project_popup', 10, 2);

function box_project_popup($post_type, $post) {
    add_meta_box('project_popup', 'Управління попапами', 'add_box_project_popup_html', 'product', 'side', 'high');
}

function add_box_project_popup_html() {
    global $post;

    $checkbox_one = get_post_meta($post->ID, 'checkbox_one', true);
    $checkbox_two = get_post_meta($post->ID, 'checkbox_two', true);

    wp_enqueue_media();

    ?>

    <style>
        .admin-popup {
            padding: 10px;
            background: #f9f9f9;
        }

        .admin-popup input {
            width: 100%;
            display: block;
        }

        .inner-popup-complectation,
        .inner-popup-commercial-offer {
            margin-top: 15px;
            padding-bottom: 15px;
            border-bottom: 1px dashed #e6e4e4;
        }

        .inner-popup-complectation .wrap-remove,
        .inner-popup-commercial-offer .wrap-remove {
            text-align: right;
            margin-bottom: 10px;
        }

        .inner-popup-complectation .remove,
        .inner-popup-commercial-offer .remove {
            font-size: 11px;
        }

        .inner-popup-commercial-offer .content .item {
            background: #f3f3f3;
            padding: 10px;
            margin-bottom: 5px;
        }

        .inner-popup-complectation .show-block,
        .inner-popup-commercial-offer .show-block {display: none;}

        .inner-popup-complectation .title-open-popup:hover,
        .inner-popup-commercial-offer .title-open-popup:hover,
        .inner-popup-complectation .title-open-popup.active,
        .inner-popup-commercial-offer .title-open-popup.active {
            background: #c3bebe;
            padding-left: 10px;
        }
        .inner-popup-complectation .title-open-popup ,
        .inner-popup-commercial-offer .title-open-popup {
            padding-bottom: 5px;
            cursor: pointer;
        }
        .inner-popup-complectation .title-open-popup .toggle-indicator:before,
        .inner-popup-commercial-offer .title-open-popup .toggle-indicator:before {
            position: relative !important;
            top: 5px !important;
            transform: rotate(180deg) !important;
            -webkit-transform: rotate(180deg) !important;
            -moz-transform: rotate(180deg) !important;
            -o-transform: rotate(180deg) !important;
            -ms-transform: rotate(180deg) !important;
        }
    </style>

    <form method='post'>
        <p>
            <label class="selectit">
                <input type="checkbox" name="checkbox_one" <?php echo ($checkbox_one) ? 'checked' : ''; ?>> Посмотреть комплектацию
            </label>
        </p>

        <p>
            <label class="selectit">
                <input type="checkbox" name="checkbox_two" <?php echo ($checkbox_two) ? 'checked' : ''; ?>> Коммерческое предложение
            </label>
        </p>
    </form>

    <?php
}

/**
 * Попап (Комплектація)
 */

add_action('save_post', 'update_project_popup');
function update_project_popup() {
    global $post;

    if (isset($_POST['checkbox_one'])) {
        update_post_meta($post->ID, 'checkbox_one', 1);
    } else {
        update_post_meta($post->ID, 'checkbox_one', 0);
    }

    if (isset($_POST['checkbox_two'])) {
        update_post_meta($post->ID, 'checkbox_two', 1);
    } else {
        update_post_meta($post->ID, 'checkbox_two', 0);
    }
}

/**
 * Управління попапами
 */
 add_action('admin_menu', 'submenu_options_popup');
function submenu_options_popup() {
    add_submenu_page( 'options-general.php', 'Настройка попапів', 'Настройка попапів', 'manage_options', 'options-popup', 'options_popup_callback' );
}

function options_popup_callback() {
  if (isset($_POST['option_popup'])) {
    if (isset($_POST['complectation'])) {
      update_option('complectation', $_POST['complectation']);
    }

    if (isset($_POST['commertial'])) {
      update_option('commertial', $_POST['commertial']);
    }
  }

  $complectation = get_option('complectation');
  $commertial = get_option('commertial');

  ?>

  <style>
      .admin-popup {
          padding: 10px;
          background: #fff;
          margin-top: 50px;
      }

      .form-popup-admin {
        padding: 0 50px;
        box-sizing: border-box;
        -webkit-box-sizing: border-box;
        -moz-box-sizing: border-box;
        -ms-box-sizing: border-box;
        -o-box-sizing: border-box;
      }

      .admin-popup input {
          width: 100%;
          display: block;
      }

      .inner-popup-complectation,
      .inner-popup-commercial-offer {
          margin-top: 15px;
          padding-bottom: 15px;
          border-bottom: 1px dashed #e6e4e4;
      }

      .inner-popup-complectation .wrap-remove,
      .inner-popup-commercial-offer .wrap-remove {
          text-align: right;
          margin-bottom: 10px;
      }

      .inner-popup-complectation .remove,
      .inner-popup-commercial-offer .remove {
          font-size: 11px;
      }

      .inner-popup-commercial-offer .content .item {
          background: #f3f3f3;
          padding: 10px;
          margin-bottom: 5px;
      }

      .inner-popup-complectation .show-block,
      .inner-popup-commercial-offer .show-block {display: none;}

      .inner-popup-complectation .title-open-popup:hover,
      .inner-popup-commercial-offer .title-open-popup:hover,
      .inner-popup-complectation .title-open-popup.active,
      .inner-popup-commercial-offer .title-open-popup.active {
          background: #e4e4e4;
      }
      .inner-popup-complectation .title-open-popup ,
      .inner-popup-commercial-offer .title-open-popup {
        cursor: pointer;
        background: #f1f1f1;
        padding: 10px 10px;
      }
      .inner-popup-complectation .title-open-popup .toggle-indicator:before,
      .inner-popup-commercial-offer .title-open-popup .toggle-indicator:before {
          position: relative !important;
          top: 5px !important;
          transform: rotate(180deg) !important;
          -webkit-transform: rotate(180deg) !important;
          -moz-transform: rotate(180deg) !important;
          -o-transform: rotate(180deg) !important;
          -ms-transform: rotate(180deg) !important;
      }
  </style>

  <form method='post' action="options-general.php?page=options-popup" class="form-popup-admin" novalidate="novalidate">
      <div class="admin-popup">
          <div class="inner-popup-complectation block-wrap-show">
              <p id="post-visibility-display" class="title-open-popup">Комплектація
                  <span class="toggle-indicator" aria-hidden="true"></span>
              </p>

              <div class="show-block">
                  <p style="overflow: hidden;"><a href="#" class="add-complectation preview button">Додати комплектацію</a></p>

                  <div class="content">
                      <?php if ($complectation) : ?>
                          <?php foreach ($complectation as $key => $value) : ?>
                              <div class="item">
                                  <input type="text" name="complectation[<?php echo $key; ?>]" value="<?php echo $value; ?>">
                                  <div class="wrap-remove">
                                      <a href="#" class="remove">видалити</a>
                                  </div>
                              </div>
                          <?php endforeach; ?>
                      <?php endif; ?>
                  </div>
              </div>
          </div>

          <div class="inner-popup-commercial-offer block-wrap-show">
              <p id="post-visibility-display" class="title-open-popup">Комертійна пропозиція
                  <span class="toggle-indicator" aria-hidden="true"></span>
              </p>

              <div class="show-block">
                  <p style="overflow: hidden;"><a href="#" class="add-commertial preview button">Додати пропозиція</a></p>
                  <p>Додавати через кому (Заголовок, чекбокс, чекбокс)</p>

                  <div class="content">
                      <?php if ($commertial) : ?>
                          <?php foreach ($commertial as $key => $value) : ?>
                              <div class="item">
                                  <input type="text" name="commertial[<?php echo $key; ?>]" value="<?php echo $value; ?>">
                                  <div class="wrap-remove">
                                      <a href="#" class="remove">видалити</a>
                                  </div>
                              </div>
                          <?php endforeach; ?>
                      <?php endif; ?>
                  </div>
              </div>
          </div>
      </div>

      <input type="hidden" name="option_popup" value="1">

      <?php
                settings_fields("opt_group");     // скрытые защитные поля
                do_settings_sections("opt_page"); // секции с настройками (опциями).
                submit_button();
            ?>
  </form>

  <script>
      function addComplectation() {
          var complectation = jQuery('.inner-popup-complectation'),
              btnAddComp = complectation.find('.add-complectation'),
              content = complectation.find('.content');

          btnAddComp.click(function(e) {
             e.preventDefault();

             var complectation = jQuery('.inner-popup-complectation'),
                 item = complectation.find('.content .item'),
                 size = item.size(),
                 html = '<div class="item"><input type="text" name="complectation[' + size + ']"><div class="wrap-remove"><a href="#" class="remove">видалити</a></div></div>';

             content.append(html);
             removeComplectation();
          });
      }

      function removeComplectation() {
          var complectation = jQuery('.inner-popup-complectation'),
              content = complectation.find('.content'),
              item = content.find('.item');

          item.find('.remove').click(function(e) {
             e.preventDefault();
             jQuery(this).closest('.item').remove();
          });
      }

      removeComplectation();
      addComplectation();

      function addCommerticalOffer() {
          var commertical = jQuery('.inner-popup-commercial-offer'),
              btnAddCommertial = commertical.find('.add-commertial'),
              content = commertical.find('.content');

          btnAddCommertial.click(function(e) {
             e.preventDefault();

              var commertical = jQuery('.inner-popup-commercial-offer'),
                  item = commertical.find('.content .item'),
                  size = item.size(),
                  html = '<div class="item"><input type="text" name="commertial[' + size + ']"><div class="wrap-remove"><a href="#" class="remove">видалити</a></div></div>';

              content.append(html);
              removeCommertialOffer();
          });
      }

      function removeCommertialOffer() {
          var commertical = jQuery('.inner-popup-commercial-offer'),
              content = commertical.find('.content'),
              item = content.find('.item');

          item.find('.remove').click(function(e) {
              e.preventDefault();
              jQuery(this).closest('.item').remove();
          });
      }

      addCommerticalOffer();
      removeCommertialOffer();

      jQuery('.title-open-popup').click(function(e) {
          jQuery(this).toggleClass('active');
          jQuery(this).closest('.block-wrap-show').find('.show-block').slideToggle(200);
      });
  </script>

  <?php
}

add_action('save_post', 'save_options_popup');

function save_options_popup() {
  update_option('hel', 'Hello, World!');
}

/*seo_inweb_sm*/
function dimox_breadcrumbs() {

  /* === ОПЦИИ === */
  $text['home'] = 'Главная'; // текст ссылки "Главная"
  $text['category'] = '%s'; // текст для страницы рубрики
  $text['search'] = 'Результаты поиска по запросу "%s"'; // текст для страницы с результатами поиска
  $text['tag'] = 'Записи с тегом "%s"'; // текст для страницы тега
  $text['author'] = 'Статьи автора %s'; // текст для страницы автора
  $text['404'] = 'Ошибка 404'; // текст для страницы 404
  $text['page'] = 'Страница %s'; // текст 'Страница N'
  $text['cpage'] = 'Страница комментариев %s'; // текст 'Страница комментариев N'
  $text['meta'] = '<meta itemprop="position" content="%3$s" />'; // meta after link

  $wrap_before = '<div class="breadcrumbs" itemscope itemtype="http://schema.org/BreadcrumbList">'; // открывающий тег обертки
  $wrap_after = '</div><!-- .breadcrumbs -->'; // закрывающий тег обертки
  $sep = '›'; // разделитель между "крошками"
  $sep_before = '<span class="sep">'; // тег перед разделителем
  $sep_after = '</span>'; // тег после разделителя
  $show_home_link = 1; // 1 - показывать ссылку "Главная", 0 - не показывать
  $show_on_home = 0; // 1 - показывать "хлебные крошки" на главной странице, 0 - не показывать
  $show_current = 1; // 1 - показывать название текущей страницы, 0 - не показывать
  $before = '<span class="current">'; // тег перед текущей "крошкой"
  $after = '</span>'; // тег после текущей "крошки"
  /* === КОНЕЦ ОПЦИЙ === */

  global $post;
  $home_url = home_url('/');
  $link_before = '<span itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">';
  $link_after = '</span>';
  $link_attr = ' itemprop="item"';
  $link_in_before = '<span itemprop="name">';
  $link_in_after = '</span>';
  $link = $link_before . '<a href="%1$s"' . $link_attr . '>' . $link_in_before . '%2$s' . $link_in_after . '</a>' . $text['meta'] . $link_after;
  $frontpage_id = get_option('page_on_front');
  $parent_id = ($post) ? $post->post_parent : '';
  $sep = ' ' . $sep_before . $sep . $sep_after . ' ';
  $home_link = $link_before . '<a href="' . $home_url . '"' . $link_attr . ' class="home">' . $link_in_before . $text['home'] . $link_in_after . '</a>' . '<meta itemprop="position" content="1" />' . $link_after;

  if (is_home() || is_front_page()) {

    if ($show_on_home) echo $wrap_before . $home_link . $wrap_after;

  } else {

    echo $wrap_before;
    if ($show_home_link) echo $home_link;

    if ( is_category() ) {
      $cat = get_category(get_query_var('cat'), false);
      if ($cat->parent != 0) {
        $cats = get_category_parents($cat->parent, TRUE, $sep);
        $cats = preg_replace("#^(.+)$sep$#", "$1", $cats);
        $cats = preg_replace('#<a([^>]+)>([^<]+)<\/a>#', $link_before . '<a$1' . $link_attr .'>' . $link_in_before . '$2' . $link_in_after .'</a>' . $link_after, $cats);
        if ($show_home_link) echo $sep;
        echo $cats;
      }
      if ( get_query_var('paged') ) {
        $cat = $cat->cat_ID;
        echo $sep . sprintf($link, get_category_link($cat), get_cat_name($cat), 2) . $sep . $before . sprintf($text['page'], get_query_var('paged')) . $after;
      } else {
        if ($show_current) echo $sep . sprintf($link, 'https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].'#modal-success', sprintf($text['category'], single_cat_title('', false)), 2);//. $before . sprintf($text['category'], single_cat_title('', false)) . $after;
      }

    } elseif ( is_search() ) {
      if (have_posts()) {
        if ($show_home_link && $show_current) echo $sep;
        if ($show_current) echo sprintf($link, 'https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].'#modal-success', sprintf($text['search'], get_search_query()), 2);//$before . sprintf($text['search'], get_search_query()) . $after;
      } else {
        if ($show_home_link) echo $sep;
        echo $before . sprintf($text['search'], get_search_query()) . $after;
      }

    } elseif ( is_day() ) {
      if ($show_home_link) echo $sep;
      echo sprintf($link, get_year_link(get_the_time('Y')), get_the_time('Y'), 2) . $sep;
      echo sprintf($link, get_month_link(get_the_time('Y'), get_the_time('m')), get_the_time('F'), 3);
      if ($show_current) echo $sep . $before . get_the_time('d') . $after;

    } elseif ( is_month() ) {
      if ($show_home_link) echo $sep;
      echo sprintf($link, get_year_link(get_the_time('Y')), get_the_time('Y'), 3);
      if ($show_current) echo $sep . $before . get_the_time('F') . $after;

    } elseif ( is_year() ) {
      if ($show_home_link && $show_current) echo $sep;
      if ($show_current) echo $before . get_the_time('Y') . $after;

    } elseif ( is_single() && !is_attachment() ) {
      if ($show_home_link) echo $sep;
      if ( get_post_type() != 'post' ) {
        $post_type = get_post_type_object(get_post_type());
        $slug = $post_type->rewrite;

        //  inweb костыль
        $mtext = $post_type->labels->singular_name;  // $post_type->labels->singular_name
        if ($mtext == 'Product')
            printf($link, $home_url. 'product-category/vse-proekty-derevyannyh-domov' . '/', 'Проекты', 2);
            else
            printf($link, $home_url. $slug['slug'].'/', $post_type->labels->singular_name, 2);
        if ($show_current) echo $sep . sprintf($link, 'https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].'#modal-success', get_the_title(), 3);//$before . get_the_title() . $after;
      } else {
        $cat = get_the_category(); $cat = $cat[0];
        $cats = get_category_parents($cat, TRUE, $sep);
        if (!$show_current || get_query_var('cpage')) $cats = preg_replace("#^(.+)$sep$#", "$1", $cats);
        $cats = preg_replace('#<a([^>]+)>([^<]+)<\/a>#', $link_before . '<a$1' . $link_attr .'>' . $link_in_before . '$2' . $link_in_after .'</a>' . '<meta itemprop="position" content="2" />' . $link_after, $cats);
        echo $cats;
        if ( get_query_var('cpage') ) {
          echo $sep . sprintf($link, get_permalink(), get_the_title(), 3) . $sep . $before . sprintf($text['cpage'], get_query_var('cpage')) . $after;
        } else {
          if ($show_current) echo sprintf($link, 'https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].'#modal-success', get_the_title(), 3);//$before . get_the_title() . $after;
        }
      }

    // custom post type
    }


    elseif ( !is_single() && !is_page() && get_post_type() != 'post' && !is_404() ) {
      $post_type = get_post_type_object(get_post_type());
      if ( get_query_var('paged') ) {
        echo $sep . sprintf($link, get_post_type_archive_link($post_type->name), $post_type->label, 2) . $sep . $before . sprintf($text['page'], get_query_var('paged')) . $after;
      } else if ($show_current)
                //  inweb костыль
                if ($post_type->label == 'Products')  echo $sep . sprintf($link, 'https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].'#modal-success', 'Проекты', 2);//. $before . 'Проекты' . $after;
                    else echo $sep . sprintf($link, 'https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].'#modal-success', $post_type->label, 2);//. $before . $post_type->label . $after;
    }
    //--------

    elseif ( is_attachment() ) {
      if ($show_home_link) echo $sep;
      $parent = get_post($parent_id);
      $cat = get_the_category($parent->ID); $cat = $cat[0];
      if ($cat) {
        $cats = get_category_parents($cat, TRUE, $sep);
        $cats = preg_replace('#<a([^>]+)>([^<]+)<\/a>#', $link_before . '<a$1' . $link_attr .'>' . $link_in_before . '$2' . $link_in_after .'</a>' . $link_after, $cats);
        echo $cats;
      }
      printf($link, get_permalink($parent), $parent->post_title, 2);
      if ($show_current) echo $sep . sprintf($link, 'https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].'#modal-success', get_the_title(), 3);//. $before . get_the_title() . $after;

    } elseif ( is_page() && !$parent_id ) {
      if ($show_current) echo $sep . sprintf($link, 'https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].'#modal-success', get_the_title(), 2);//. $before . get_the_title() . $after;

    } elseif ( is_page() && $parent_id ) {
      if ($show_home_link) echo $sep;
      if ($parent_id != $frontpage_id) {
        $breadcrumbs = array();
        $i_step = 2;
        while ($parent_id) {
          $page = get_page($parent_id);
          if ($parent_id != $frontpage_id) {
            $breadcrumbs[] = sprintf($link, get_permalink($page->ID), get_the_title($page->ID), $i_step);
          }
          $parent_id = $page->post_parent;
          $i_step++;
        }
        $breadcrumbs = array_reverse($breadcrumbs);
        for ($i = 0; $i < count($breadcrumbs); $i++) {
          echo $breadcrumbs[$i];
          if ($i != count($breadcrumbs)-1) echo $sep;
        }
      }
      if ($show_current) echo $sep . sprintf($link, 'https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].'#modal-success', get_the_title(), 2);//. $before . get_the_title() . $after;

    } elseif ( is_tag() ) {
      if ( get_query_var('paged') ) {
        $tag_id = get_queried_object_id();
        $tag = get_tag($tag_id);
        echo $sep . sprintf($link, get_tag_link($tag_id), $tag->name, 2) . $sep . $before . sprintf($text['page'], get_query_var('paged')) . $after;
      } else {
        if ($show_current) echo $sep . sprintf($link, 'https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].'#modal-success', sprintf($text['tag'], single_tag_title('', false)), 2);//. $before . sprintf($text['tag'], single_tag_title('', false)) . $after;
      }

    } elseif ( is_author() ) {
      global $author;
      $author = get_userdata($author);
      if ( get_query_var('paged') ) {
        if ($show_home_link) echo $sep;
        echo sprintf($link, get_author_posts_url($author->ID), $author->display_name, 2) . $sep . $before . sprintf($text['page'], get_query_var('paged')) . $after;
      } else {
        if ($show_home_link && $show_current) echo $sep;
        if ($show_current) echo sprintf($link, 'https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].'#modal-success', sprintf($text['author'], $author->display_name), 2); //$before . sprintf($text['author'], $author->display_name) . $after;
      }

    } elseif ( is_404() ) {
      if ($show_home_link && $show_current) echo $sep;
      if ($show_current) echo $before . $text['404'] . $after;

    } elseif ( has_post_format() && !is_singular() ) {
      if ($show_home_link) echo $sep;
      echo get_post_format_string( get_post_format() );
    }

    echo $wrap_after;

  }
} // end of dimox_breadcrumbs()
/*/seo_inweb_sm*/

function get_breadcrumb() {
    echo '<a href="'.home_url().'" rel="nofollow">Home</a>';
    if (is_category() || is_single()) {
        echo "&nbsp;&nbsp;&#187;&nbsp;&nbsp;";
        the_category(' &bull; ');
            if (is_single()) {
                echo " &nbsp;&nbsp;&#187;&nbsp;&nbsp; ";
                the_title();
            }
    } elseif (is_page()) {
        echo "&nbsp;&nbsp;&#187;&nbsp;&nbsp;";
        echo the_title();
    } elseif (is_search()) {
        echo "&nbsp;&nbsp;&#187;&nbsp;&nbsp;Search Results for... ";
        echo '"<em>';
        echo the_search_query();
        echo '</em>"';
    }
}

if (strpos($_SERVER['REQUEST_URI'], '/product-category/') !== false) {
    add_filter( 'wpseo_metadesc', 'change_seo_title_description', 10, 1 );
    add_filter( 'wpseo_title', 'change_seo_title_description', 10, 1 );
}

//add_filter( 'wpseo_metadesc', 'change_seo_title_description', 10, 1 );
//add_filter( 'wpseo_title', 'change_seo_title_description', 10, 1 );

function change_seo_title_description ($text) {
//    echo "position 0";
//    if (strpos($_SERVER['REQUEST_URI'], '/product-category/') !== false) {
//        echo "position 1";

        $current_page =  get_query_var( 'paged' );
        if ( ($current_page != 0) && ($current_page != 1) ) {
//            echo "position 2";
            $text = $text . " - страница " . $current_page;
        }
//    }
    return $text;
}

//remove_action('woocommerce_after_shop_loop', 'woocommerce_pagination', 10);
//add_action( 'woocommerce_after_shop_loop', 'custom_pagination', 10);

//function custom_wc_pagination() {
//    custom_pagination();
//}

function custom_pagination($pages = '', $range = 2)
{
    $showitems = ($range * 2)+1;

    global $paged;
    if(empty($paged)) $paged = 1;

    if($pages == '')
    {
        global $wp_query;

        if ( $wp_query->max_num_pages <= 1 ) {
            return;
        }

        $pages = $wp_query->max_num_pages;
        if(!$pages)
        {
            $pages = 1;
        }
    }

    if(1 != $pages)
    {
        echo "<div class='pagination' style='display: none'>";
        if($paged > 2 && $paged > $range+1 && $showitems < $pages) echo "<a href='".get_pagenum_link(1)."'>&laquo;</a>";
        if($paged > 1 && $showitems < $pages) echo "<a href='".get_pagenum_link($paged - 1)."'>&lsaquo;</a>";

        for ($i=1; $i <= $pages; $i++)
        {
            if (1 != $pages &&( !($i >= $paged+$range+1 || $i <= $paged-$range-1) || $pages <= $showitems ))
            {
                echo ($paged == $i)? "<span class='current'>".$i."</span>":"<a href='".get_pagenum_link($i)."' class='inactive' >".$i."</a>";
            }
        }

        if ($paged < $pages && $showitems < $pages) echo "<a href='".get_pagenum_link($paged + 1)."'>&rsaquo;</a>";
        if ($paged < $pages-1 &&  $paged+$range-1 < $pages && $showitems < $pages) echo "<a href='".get_pagenum_link($pages)."'>&raquo;</a>";
        echo "</div>\n";
    }
}



function add_microdata_to_header() {

    $site_name = get_bloginfo();

    $site_url = get_site_url();

    ?>

    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "LocalBusiness",
            "image": [
                "https://derevbud.com.ua/wp-content/uploads/2017/05/Фото00049-600x350.jpg",
                "https://derevbud.com.ua/wp-content/uploads/2017/05/Фото00010-600x350.jpg",
                "https://derevbud.com.ua/wp-content/uploads/2017/06/klassik2-600x350.jpg"
            ],
            "@id": "http://davessteakhouse.example.com",
            "name": "derevbud",
            "priceRange": "UAH",
            "address": {
                "streetAddress":"Львівска область , Старий Самбір вул. Промислова,1",
                "addressLocality": "Львівска область",
                "addressRegion" : "<?php echo get_locale();?>",
                "postalCode" : "79052",
                "addressCountry" : "<?php echo get_locale();?>"
            },
            "review": {
                "@type": "Review",
                "reviewRating": {
                    "@type": "Rating",
                    "ratingValue": "5",
                    "bestRating": "5"
                },
                "author": {
                    "@type": "Person",
                    "name": "derevbud"
                }
            },
            "geo": {
                "@type": "GeoCoordinates",
                "latitude": "49.46593051352242",
                "longitude": "22.98574481472021"
            },
            "url":"https://www.google.com/maps/place/%D0%94%D0%B5%D1%80%D0%B5%D0%B2%D0%91%D1%83%D0%B4,+%D0%BA%D0%BE%D0%BC%D0%BF%D0%B0%D0%BD%D0%B8%D1%8F/@49.456856,22.987744,16z/data=!4m6!3m5!1s0x473bbdccffbb3f23:0xb7392f5c82bbe73d!8m2!3d49.4568561!4d22.987744!16s%2Fg%2F11gdrhtkmq?hl=ru-RU",
            "telephone":"+38 067 341 40 81",
            "openingHoursSpecification": [
                {
                    "@type": "OpeningHoursSpecification",
                    "dayOfWeek": [
                        "Пн",
                        "Пт"
                    ],
                    "opens": "09:30",
                    "closes": "17:00"
                }
            ],
            "menu": "derevbud"
        }
    </script>

    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "Organization",
            "name": "<?php echo $site_name; ?>",
            "url": "<?php echo $site_url?>",
            "logo": "https://derevbud.com.ua/wp-content/uploads/2017/07/logo4.png",
            "contactPoint": {
                "@type": "ContactPoint",
                "telephone": "+38 067 341 40 81",
                "contactType": "customer service"
            }
        }
    </script>

    <?php
}
add_action( 'wp_head', 'add_microdata_to_header' );

function add_microdata_to_post() {

    if ( is_single() || is_category() ) { // Убедитесь, что мы находимся на странице статьи или категории
        global $post;
        $author_id = $post->post_author;
        $author_name = get_the_author_meta( 'display_name', $author_id );
        $publisher_name = get_bloginfo( 'name' );
        $publisher_logo = get_template_directory_uri() . '/images/logo.png'; // замените ссылку на логотип вашей компании

        $json_ld = array(
            '@context' => 'https://schema.org',
            '@type' => 'Article',
            'mainEntityOfPage' => array(
                '@type' => 'WebPage',
                '@id' => get_permalink( $post->ID )
            ),
            'url' => get_permalink( $post->ID ),
            'datePublished' => get_the_date( 'c' ),
            'dateModified' => get_the_modified_date( 'c' ),
            'headline' => get_the_title( $post->ID ),
            'image' => array(
                '@type' => 'ImageObject',
                'url' => get_the_post_thumbnail_url( $post->ID, 'thumbnail' ),
                'height' => '100',
                'width' => '100'
            ),
            'articleBody' => get_the_content(),
            'author' => array(
                '@type' => 'Person',
                'name' => $author_name,
                'url' => get_author_posts_url( $author_id )
            ),
            'publisher' => array(
                '@type' => 'Organization',
                'name' => $publisher_name,
                'logo' => array(
                    '@type' => 'ImageObject',
                    'url' => $publisher_logo,
                    'height' => '100',
                    'width' => '100'
                )
            )
        );
        echo '<script type="application/ld+json">' . json_encode( $json_ld ) . '</script>';
    }
}
add_action( 'wp_head', 'add_microdata_to_post' );

function add_microdata_to_nav_menu_items($items, $args) {
    // проверяем, что текущее меню является главным меню с id="top-nav"
    if ($args->theme_location == 'top-nav') {
        $items = preg_replace('/<ul>/', '<ul itemprop="hasMenu">', $items, 1);
        $items = preg_replace('/<span>/', '<span itemprop="name">', $items, 1);
        $items = preg_replace('/<a(.*?)>/', '<a itemprop="url" $1>', $items);
        //$items = preg_replace('/<ul(.*?)>/', '<ul itemprop="hasSubMenu" $1>', $items);
    }
    return $items;
}
add_filter('wp_nav_menu_items', 'add_microdata_to_nav_menu_items', 10, 2);




function add_content_language_meta_tag() {
    $current_language = get_locale();
    $content_language_meta_tag = '<meta http-equiv="Content-Language" content="' . $current_language . '">';
    echo $content_language_meta_tag;
}
add_action('wp_head', 'add_content_language_meta_tag');

function add_detected_language_meta_tag() {

    //$user_language = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);

    $current_language = get_locale();

    $alternate_languages = '';
    if ( $current_language == 'ru_RU' ) {
        $alternate_languages = 'ru';
    } else if ( $current_language == 'ua_UA' ) {
        $alternate_languages = 'uk';
    }
    $detected_language_meta_tag = '<meta name="Detected Language" content="' . $alternate_languages . '">';
    echo $detected_language_meta_tag;
}
add_action('wp_head', 'add_detected_language_meta_tag');


function add_content_language_header() {
    $current_language = get_locale();
    header("Content-Language: $current_language");
}
add_action('send_headers', 'add_content_language_header');

function custom_meta_description() {
    if (is_single() || is_page()) {
        global $post;
        $meta_description = $post->post_title;
        if (!empty($meta_description)) {
            echo '<meta name="description" content="' . esc_attr($meta_description) . '">';
        }
    }else{
        echo '<meta name="description" content="' . esc_attr("derevbud") . '">';
    }
}
add_action('wp_head', 'custom_meta_description');

function redirect_404_errors() {
    if (is_404()) {
        wp_redirect(home_url(), 301);
        exit;
    }
}
add_action('template_redirect', 'redirect_404_errors');