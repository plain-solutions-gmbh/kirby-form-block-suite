<?php 
    /*
    *
    * DO NOT MODIFY THE FILES IN THE PLUGIN FOLDER. COPY FILE TO 'SITE/SNIPPETS/BLOCKS' AND EDIT THERE. (EXCEPT THE FORMCORE FOLDER!)
    *
    * ALLOW:
    * - Modify and add attributes of each element (except id, data-form, data-id or the attributes of the form element)
    * - Add elements
    *
    * DISALLOW:
    * - Change or remove the attributes id, data-form, data-id of any templates
    * - Modify or change the calls of the templates: hidden, script and validation 
    * - Copy the templates files hidden.php, script.php or validation.php into your site folder! (May break the plugin after updates)
    *
    */
?>

<?php if($block->showForm()): ?>
  
    <form class="formblock" method="post" id="<?= $block->id() ?>" novalidate enctype="multipart/form-data">

        <?= $block->template('fields') ?>
        
        <?= $block->template('form_error') ?>

        <?= $block->template('hidden') ?>
        <?= $block->template('submit') ?>

    </form>

    <?= $block->template('script') ?>

<?php else: ?>

    <?= $block->template('validation') ?>

<?php endif ?>