<?php foreach ($formfield->options() as $option) : ?>

    <div class="formfield__option">
        <label class="formfield__option__label" for="<?= $formfield->id() . '-' . $option->slug() ?>">
            <input 
                class="formfield__checkbox"
                type="checkbox"
                id="<?= $formfield->id() . '-' . $option->slug() ?>"
                name="<?= $option->slug() ?>"
                value="<?= $option->slug() ?>"
                data-form="field"
                <?= $formfield->autofill(true) ?>
                <?= e($option->selected()->isTrue(), " checked") ?>
                <?= $formfield->required('attr') ?>
                <?= $formfield->ariaAttr() ?>
            >
            <?= $option->label() ?>
            <span class="formfield__checkbox__check"></span>
        </label>
    </div>

<?php endforeach ?>
