<textarea
    class="formfield__textarea"
    id="<?= $formfield->slug() ?>"
    name="<?= $formfield->slug() ?>"
    rows="<?= $formfield->row() ?>"
    placeholder="<?= $formfield->placeholder() ?>"
    data-form="field"
    maxlength="<?= $formfield->man() ?>"
    <?= $formfield->required('attr') ?>
    <?= $formfield->ariaAttr() ?>
    <?= $formfield->autofill(true) ?>
><?= $formfield->value() ?></textarea>
