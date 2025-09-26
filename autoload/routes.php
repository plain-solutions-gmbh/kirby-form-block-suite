<?php

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

            header('Content-Type: text/csv');
            header("Content-Disposition: attachment;filename={$filename}");
            
            $formRequest = new Request(compact('page_id', 'form_id'));
            return $formRequest->download();
            
        }
    ]
];