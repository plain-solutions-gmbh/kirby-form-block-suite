<?php

load([
    'microman\\FormBlueprint' => '/classes/FormBlueprint.php',
    'microman\\Form' => '/classes/Form.php',
    'microman\\FormRequest' => '/classes/FormRequest.php',
    'microman\\FormFields' => '/classes/FormFields.php',
    'microman\\FormField' => '/classes/FormField.php',
    'microman\\FormLicense' => '/classes/FormLicense.php',
], __DIR__);

use microman\Form;
use microman\FormLicense;
use microman\FormRequest;
use microman\FormBlueprint;
use Kirby\Cms\App as Kirby;
use Kirby\Filesystem\Dir as Dir;

Kirby::plugin('microman/formblock', [
    'options' => [ 
        'from_email' => 'no-reply@' . Kirby::instance()->environment()->host(),
        'placeholders' => FormBlueprint::getPlaceholders(),
        'honeypot_variants' => ["email", "name", "url", "tel", "given-name", "family-name", "street-address", "postal-code", "address-line2", "address-line1", "country-name", "language", "bday"],
        'default_language' => 'en',
        'disable_confirm' => false,
        'disable_notify' => false,
        'disable_html' => false,
        'email_field' => 'email',
        'dynamic_validation' => true
    ],
    'templates' => [ 'formcontainer' => __DIR__ . "/templates/formcontainer.php" ],
    'blueprints' => [
        'blocks/form' => [
            'name' => 'form.block.name',
            'icon' => 'email',
            'tabs' => [
                'inbox' => FormBlueprint::getInbox(),
                'form' => FormBlueprint::getForm(),
                'options' => FormBlueprint::getOptions()
            ]
        ],
        'pages/formrequest' => FormBlueprint::getBlueprint('pages/formrequest'),
        'pages/formcontainer' => FormBlueprint::getBlueprint('pages/formcontainer'),
    ],
    'snippets' => Form::snippets(__DIR__),
    'fields' => [
        'mailview' => [
            'props' => [
                'license' => function (bool $license = false) {
                    return $license ? FormLicense::licenseText() : '';
                }
            ]
        ],
    ],
    'blockModels' => [
        'form' => Form::class
    ],
    'routes' => [
        [
            'pattern' => 'form/validator',
            'method' => "POST",
            'action'  => function () {

                //Get Page
                if ((get('page') ?? "site") === 'site') {
                    $page = site();
                } else {
                    $page = site()->index(true)->find(get('page'));
                }
                site()->visit($page, get('lang'));
                $rendered_page = page()->render();
                preg_match('/\<\!--\[Startvalidation:' . get('id') . '\]--\>(.*?)\<\!--\[Endvalidation\]--\>/s', $rendered_page, $out);
                return end($out);
                    
                
            }
        ]
    ],
    'api' => [
        'routes' => [
            [
                'pattern' => 'formblock',
                'action' => function() {
                    $formRequest = new FormRequest($this->requestQuery());
                    return $formRequest->api($this->requestQuery());
                }
            ],
            [
                "pattern" => "formblock/license",
                "action" => function () {
                    return FormLicense::register(get("key"), get("email"));
                },
            ],
        ]
    ],
    'areas' => [
        'formblocks'  => function() {
            return [
                'dialogs'   => [
                    'formblock/register' => FormLicense::dialog()
                ]
            ];
        }
    ],
    'translations' => [
        'en' => require __DIR__ . '/lib/languages/en.php',
        'de' => require __DIR__ . '/lib/languages/de.php',
	'lt' => require __DIR__ . '/lib/languages/lt.php',
	'hu' => require __DIR__ . '/lib/languages/hu.php',
	'fr' => require __DIR__ . '/lib/languages/fr.php',
    ]
]);
