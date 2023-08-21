<script type="module">

  import {FormBlock} from '<?= kirby()->url('media') . '/plugins/microman/formblock/formblock.js' ?>';

  document.getElementById('<?= $form->id() ?>').formblock = new FormBlock({
    form_name: '<?= $form->name() ?>',
    form_id: '<?= $form->id() ?>',
    form_hash: '<?= $form->hash() ?>',
    page_id: '<?= $page->id() ?>',
    language: '<?= $form->getLang(); ?>',
    endpoint: '<?= kirby()->url() ?>/form/validator',
    messages: {
      fatal: '<?= $form->message('fatal_message') ?>',
      send: '<?= $form->message('send_button') ?>',
      loading: '<?= $form->message('loading') ?>'
    } 
  });

</script>