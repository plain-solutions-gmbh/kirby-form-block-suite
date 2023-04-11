<div class="formfield__hpot">
    <label for="<?= $form->honeypotId() ?>" aria-hidden="true"> <?= ucfirst($form->honeypotId()) ?></label>
    <input type="search" id="<?= $form->honeypotId() ?>" name="<?= $form->honeypotId() ?>" aria-hidden="true" value="" autocomplete="off" tabindex="1000" required />
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
