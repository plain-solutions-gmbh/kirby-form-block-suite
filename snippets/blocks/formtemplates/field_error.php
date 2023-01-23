<div class="formblock__message--error" data-form="fields_error" id="<?= $field->id() ?>-error-message">

    <ul class="formblock__message__list">

        <?php foreach ($field->getErrorMessages() as $errorfield): ?>
            <li class="formblock__message__list__item"><?= $errorfield ?></li>
        <?php endforeach ?>

    </ul>

</div>