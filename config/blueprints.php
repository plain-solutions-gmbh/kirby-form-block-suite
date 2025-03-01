<?php

use Plain\Formblock\Blueprint;

return [
    'blocks/form' => [
        'name' => 'form.block.name',
        'icon' => 'email',
        'tabs' => [
            'inbox' => Blueprint::getInbox(),
            'form' => Blueprint::getForm(),
            'options' => Blueprint::getOptions()
        ]
    ],
    'pages/formrequest' => Blueprint::getBlueprint('pages/formrequest'),
    'pages/formcontainer' => Blueprint::getBlueprint('pages/formcontainer'),
];