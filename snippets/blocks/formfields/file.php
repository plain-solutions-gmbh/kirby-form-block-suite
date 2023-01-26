
<?php $isMultiple = $formfield->maxnumber()->value() > 1; ?>
<input
    class="formfield__file"
    type="file"
    id="<?= $formfield->id() ?>"
    name="<?= $formfield->slug() . '[]' ?>"
    accept="<?= $formfield->accept() ?>"
    data-form="files"
    <?= $formfield->autofill(true) ?>
    <?= $formfield->required('attr') ?>
    <?= $formfield->ariaAttr() ?>
    <?= ($isMultiple) ? "multiple" : "" ?>

    />
    