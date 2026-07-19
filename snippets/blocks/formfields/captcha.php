<?php

$mode = option('plain.formblock.captcha.mode', 'math');
$ask_text = $formfield->message('captcha_ask');

?>

<?php if ($mode === 'math'): ?>
    <!-- Math CAPTCHA -->
    <?php
    $max1 = option('plain.formblock.captcha.math.max1');
    $max2 = option('plain.formblock.captcha.math.max2');
    $add1 = rand(1, $max1);
    $add2 = rand(1, $max2);
    ?>

    <p><?= "{$ask_text} {$add1} + {$add2}" ?></p>

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
        <?= $formfield->ariaAttr() ?> />

<?php elseif ($mode === 'hcaptcha'): ?>


    <!-- hCaptcha -->
    <?php
    $sitekey = option('plain.formblock.captcha.hcaptcha.sitekey');
    $widgetId = 'h-captcha-' . $formfield->slug();
    $onloadCallback = 'formblockHCaptchaOnload_' . preg_replace('/[^A-Za-z0-9_]/', '_', $formfield->slug());
    ?>

    <?php if (!empty($sitekey)): ?>
        <div class="h-captcha" data-sitekey="<? $sitekey ?>"></div>
        <script src="https://js.hcaptcha.com/1/api.js" async defer></script>
    <?php else: ?>
        <p style="color: red;">hCaptcha sitekey not configured</p>
    <?php endif; ?>

<?php elseif ($mode === 'recaptcha_v2'): ?>


    <!-- reCAPTCHA v2 -->
    <?php $sitekey = option('plain.formblock.captcha.recaptcha.sitekey'); ?>

    <?php if (!empty($sitekey)): ?>
        <div class="g-recaptcha" data-sitekey="<?= $sitekey ?>"></div>
        <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <?php else: ?>
        <p style="color: red;">reCAPTCHA sitekey not configured</p>
    <?php endif; ?>

<?php elseif ($mode === 'recaptcha_v3'): ?>


    <!-- reCAPTCHA v3 (Invisible) -->
    <?php
    $sitekey = option('plain.formblock.captcha.recaptcha.sitekey');
    $action = option('plain.formblock.captcha.recaptcha.v3.action', 'form_submit');
    ?>

    <?php if (!empty($sitekey)): ?>
        <script src="https://www.google.com/recaptcha/api.js?render=<?= $sitekey ?>"></script>
        <script>
            grecaptcha.ready(function() {
                grecaptcha.execute('<?= $sitekey ?>', {
                    action: '<?= $action ?>'
                }).then(function(token) {
                    document.getElementById('g-recaptcha-response').value = token;
                });
            });
        </script>
        <input type="hidden" id="g-recaptcha-response" name="g-recaptcha-response">
    <?php else: ?>
        <p style="color: red;">reCAPTCHA sitekey not configured</p>
    <?php endif; ?>

<?php else: ?>
    <!-- CAPTCHA unknown -->
<?php endif; ?>