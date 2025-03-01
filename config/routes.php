<?php 

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
                    'fields'            => []
                ]);
            }

            return end($out);
            
        }
    ]
];