

<div class="formfield__select__wrapper" >

    <select
        class="formfield__select"
        name="<?= $formfield->slug() ?>"
        id="<?= $formfield->slug() ?>"
        data-form="field"
        <?= $formfield->required('attr') ?>
        <?= $formfield->ariaAttr() ?>
        <?= $formfield->autofill(true) ?>
        >

        <?php $selected = $formfield->value() == "" ? "selected" : ""?>

        <option class="formfield__select__option--<?= $selected ?>" value="" disabled <?= $selected ?>><?= $formfield->placeholder() ?></option>

        <?php foreach ($formfield->options() as $option) : ?>

            <?php $selected = $option->selected()->isTrue() ? "selected" : ""?>

            <option class="formfield__select__option--<?= $selected ?>" value="<?= $option->slug() ?>" <?= $selected ?>>
                <?= $option->label() ?>
            </option>
        <?php endforeach ?>

    </select>
    <span class="formfield__select__chevron"></span>
</div>