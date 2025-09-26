<?php 

use Kirby\Cms\App;
use Plain\Formblock\Blueprint;

return [ 
    'from_email' => 'no-reply@' . App::instance()->environment()->host(),
    'placeholders' => Blueprint::getPlaceholders(),
    'honeypot_variants' => ["email", "name", "url", "tel", "given-name", "family-name", "street-address", "postal-code", "address-line2", "address-line1", "country-name", "language", "bday"],
    'default_language' => 'en',
    'disable_confirm' => false,
    'disable_notify' => false,
    'disable_html' => false,
    'email_field' => 'email',
    'dynamic_validation' => true
]

?>