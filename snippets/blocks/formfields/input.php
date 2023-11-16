
<input
    class="formfield__input"
    type="<?= $formfield->inputtype() ?>"
    id="<?= $formfield->slug() ?>"
    name="<?= $formfield->slug() ?>"
    placeholder="<?= $formfield->placeholder() ?>"
    value="<?= $formfield->value() ?>"
    data-form="field"
    <?= $formfield->autofill(true) ?>
    <?= $formfield->required('attr') ?>
    <?= $formfield->ariaAttr() ?>
/>
