<?php
/**
 * Single Product Price
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/price.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product;

$attributes = $product->get_attributes();

$additional_attribute_name = get_post_meta($product->id, 'additional_attribute_name', true);
$additional_attribute_value = get_post_meta($product->id, 'additional_attribute_value', true);
$custom_attribute = array();

$count = 0; 
foreach ($additional_attribute_name as $name) {
    $custom_attribute[$count]['name'] = $name;
    $count++;
}

$count = 0; 
foreach ($additional_attribute_value as $value) {
    $custom_attribute[$count]['value'] = $value;
    $count++;
}

?>

<?php if ($custom_attribute) : ?>
<ul class="product-attributes">
    <?php foreach ($custom_attribute as $attribute) : ?>
        <li>
            <p class="title"><?php echo $attribute['name']; ?></p>
            <strong class="value"><?php echo $attribute['value']; ?></strong>
        </li>
    <?php endforeach; ?>
</ul>
<?php endif; ?>

<div itemprop="offers" itemscope itemtype="http://schema.org/Offer">
    <?php 
        $text_bottom_price = get_post_meta($product->id, 'additional_text', true);
        if ($text_bottom_price) :
    ?>
	    <p class="ticket">
            <svg><use xlink:href="#checkbox"></use></svg>
            <span><?php echo $text_bottom_price; ?></span>
        </p>

        <div class="price">
            <div class='text'><?php pll_e('Ціна від'); ?></div>
            <?php echo ($product->get_price_html()) ? $product->get_price_html() : get_price_html(); ?>
        </div>
            
        <div class="links">
            <?php
                $checkbox_one = get_post_meta($product->id, 'checkbox_one', true);
                $checkbox_two = get_post_meta($product->id, 'checkbox_two', true);
            ?>

            <?php if ($checkbox_one) : ?>
                <a href="#" class="btn-popup" data-popup="completion"><?php pll_e('Посмотреть комплектацию'); ?></a><br>
            <?php endif; ?>

            <?php if ($checkbox_two) : ?>
                <a href="#" class="btn-popup" data-popup="comercial-offer"><?php pll_e('Коммерческое предложение'); ?></a>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <?php $text_button = get_post_meta($product->id, 'additional_text_button', true); ?>

    <a href="#" class="btn-fill btn-popup" data-popup="callback-head">
        <span><?php echo $text_button; ?></span>
        <svg><use xlink:href="#arr-right"></use></svg>
    </a>

	<meta itemprop="price" content="<?php echo esc_attr( $product->get_display_price() ); ?>" />
	<meta itemprop="priceCurrency" content="<?php echo esc_attr( get_woocommerce_currency() ); ?>" />
	<link itemprop="availability" href="http://schema.org/<?php echo $product->is_in_stock() ? 'InStock' : 'OutOfStock'; ?>" />
</div>
