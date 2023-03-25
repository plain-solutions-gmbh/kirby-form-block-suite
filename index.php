<?php

load([
    'microman\\FormBlueprint' => '/classes/FormBlueprint.php',
    'microman\\Form' => '/classes/Form.php',
    'microman\\FormRequest' => '/classes/FormRequest.php',
    'microman\\FormFields' => '/classes/FormFields.php',
    'microman\\FormField' => '/classes/FormField.php',
    'microman\\License' => '/classes/License.php',
], __DIR__);

use microman\Form;
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
        'dynamic_validation' => true
    ],
    'templates' => [ 'formcontainer' => __DIR__ . "/templates/formcontainer.php" ],
    'blueprints' => [
        'blocks/form' => [
            'name' => 'form.block.fromfields',
            'icon' => 'form',
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
    'hooks' => [
        'page.update:before' => function ($page, $values, $strings) {
            $content = json_encode($values);
            foreach ($page->drafts()->template('formcontainer') as $container) {
                if(!str_contains($content, $container->slug())) {
                    $container->delete(true);
                };
            }
        }
    ],
    'fields' => [
        'mailview' => [
            'props' => [
                'parent' => function () {
                    return false;
                },
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
                site()->visit(get('page'), get('lang'));
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
            ]
        ]
    ],
    'translations' => [
        'en' => require __DIR__ . '/lib/languages/en.php',
        'de' => require __DIR__ . '/lib/languages/de.php',
	    'lt' => require __DIR__ . '/lib/languages/lt.php'
    ]
]);
