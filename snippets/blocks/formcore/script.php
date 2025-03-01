<script type="module">

  import {FormBlock} from '<?= kirby()->url('media') . '/plugins/plain/formblock/formblock.js' ?>';

  let formObject = document.getElementById('formblock_<?= str_replace('-', '', $form->id()) ?>');

  //custom template and < v4.1.0
  formObject ??= document.getElementById('<?= $form->id() ?>');

  formObject.formblock = new FormBlock({
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
  }, formObject);

</script>