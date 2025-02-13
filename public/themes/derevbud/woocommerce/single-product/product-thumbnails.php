<?php
/**
 * Single Product Thumbnails
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/product-thumbnails.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://woocommerce.com/document/template-structure/
 * @package     WooCommerce\Templates
 * @version     3.5.1
 */

defined( 'ABSPATH' ) || exit;

// Note: `wc_get_gallery_image_html` was added in WC 3.3.2 and did not exist prior. This check protects against theme overrides being used on older versions of WC.
if ( ! function_exists( 'wc_get_gallery_image_html' ) ) {
	return;
}

global $post, $product, $woocommerce;

$attachment_ids = $product->get_gallery_image_ids();

?>

<ul class="product-gallery">

	<?php if ($attachment_ids) : ?>
		<?php foreach ($attachment_ids as $attachment_id) : $props = wc_get_product_attachment_props( $attachment_id, $post ); ?>

			<li><a href="<?php echo $props['url']; ?>" data-lightbox="gallery"><img src="<?php echo $props['url']; ?>" loading="lazy" alt="<?php echo $props['alt']; ?>"></a></li>

		<?php endforeach; ?>
	<?php endif; ?>
	
</ul>
