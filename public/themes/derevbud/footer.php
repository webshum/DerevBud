<div class="reviews-client">
	<div class="center">
	    <?php
	        $rewiews_clients =  new WP_Query(array(
	          'tag' => 'video_otzyvy_klientov'
	        ));
	    ?>

	    <?php if($rewiews_clients->have_posts()) : ?>
		    <ul>
		        <?php while ($rewiews_clients->have_posts()) : $rewiews_clients->the_post(); ?>
		            <li>
		                <div>
		                    <div class="tags"><?php echo the_tags('',' ',''); ?></div>

							<h2>
								<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
							</h2>

		                    <div class="date"><?php the_date(); ?></div>
		                </div>

		                <a class="thumb" href="<?php the_permalink(); ?>">
		                	<?php the_post_thumbnail('full'); ?>		
		                </a>
		            </li>
		        <?php endwhile; ?>
		    </ul>
	    <?php endif; ?>
	</div>
</div>

<footer id="footer">
	<div class="center">
		<div class="footer-col">
			<?php dynamic_sidebar('footer_shop_top'); ?>
		</div>

		<div class="footer-col">
			<?php dynamic_sidebar('footer_shop_bottom'); ?>
		</div>
	</div>

	<div class="center copy">
		<a href="/" class="logo">
            <img src="<?php echo bloginfo('template_url'); ?>/img/logo.png">
        </a>

        DerevBud.com.ua &copy; <?php echo date('Y'); ?>
	</div>
</footer>

<?php 

require_once 'popup.php';

wp_footer(); 

?>

<svg class="d-none">
	<symbol viewBox="0 0 512 512" id="phone">
		<path d="M164.9 24.6c-7.7-18.6-28-28.5-47.4-23.2l-88 24C12.1 30.2 0 46 0 64C0 311.4 200.6 512 448 512c18 0 33.8-12.1 38.6-29.5l24-88c5.3-19.4-4.6-39.7-23.2-47.4l-96-40c-16.3-6.8-35.2-2.1-46.3 11.6L304.7 368C234.3 334.7 177.3 277.7 144 207.3L193.3 167c13.7-11.2 18.4-30 11.6-46.3l-40-96z"/>
	</symbol>

	<symbol viewBox="0 0 448 512" id="checkbox">
		<path d="M64 32C28.7 32 0 60.7 0 96L0 416c0 35.3 28.7 64 64 64l320 0c35.3 0 64-28.7 64-64l0-320c0-35.3-28.7-64-64-64L64 32zM337 209L209 337c-9.4 9.4-24.6 9.4-33.9 0l-64-64c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0l47 47L303 175c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9z"/>
	</symbol>

	<symbol viewBox="0 0 24 24" id="close">
		<path d="M 4.9902344 3.9902344 A 1.0001 1.0001 0 0 0 4.2929688 5.7070312 L 10.585938 12 L 4.2929688 18.292969 A 1.0001 1.0001 0 1 0 5.7070312 19.707031 L 12 13.414062 L 18.292969 19.707031 A 1.0001 1.0001 0 1 0 19.707031 18.292969 L 13.414062 12 L 19.707031 5.7070312 A 1.0001 1.0001 0 0 0 18.980469 3.9902344 A 1.0001 1.0001 0 0 0 18.292969 4.2929688 L 12 10.585938 L 5.7070312 4.2929688 A 1.0001 1.0001 0 0 0 4.9902344 3.9902344 z"/>
	</symbol>

	<symbol viewBox="0 0 512 512" id="arr-right">
		<path d="M502.6 278.6c12.5-12.5 12.5-32.8 0-45.3l-128-128c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L402.7 224 32 224c-17.7 0-32 14.3-32 32s14.3 32 32 32l370.7 0-73.4 73.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0l128-128z"/>
	</symbol>

	<symbol viewBox="0 0 8 13" id="arr">
		<path d="M7.14797 6.12472L1.75578 0.732636C1.63107 0.607823 1.46458 0.539062 1.28707 0.539062C1.10955 0.539062 0.943066 0.607823 0.818351 0.732636L0.421254 1.12963C0.16286 1.38832 0.16286 1.80877 0.421254 2.06706L4.94921 6.59501L0.41623 11.128C0.291515 11.2528 0.222656 11.4192 0.222656 11.5966C0.222656 11.7742 0.291515 11.9406 0.41623 12.0655L0.813327 12.4624C0.93814 12.5872 1.10453 12.656 1.28204 12.656C1.45956 12.656 1.62604 12.5872 1.75076 12.4624L7.14797 7.0654C7.27298 6.94019 7.34164 6.77302 7.34125 6.59531C7.34164 6.4169 7.27298 6.24983 7.14797 6.12472Z"/>
	</symbol>
</svg>
</body>

</html>
