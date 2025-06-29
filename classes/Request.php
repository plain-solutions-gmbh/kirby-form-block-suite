<?php

namespace Plain\Formblock;

/**
 * @package   Kirby Form Block Suite
 * @author    Roman Gsponer <support@plain-solutions.net>
 * @link      https://plain-solutions.net/
 * @copyright Roman Gsponer
 * @license   https://plain-solutions.net/terms/ 
 */

use Kirby\Cms\App;
use Kirby\Toolkit\I18n;
use Kirby\Http\Request\Files;
use Kirby\Filesystem\F;
use Kirby\Toolkit\A;
use Kirby\Toolkit\Str;
use Kirby\Uuid\Uuid;

class Request
{


    /**
     * Page with form
     *
     * @var \Kirby\Cms\Page
     */
    protected $page;


    protected $page_id;

    /**
     * Request container
     *
     * @var \Kirby\Cms\Page
     */
    protected $container;

    /**
     * Current Request
     *
     * @var \Kirby\Cms\Page
     */
    protected $request;

    /**
     * Current Request
     *
     * @var \Kirby\Cms\Pages
     */
    protected $forms;

    /**
     * Magic getter function
     *
     * @param Array $props
     * 
     * @return mixed
     */
    public function __construct($props)
    {
        
        $this->page_id = $props['page_id'] ?? 'site';

        //Get Page
        if ($this->page_id === 'site') {
            $this->page = site();
        } else {
            $this->page = site()->index(true)->find($this->page_id);
        }

        $this->forms = $this->page->index(true)->filterBy('intendedTemplate', 'formcontainer');

        //Get container
        if ($props['form_id'] ?? false) {
            
            //Set Container
            $this->container = $this->forms->findBy('slug', $props['form_id']);

        } 

        //Create if not exists
        if (is_null($this->container) && ($props['allowCreate'] ?? false)) {
            $this->createContainer($props);
        }

        //Set current request
        if ($props['request_id'] ?? false) {
            $this->request($props['request_id']);
        }
            
    }

   
    /**
     * Create Formcontainer
     *
     * @param array $props
     * 
     * @return null
     */
    private function createContainer($props) {

        site()->kirby()->impersonate('kirby');

        $this->container = $this->page->childrenAndDrafts()->findBy('slug', $props['form_id']);

        $this->container ??= $this->page->createChild([
            'slug' => $props['form_id'],
            'template' => 'formcontainer',
            'content' => [ 
                'name' => $props['form_name'] ?? $props['form_id'],
            ]
        ]);

    }

    /**
     * Set/Get Request
     *
     * @param string $request_id
     * 
     * @return \Kirby\Cms\Page
     */
    private function request($request_id = null) {

        if (!is_null($request_id) && is_null($this->container) === false) {

            $this->request = $this->container->draft($request_id);
            
        }

        return $this->request;

    }

    /**
     * Create a new request
     *
     * @param array $content
     * @param string $requestid
     * 
     * @return \Kirby\Cms\Page|null
     */
    public function create($content, $requestid): \Kirby\Cms\Page|null
    {


        if (is_null($this->request($requestid))) {

            site()->kirby()->impersonate('kirby');

            $this->request = $this->container->createChild([
                'slug' => $requestid,
                'template' => 'formrequest',
                'content' => $content
            ]);

            return $this->request;
        }

        return null;
        
    }

    /**
     * Update the request as page
     *
     * @param array $input Changes
     * 
     * @return \Kirby\Cms\Page|null
     * 
     */
    public function update(array $input = [])
    {

        if (!is_null($this->request)) {

            site()->kirby()->impersonate('kirby');
            return $this->request = $this->request->update($input);

        }

        return null;
    }

    /**
     * Update the formdata of the request
     *
     * @param array $input Changes
     * 
     * @return \Kirby\Cms\Page|null
     * 
     */
    public function updateFormdata(array $input = [])
    {

        $fd = json_decode($this->request->content()->formdata()->value(), true);
        $fd = array_merge($fd, $input);
        return $this->update(['formdata' => json_encode($fd)]);
        
    }
    

    /**
     * Delete request
     * 
     * @return \Kirby\Cms\Page|null
     * 
     */
    public function delete () {

        if (!is_null($this->request)) {

            site()->kirby()->impersonate('kirby');
            $this->request->delete();

            if($this->container->hasDrafts() === false) {
                $this->container->delete();
            }
            
            return true;
        }

        return false;
    }


    /**
     * Update container
     *
     * @param array $input Changes
     * 
     * @return \Kirby\Cms\Page
     * 
     */

    public function updateContainer(array $input = [])
    {

        site()->kirby()->impersonate('kirby');
        return $this->container = $this->container->update($input);

    }

    
    private function verifyName($form_name) {

        if (is_null($this->container) === false && $this->container->name()->value() !== $form_name) {
            $this->updateContainer([
                'name' => $form_name
            ]);
        }

    }

    /**
     * Get infos of requests
     *
     * @param array $kind Kind of infos
     * @param \Kirby\Cms\Page $container Container to init
     * 
     * @return array|string
     * 
     */
    public function info($params = [])
    {

        if (array_key_exists('form_name', $params)) {
            $this->verifyName($params['form_name']);
        }

        return $this->infoPart('array');


    }

    public function infoPart($kind = "count", $container = null)
    {

        $container ??= $this->container;
        $counter = [0,0,0];

        if (is_null($container) === false && $container->hasDrafts()) {
            $counter = [
                $container->drafts()->count(),
                $container->drafts()->filterBy('read', '')->count(),
                $container->drafts()->filterBy([['read', ''],['error', '!=', '']])->count()
            ];
        }
    


        switch ($kind) {
            case 'count':
                return $counter[0];
                break;
            
            case 'read':
                return $counter[1];
                break;
            
            case 'fail':
                return $counter[2];
                break;
            
            case 'state':
                if ($this->infoPart('read', $container) > 0)
                    return 'positive';

                if ($this->infoPart('fail', $container) > 0)
                    return 'negative';

                return 'info';
                break;

            case 'theme':
                if ($this->infoPart('read', $container) > 0)
                    return 'positive';

                if ($this->infoPart('fail', $container) > 0)
                    return 'negative';

                return 'gray';
                break;

            case 'text':
                $text = $this->infoPart('read', $container) . "/" . $this->infoPart('count', $container) . " " . I18n::translate('form.block.inbox.new');

                if ($this->infoPart('fail', $container) > 0)
                    $text .= " & " . $this->infoPart('fail', $container) . " " . I18n::translate('form.block.inbox.failed');

                return $text;
                break;
            
            default:
            
                return [
                    "count" => $this->infoPart('count', $container),
                    "read" => $this->infoPart('read', $container),
                    "fail" => $this->infoPart('fail', $container),
                    "state" => $this->infoPart('state', $container),
                    "theme" => $this->infoPart('theme', $container),
                    "text" => $this->infoPart('text', $container)
                ];
        }
                    
        
    }

    private function downloadLink($form_id, $title) {

        $encodedPageId = str_replace('/', '__DS__', $this->page_id);

        return A::join([
            kirby()->url(),
            'form',
            'download',
            csrf(),
            $encodedPageId,
            $form_id,
            Str::slug($title)
        ], '/') . '.csv';
    }

    public function download()
    {

        $output = null;

        function parseField($field) {
            $array = json_decode($field->value(), true);
            unset($array['summary']);
            return array_values($array);
        }

        foreach ($this->container->drafts()->sortBy('received', 'desc') as $b) {

            $content = $b->content();
            $received = $content->received()->toValue();
            $id = $content->slug();

            $output ??= A::join(['ID', ...parseField($content->formfields()), 'Received'], ';') . "\n";
            $output .= A::join([$id, ...parseField($content->formdata()), $received], ';') . "\n";
            
        }
        
        return $output;
    }

    /**
     * Get cinfos about request
     *
     * @param array $props Properies to add
     * 
     * @return array|string
     * 
     */
    public function requestsArray($props = []) {
        
        $out = array();

        foreach ($this->forms as $a) {

            $content = [];
            $read = 0;
            $filter = $props['filter'];

            if (count($filter) > 0 && in_array($a->slug(), $filter) === false) {
                continue;
            }

            foreach ($a->drafts()->sortBy('received', 'desc') as $b) {
                if ($b->read())
                    $read ++;
                array_push($content, array_merge($b->content()->toArray(), $b->toArray()));
            }

            $pagetitle = ($a->parent()) ? $a->parent()->title()->value() : site()->title()->value();
            $formtitle = $pagetitle . " - " .  $a->name()->value();

            $out[$a->slug()] = [
                "content" => $content,
                "id" => $a->slug(),
                "page" => $this->page_id,
                "uuid" => $a->content()->uuid()->value(),
                "header" => [
                    "page" => $pagetitle,
                    "name" => $formtitle,
                    "state" => $this->infoPart('array', $a),
                    "download" => $this->downloadLink($a->slug(), $formtitle)
                ]
            ] ;
            
        }

        return $out;
    }

    /**
     * Download Files in the Form
     * 
     * @return mixed
     */
    public function uploadFiles($fields)
    {
        
        $file_obj = new Files();

        //Become almighty
        kirby()->impersonate('kirby');

        //Prepare array for mail attachment
        $attachments = array();

        //Prepare array for request
        $fileinfos = [];

        //Prepare array for filename colision
        $filenames = array();

        //Increser for filename colision
        $index = 1;

        //Walker through file fields
        foreach ($fields as $field_slug) {

            //Prepare Field
            $filearray = [];

            //Walker through files in fields
            foreach ($file_obj->get($field_slug) as $f) {

                if ($f['error'] > 0) {
                    break;
                }

                $name = F::safeName($f['name']);

                //Check for filename colision
                if ( in_array($name, $filenames ) ){

                    $name = explode('.', $name);
                    $name[0] .= "_" . $index;
                    $name = implode('.', $name);
                    $index ++;
                }

                //Insert name to filename colision
                array_push($filenames, $name);

                $tmpName  = pathinfo($f['tmp_name']);
                
                $filename = $tmpName['dirname']. '/'. $name;

                rename($f['tmp_name'], $filename);

                //Push File for email upload
                array_push($attachments, $filename);

                //Save file
                $localfile = $this->request->createFile([
                    'source'     => $filename,
                    'template'   => 'formfile',
                    'filename'   => $name,
                    'content'   => [
                        'filename'   =>  $f['name'],
                        'field'      => $field_slug
                    ]
                ]);

                //Push fileinfos
                array_push($filearray, [
                    'name' => F::safeName($f['name']),
                    'tmp_name' => $name,
                    'location' => $localfile->url(),
                    'size' => $f['size']
                ]);

            }

            $fileinfos[$field_slug] = $filearray;

            
        }

        $this->update(['attachment' => json_encode($fileinfos)]);

        return $attachments;
        
    }

    /** 
    * API from Panel 
    *
    * @param array $res Responding data
    * 
    * @return mixed
    * 
    */
    public function api($res) {

        $params = json_decode($res['params'] ?? "", true);
        return $this->{$res['action']}($params);
    }

}

?>