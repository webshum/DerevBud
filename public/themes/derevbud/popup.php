<div class="popup-overlay"></div>

<div class="popup popup-callback-head">
    <button class="popup-close">
        <svg><use xlink:href="#close"></use></svg>
    </button>

    <p class="title"><?php pll_e('Manager fedback'); ?></p>

    <form action="#" class="ajax-form">
        <input type="hidden" class="title">

        <label class="label">
            <input type="tel" name="phone" placeholder="+380-__-___-____" required>
        </label>

        <label class="label">
            <textarea name="message" placeholder="<?php pll_e('When collback'); ?>"></textarea>
        </label>

        <div class="tx-r">
            <input type="submit" class="btn" value="<?php pll_e('Send'); ?>">
        </div>
    </form>
</div>

<div class="popup popup-comercial-offer">
    <button class="popup-close">
        <svg><use xlink:href="#close"></use></svg>
    </button>

    <?php
        global $product;
        $name_project = $product->name;
        $commertial = get_option('commertial');
    ?>

    <p class="title"><?php echo $name_project; ?></p>
    <p><?php pll_e('Укажите свой email и телефон для возможных вопросов. Ми отправим  коммерческое предложение на следуюций день.'); ?></p>

    <form action="#" class="ajax-form">

        <input type="hidden" name="title" value="<?php pll_e('Комерційна пропозиція'); ?>">
        <input type="hidden" name="project" value='<?php pll_e("$name_project"); ?>'>

        <div class="label">
            <input type="email" name="email" required placeholder="<?php pll_e('Укажите Email'); ?>"/>
        </div>

        <div class="label">
            <input type="tel" name="phone" required placeholder="<?php pll_e('Ваш телефон'); ?>"/>
        </div>

        <div class="tx-r">
            <input type="submit" class="btn" value="<?php pll_e('Send'); ?>"/>
        </div>
    </form>
</div>

<div  class="popup popup-completion">
    <button class="popup-close">
        <svg><use xlink:href="#close"></use></svg>
    </button>

    <p class="title"><?php pll_e('Комплектация'); ?></p>
    <strong><?php pll_e('Расчитать под ключ'); ?></strong>
    <p><?php pll_e('Оставьте свой телефон и мы Вам перезвоним'); ?></p>

    <form action="#" class="ajax-form">
        <input type="hidden" name="title" value="<?php pll_e('Комплектація'); ?>">
        <input type="hidden" name="complectation" value='<?php pll_e("$complectation_string"); ?>'>
        <input type="hidden" name="project" value='<?php pll_e("$name_project"); ?>'>

        <div class="flex">
            <input type="tel" name="phone" required placeholder="+380_________"/>

            <input type="submit" class="btn" value="<?php pll_e('Send'); ?>"/>
        </div>
    </form>
</div>

<div class="popup popup-success">
    <button class="popup-close">
        <svg><use xlink:href="#close"></use></svg>
    </button>

    <div class="success tx-c">
        <p class="title"><?php pll_e('Manager fedback'); ?></p>
        <p class="thankyou"><?php pll_e('Thanks'); ?></p>
    </div>
</div>