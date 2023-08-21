
<?php if(get('page')): ?>

    <!--[Startvalidation:<?= $form->id() ?>]-->
    
    <?php

        $state = "fatal";

        if (!$form->isValid())
            $state = "invalid";

        if ($form->isSuccess())
            $state = "success";

        $fields = [];

        $toValidate = get('field_validation') ? [$form->form_field(get('field_validation'))] : $form->fields();

        foreach ($toValidate as $field) {

            array_push($fields, [
                'slug' => $field->slug()->toString(),
                'is_valid' =>  $field->isValid(),
                'message' => $form->template('field_error', ['field' => $field], $field->isValid())
            ]);
        }

        echo json_encode([
            'state'             => $state ,
            'error_message'     => $form->template('form_error', [], (!$form->isFatal() and $form->isValid())),
            'success_message'   => $form->template('form_success', [], !$form->isSuccess()),
            'redirect'          => ($form->redirect()->isTrue() && $form->isSuccess()) ? $form->success_url()->toPage()->url() : "",
            'fields'            => $fields
        ]);

    ?>
    <!--[Endvalidation]-->    

<?php else: ?>
    <?= $form->template('form_error', [], false) ?>
<?php endif ?>

