<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
    <?php wp_body_open(); ?>
    <!-- HEADER -->
    <header id="header">
        <div class="center">
            <a href="/" class="logo">
                <img src="<?php echo bloginfo('template_url'); ?>/img/logo.png">
            </a>

            <div class="right">
                <div class="head-contacts">
                    <?php 
                        pll_the_languages([
                            'dropdown' => 1,
                            'display_names_as' => 'slug'
                        ]); 

                        $phones = get_option('phone');

                        if (!empty($phones)) $phones = explode(';', $phones);
                    ?>    

                    <?php if (!empty($phones[0])) : ?>
                        <div class="head-phones">
                            <a href="tel:<?php echo explode(':', $phones[0])[0] ?>">
                                <span>
                                    <?php echo explode(':', $phones[0])[0] ?>
                                </span>
                                <svg class="arr"><use xlink:href="#arr"></use></svg>
                                <svg class="phone d-none"><use xlink:href="#phone"></use></svg>
                            </a>

                            <div class="inner">
                                <form action="#">
                                    <h4><?php pll_e('Call us'); ?></h4>

                                    <?php foreach ($phones as $phone) : ?>
                                        <a href="tel:<?php echo explode(':', $phone)[0] ?>">
                                            <?php echo explode(':', $phone)[0] ?>
                                            <?php echo explode(':', $phone)[1] ?>
                                        </a>
                                    <?php endforeach; ?>

                                    <h4><?php pll_e('Or live number'); ?></h4>

                                    <input type="tel" name="tel" placeholder="+380" required> 

                                    <div class="foot">
                                        <button class="btn-back">
                                            <?php pll_e('Back'); ?>  
                                        </button>
                                        <button type="submit">
                                            <?php pll_e('Send'); ?>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    <?php endif; ?>

                    <a href="#" class="btn btn-popup" data-popup="callback-head"><?php pll_e('Consultation') ?></a>
                </div>

                <nav class="nav" role="navigation">
                    <?php 
                        wp_nav_menu([
                            'theme_location' => 'header',
                            'container' => '',
                            'before' => '<button class="prev"><svg width="15" height="15"><use xlink:href="#arr"></use></svg></button>',
                        ]); 
                    ?>

                    <div class="d-none s-991 foot">
                        <a href="#" class="btn btn-popup" data-popup="callback-head"><?php pll_e('Consultation') ?></a>
                    </div>
                </nav>

                <button class="btn-nav">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
            </div>
        </div>
    </header>
    <!-- // HEADER -->