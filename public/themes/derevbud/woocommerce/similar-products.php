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

<div class="similar-product">
    <div class="flex">
        <h2><?php pll_e('Похожие по цене'); ?></h2>
    </div>

    <div class="products">
        <?php if ($similar_in_price->have_posts()) :  ?>
            <?php $count = 0; $count_two = 0; while ($similar_in_price->have_posts()) : $similar_in_price->the_post(); ?>
                <?php
                    global $post;
                    $current_price_loop = ($product->get_price_html()) ? $product->get_price_html() : get_price_html();
                    $current_price_loop = preg_replace("~[^0-9]+~", '', $current_price_loop);
                    $current_price_loop = (int)$current_price_loop;
                ?>
                
                <?php if (($current_price_loop > $current_price_product) && ($current_price_loop < $current_price_product + 130000) or ($current_price_loop < $current_price_product) && ($current_price_loop + 130000 > $current_price_product ) ) : ?>
                
                    <div class="box-product">
                        <a class="image" href="<?php the_permalink(); ?>">
                            <?php the_post_thumbnail(); ?>        
                        </a>

                        <div class="foot">
                            <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>

                            <div class="price">
                                <?php pll_e('від'); ?> 
                            </div>

                            <svg><use xlink:href="#arr"></use></svg>
                        </div>
                    </div>
                    <?php if ($count >= 20) break; ?>
                    <?php $count++; ?>
                
                <?php endif; ?>

            <?php endwhile; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Проекти з категорій -->

<?php 

$arr_category = array();
$arr_category_count = array();
$terms = get_the_terms( $post->ID, 'product_cat');
$count_category;

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
                        </div>

                        <svg><use xlink:href="#arr"></use></svg>
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
                            <?php pll_e('від'); ?> 
                        </div>

                        <svg><use xlink:href="#arr"></use></svg>
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
                            <?php pll_e('від'); ?> 
                        </div>

                        <svg><use xlink:href="#arr"></use></svg>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php endif; ?>
    </div>
</div>

<?php endif; ?>