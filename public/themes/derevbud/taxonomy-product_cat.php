<?php get_header(); ?>

<div class="wrapper" id="archive-products">
	<div class="center main">
		<?php 
			$category = get_queried_object();
			$cat_id = $category->term_id;
			$title =  get_term_meta( $cat_id, 'title', 1 );
			$json = json_encode($category);
			$id = ($cat_id != 273) ? $cat_id : 0;
		?>

        <archive-products :cat-id="'<?php echo $id ?>'"></archive-products>

		<?php
            $current_page =  get_query_var( 'paged' );

            if ( ($current_page != 0) && ($current_page != 1) ) {
                $title = $title . " - страница " . $current_page;
            }

			$description =  get_term_meta( $cat_id, 'description', 1 );

		?>
        <div class="term-description"><?php echo $description; ?></div>
	</div>
</div>

<?php get_footer(); ?>