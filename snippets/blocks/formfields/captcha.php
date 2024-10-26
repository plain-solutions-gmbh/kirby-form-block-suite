<?php 

    $add2 = rand(1, 9);
    $add1 = rand(1, 9) * 10 + rand(1, 9 - $add2);


    $text =  $formfield->ask()->or($formfield->message('captcha_ask'));

?>

<p><?= "{$text} {$add1} + {$add2}" ?></p>

<input class="formfield--hidden" type="hidden" name="captcha-id" value="<?= "{$add1}_{$add2}" ?>">

<input
    class="formfield__input"
    type="input"
    id="<?= $formfield->slug() ?>"
    name="<?= $formfield->slug() ?>"
    placeholder="<?= $formfield->placeholder() ?>"
    value="<?= $formfield->value() ?>"
    data-form="field"
    autocomplete="off"
    <?= $formfield->required('attr') ?>
    <?= $formfield->ariaAttr() ?>
/>
