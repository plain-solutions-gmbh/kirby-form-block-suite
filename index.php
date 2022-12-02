<?php

load([
    'microman\\FormBlueprint' => '/classes/FormBlueprint.php',
    'microman\\Form' => '/classes/Form.php',
    'microman\\FormFields' => '/classes/FormFields.php',
    'microman\\FormField' => '/classes/FormField.php',
], __DIR__);

use microman\Form;
use microman\FormBlueprint;
use Kirby\Cms\App as Kirby;

Kirby::plugin('microman/formblock', [
    'options' => [ 
        'from_email' => 'no-reply@' . Kirby::instance()->environment()->host(),
        'placeholders' => FormBlueprint::getPlaceholders(),
        'honeypot_variants' => ["email", "name", "url", "tel", "given-name", "family-name", "street-address", "postal-code", "address-line2", "address-line1", "country-name", "language", "bday"],
        'default_language' => 'en',
        'disable_inbox' => false,
        'disable_confirm' => false,
        'disable_notify' => false,
        'dynamic_validation' => true,
        'verify_content' => false,
        'translations' => [
            'en' => [
                'confirm_body'				=> '<p>Thank you for your request, we will get back to you as soon as possible.</p>',
                'confirm_subject'			=> 'Your request',
                'exists_message'			=> '<p>The form has already been filled in.</p>',
                'fatal_message'				=> '<p>Something went wrong.<br>Contact the administrator or try again later.</p>',
                'field_message'				=> 'This field is required.',
                'file_accept' 				=> 'Only following file types are accepted: {{accept}}.',
                'file_maxsize' 				=> 'File(s) must not be larger than {{ maxsize }}MB',
                'file_maxnumber'            => 'No more than {{maxnumber}} may be uploaded.',
                'file_required' 			=> 'Choose at least one File to upload.',
                'invalid_message'			=> '<p>Please check these fields: {{ fields }}.</p>',
                'notify_body'				=> '<p>{{ given-name }} send a request:</p><p>{{ summary }}</p>',
                'notify_subject'			=> 'Request from website.',
                'send_button'				=> 'Send',
                'success_message'			=> '<p>Thank you {{ given-name }}. We will get back to you as soon as possible.</p>',
            
            ],
            'de' => [
	            'confirm_body'				=> 'Danke {{ given-name }}. Wir werden uns schnellst möglich bei dir melden.',
	            'confirm_subject'			=> 'Deine Anfrage',
	            'exists_message'			=> 'Das Formular wurde bereits ausgefüllt.',
	            'fatal_message'				=> 'Es ist etwas schief gelaufen. Kontaktieren Sie den Administrator oder versuchen Sie es später noch einmal.',
	            'field_message'				=> 'Dieses Feld ist erforderlich.',
	            'file_accept' 				=> 'Nur folgende Dateitypen werden akzeptiert: {{ accept }}.',
	            'file_maxsize' 				=> 'Dateien dürfen nicht grösser als {{ maxsize }}MB sein.',
	            'file_maxnumber' 			=> 'Es dürfen nicht mehr als {{maxnumber}} hochgeladen werden.',
                'file_required' 			=> 'Wähle mindestens eine Datei zum Hochladen aus.',
	            'invalid_message'			=> 'Bitte überprüfen Sie diese Felder: {{ fields }}.',
	            'notify_body'				=> '<p>{{ given-name }} hat eine Anfrage gesendet:</p><p>{{ summary }}</p>',
	            'notify_subject'			=> 'Anfrage aus der Webseite.',
	            'send_button'			 	=> 'Senden',
	            'success_message'			=> 'Danke {{ given-name }}. Wir werden uns schnellst möglich bei dir melden.',
            ]
        ],
    ],
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
    'snippets' => [
        'formapi' => __DIR__ . '/snippets/formapi.php',
        'blocks/form' => __DIR__ . '/snippets/blocks/form.php',
        'blocks/formfields/checkbox' => __DIR__ . '/snippets/blocks/formfields/checkbox.php',
        'blocks/formfields/input' => __DIR__ . '/snippets/blocks/formfields/input.php',
        'blocks/formfields/radio' => __DIR__ . '/snippets/blocks/formfields/radio.php',
        'blocks/formfields/select' => __DIR__ . '/snippets/blocks/formfields/select.php',
        'blocks/formfields/textarea' => __DIR__ . '/snippets/blocks/formfields/textarea.php',
        'blocks/formfields/file' => __DIR__ . '/snippets/blocks/formfields/file.php'
    ],
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
                }
            ],
        ],
    ],
    'blockModels' => [
        'form' => Form::class
    ],
    'siteMethods' => [
        'getFormContainer' => function ($containerSlug = "", $pageSlug = "",  $formName = NULL) {

            //Set root of the container
            if ( in_array($pageSlug, ['site','']) ) {
                $page = site();
            } else {
                $page = ($pageSlug) ? site()->find($pageSlug) : $this->pages();
            }

            //No container demand return list of all containers
            if ($containerSlug == "") 
                return $page->index(true)->template('formcontainer');


            if ($isSite = str_contains($containerSlug  , "site/")) {
                $page = site();
                $containerSlug = str_replace("site/", "", $containerSlug);
            }

            //Return list of all containers
            if ($pageSlug != "" or $pageSlug) {
                return $page->index(true)->filterBy('slug', $containerSlug );
            }

            //Container slug given return it
            if ($container = $page->index(true)->get( $containerSlug )) {

                //Update form name
                if ($formName != "" and $container->name() != $formName )
                    $container->update(['name' => $formName]);

                return $container;
            }

            if (!$formName)
                throw new Exception("No form container to create");

            //No form container found - let's create one.
            $slugArray = explode('/', $containerSlug);
            $slug = array_pop($slugArray);

            $parent = ($isSite) ? site() : site()->find( implode("/", $slugArray) );
            
            return $parent->createChild([
                'slug' => $slug,
                'template' => 'formcontainer',
                'content' => ['name' => $formName]
            ]);

        },
        'getFormCount' => function ($container = NULL) {

            $requests = $container->drafts();
                    
            $out = [
                "count" => $requests->count(),
                "read" => $requests->filterBy('read', '')->count(),
                "fail" => $requests->filterBy([['read', ''],['error', '!=', '']])->count(),
                "state" => "ok"
            ];

            if ($out['read'] > 0) 
                $out['state'] = "new";

            if ($out['fail'] > 0) 
                $out['state'] = "error";
          
            $out['text'] = $out['read'] . "/" . $out['count'] . " " . I18n::translate('form.block.inbox.new');
    
            if ($out['state'] == "error")
                $out['text'] .= " & " . $out['fail'] . " " . I18n::translate('form.block.inbox.failed');
    
            return $out;
        }
    ],
    'routes' => [
        [
            'pattern' => 'form/validator',
            'method' => "POST",
            'action'  => function () {
                site()->visit(get('page'), get('lang'));
                $page = page()->render();
                preg_match('/\<\!-- Startform:'.get('id').' --\>(.*?)\<\!-- Endform --\>/s', $page, $out);
                return end($out);
            }
        ]
    ],
    'api' => [
        'routes' => [
            [
                'pattern' => 'form/get-requests',
                'action'  => function ()
                {
                    $out = array();
                    $hideHeader = false;
                    $container = site()->getFormContainer($this->requestQuery('form'), $this->requestQuery('page'), $this->requestQuery('name'));
                

                    if (!$container) {
                        return [];
                    }
                    
                    foreach ($container as $a) {

                        $content = [];
                        $read = 0;
    
                        foreach ($a->drafts() as $b) {
                            if ($b->read())
                                $read ++;
                            array_push($content, array_merge($b->content()->toArray(), $b->toArray()));
                        }
    
                        $out[$a->id()] = [
                            "content" => $content,
                            "openaccordion" => $a->content()->openaccordion()->value(),
                            "id" => $a->id(),
                            "uuid" => $a->content()->uuid()->value(),
                            "header" => [
                                "page" => ($a->parent()) ? $a->parent()->title()->value() : site()->title()->value(),
                                "name" => $a->name()->value(),
                                "hide" => $hideHeader or $this->requestQuery('form') != "",
                                "state" => site()->getFormCount($a),
                            ]
                        ] ;
    
                        
                    }
    
                    return array_reverse($out);
                }
            ],
            [
                'pattern' => 'form/get-requests-count',
                'action'  => function ()
                {
                    if ($container = site()->getFormContainer($this->requestQuery('form'), $this->requestQuery('page'), $this->requestQuery('name')))
                        return site()->getFormCount($container);

                    return [
                        "count" => 0,
                        "read" => 0,
                        "fail" => 0,
                        "status" => "fail"
                    ];
                        
                    
                }
            ],
            [
                'pattern' => 'form/setAccodion',
                'action'  => function ()
                {
                    if ($toSet = site()->getFormContainer( $this->requestQuery('form') )) {
                        return $toSet->update(['openaccordion' => $this->requestQuery('value')]);
                    } 
                    return true;
                }
            ],
            [
                'pattern' => 'form/set-read',
                'action'  => function ()
                {
                    if ($toUpdate = site()->getFormContainer( $this->requestQuery('form') )->draft($this->requestQuery('request')))
                        $toUpdate->update(
                            ['read' => ($this->requestQuery('state') == "false") ? "" : date('Y-m-d H:i:s', time())]
                        );
                    return true;
                }
            ],
            [
                'pattern' => 'form/delete-request',
                'action'  => function ()
                {
                    if ($toDelete = site()->getFormContainer( $this->requestQuery('form') )->draft($this->requestQuery('request')))
                        $toDelete->delete(true);

                    return true;
                }
            ],
        ]
    ],
    'translations' => [
        'en' => require __DIR__ . '/lib/languages/en.php',
        'de' => require __DIR__ . '/lib/languages/de.php'
    ]
]);
