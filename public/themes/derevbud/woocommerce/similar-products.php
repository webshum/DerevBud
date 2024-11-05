<?php

/* Найбільш схожі за ціною */
$similar_in_price = new WP_Query(array(
    'post_type' => 'product',
    'post_status' => 'publish',
    'posts_per_page' => 100000,
    'orderby' => 'rand'
));

$min_price = get_option('range_min');
$max_price = get_option('range_max');
$min_price = (int)$min_price;
$max_price = (int)$max_price;

$current_price_product = ($product->get_price_html()) ? $product->get_price_html() : get_price_html();
$current_price_product = preg_replace("~[^0-9]+~", "", $current_price_product);
$current_price_product = (int)$current_price_product;

?>

<!-- Проекти з категорій -->

<?php 

$arr_category = array();
$arr_category_count = array();
$terms = get_the_terms( $post->ID, 'product_cat');
$count_category = 0;

foreach ($terms as $key => $val) {
    $arr_category[$key]['slug'] = $val->slug;
    $arr_category[$key]['name'] = $val->name;

    $arr_category_count[$key]['slug'] = $val->slug;

    if ($count_category >= 2) break;
    $count_category++;
}

$arr_elem_cat = array_shift($arr_category);

$popular_product = new WP_Query(array(
    'post_type' => 'product',
    'posts_per_page' => 3,
    'orderby' => 'rand',
    'product_cat' => $arr_elem_cat['slug']
));

?>

<?php if (count($arr_category_count) >= 3) : ?>

<div class="similar-product">
    <div class="flex">
        <h2><?php echo $arr_elem_cat['name'];  ?></h2>
        <a href="/product-category/vse-proekty-derevyannyh-domov/" class="all-project"><?php pll_e('Смотреть все'); ?></a>
    </div>

    <div class="products">
        <?php if ($popular_product->have_posts()) :  ?>
            <?php while ($popular_product->have_posts()) : $popular_product->the_post(); ?>
                <div class="box-product">
                    <a class="image" href="<?php the_permalink(); ?>">
                        <?php the_post_thumbnail(); ?>        
                    </a>

                    <div class="foot">
                        <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>

                        <div class="price">
                            <?php echo get_price_html_by_id(get_the_ID()); ?>
                        </div>

                        <a href="<?php the_permalink(); ?>">
                            <svg><use xlink:href="#arr"></use></svg>
                        </a>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php endif; ?>
    </div>

</div>

<?php 

$arr_elem_cat = array_shift($arr_category); 

$popular_product = new WP_Query(array(
    'post_type' => 'product',
    'posts_per_page' => 3,
    'orderby' => 'rand',
    'product_cat' => $arr_elem_cat['slug']
));

?>

<div class="similar-product">
    <div class="flex">
        <h2><?php echo $arr_elem_cat['name'];  ?></h2>
        <a href="/product-category/vse-proekty-derevyannyh-domov/" class="all-project"><?php pll_e('Смотреть все'); ?></a>
    </div>

    <div class="products">
        <?php if ($popular_product->have_posts()) :  ?>
            <?php while ($popular_product->have_posts()) : $popular_product->the_post(); ?>
                <div class="box-product">
                    <a class="image" href="<?php the_permalink(); ?>">
                        <?php the_post_thumbnail(); ?>        
                    </a>

                    <div class="foot">
                        <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>

                        <div class="price">
                            <?php echo get_price_html_by_id(get_the_ID()); ?>
                        </div>

                        <a href="<?php the_permalink(); ?>">
                            <svg><use xlink:href="#arr"></use></svg>
                        </a>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php endif; ?>
    </div>
</div>

<?php 

$arr_elem_cat = array_shift($arr_category); 

$popular_product = new WP_Query(array(
    'post_type' => 'product',
    'posts_per_page' => 3,
    'orderby' => 'rand',
    'product_cat' => $arr_elem_cat['slug']
));

?>

<div class="similar-product">
    <div class="flex">
        <h2><?php echo $arr_elem_cat['name'];  ?></h2>
        <a href="/product-category/vse-proekty-derevyannyh-domov/" class="all-project"><?php pll_e('Смотреть все'); ?></a>
    </div>

    <div class="products">
        <?php if ($popular_product->have_posts()) :  ?>
            <?php while ($popular_product->have_posts()) : $popular_product->the_post(); ?>
                <div class="box-product">
                    <a class="image" href="<?php the_permalink(); ?>">
                        <?php the_post_thumbnail(); ?>        
                    </a>

                    <div class="foot">
                       <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>

                        <div class="price">
                            <?php echo get_price_html_by_id(get_the_ID()); ?>
                        </div>

                        <a href="<?php the_permalink(); ?>">
                            <svg><use xlink:href="#arr"></use></svg>
                        </a>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php endif; ?>
    </div>
</div>

<?php endif; ?>