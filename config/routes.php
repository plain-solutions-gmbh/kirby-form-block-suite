<?php

use Kirby\Cms\Response;
use Kirby\Toolkit\Str;
use Plain\Formblock\Request;

return [ 
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

            if (empty($out)) {
                return json_encode([
                    'state'             => "fatal",
                    'error_message'     => t('form.block.message.fatal_message'),
                    'success_message'   => "",
                    'redirect'          => "",
                    'fields'            => [],
                ]);
            }

            return end($out);
            
        }
    ],
    [
        'pattern' => 'form/download/(:all)',
        'action' => function ($params) {

            [$csrf, $page_id, $form_id, $filename] = Str::split($params, '/');
            
            $page_id = str_replace('__DS__', '/', $page_id);

            if (csrf($csrf) === false) {
                return go('error');
            }

            $formRequest = new Request(compact('page_id', 'form_id'));
            $csv = $formRequest->download();

            if ($csv === null) {
                return null;
            }

            // Return a Response object, otherwise Kirby overrides the
            // content type with text/html when sending a string
            return new Response($csv, 'text/csv', 200, [
                'Content-Disposition' => 'attachment; filename="' . $filename . '"'
            ]);
            
        }
    ]
];