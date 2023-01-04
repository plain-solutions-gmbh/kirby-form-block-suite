

<?php if(option('microman.formblock.dynamic_validation')): ?>
  
  <script>

  document.addEventListener("submit", function(e){
    const id = e.target.id.value;
    if (id == '<?= $form->id() ?>') {
      
      const output = document.getElementById("form_"+id);
      const formData = new FormData(event.target);

      formData.append("page", '<?= $page->id() ?>' );
      formData.append("lang", '<?= $form->getLang(); ?>' );

      const request = new XMLHttpRequest();
      request.open("POST", "/form/validator", true);
      request.onload = (progress) => {
        if(request.status === 200) {
          const regex = /<!-- Redirect: (.*?) -->/gm;
          if(url = regex.exec(request.response)?.[1]) location.href = url;
           output.innerHTML = request.response;
        } else {
          output.innerHTML = '<?= $form->message('fatal_message') ?>';
        }

      };

      request.send(formData);
      event.preventDefault();

    }
  });
  </script>
<?php endif ?>
