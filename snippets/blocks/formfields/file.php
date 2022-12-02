
<?php $isMultiple = $formfield->maxnumber()->value() > 1; ?>
<input
    type="file"
    id="<?= $formfield->slug() ?>"
    name="<?= $formfield->slug() . (($isMultiple) ? '[]' : '') ?>"
    accept="<?= $formfield->accept() ?>"
    <?= $formfield->autofill(true) ?>
    <?= $formfield->required('attr') ?>
    <?= $formfield->ariaAttr() ?>
    <?= ($isMultiple) ? "multiple" : "" ?>

    />