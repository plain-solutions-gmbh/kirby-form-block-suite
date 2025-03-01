<?php 

use Plain\Formblock\Request;

return [
    [
        'pattern' => 'formblock',
        'action' => function() {
            $formRequest = new Request($this->requestQuery());
            return $formRequest->api($this->requestQuery());
        }
    ]
];