<?php $hpot = $form->honeypotId(); ?>

<div class="formfield__hpot" aria-hidden="true">
    <label for="<?= $hpot ?>"> <?= ucfirst($hpot) ?></label>
    <input 
		type="search" 
		id="<?= $hpot ?>"
		name="<?= $hpot ?>"
		data-form="field"
		value=""
		autocomplete="off"
		tabindex="1000"
		required 
	/>
</div>

<style>
	.formfield__hpot {
		opacity: 0.001;
		position: absolute;
		z-index: -1;
	}
</style>

<input class="formfield--hidden" type="hidden" name="id" value="<?= $form->id() ?>">
<input class="formfield--hidden" type="hidden" name="hash" value="<?= $form->hash() ?>">
