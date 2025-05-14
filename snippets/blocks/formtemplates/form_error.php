<div class="formblock__message--error" data-form="form_error">

    <div class="formblock__message__text"><?= $form->errorMessage() ?></div>

    <?php if(!$form->isValid()): ?>

        <ul class="formblock__message__list">
            <?php foreach ($form->fields()->errorFields('label') as $errorfield): ?>
                <li class="formblock__message__list__item"><?= $errorfield ?></li>
            <?php endforeach ?>
        </ul>

    <?php endif ?>

</div>