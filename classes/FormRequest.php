<?php

namespace microman;

use Kirby\Cms\Page;
use Kirby\Cms\Site;
use Kirby\Cms\Template;
use Kirby\Toolkit\I18n;
use Kirby\Toolkit\F;
use Kirby\Uuid\Uuid;
use Kirby\Http\Request\Files;

class FormRequest
{


    /**
     * Page with form
     *
     * @var \Kirby\Cms\Page
     */
    protected $page;

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
     * Magic getter function
     *
     * @param Array $props
     * 
     * @return mixed
     */
    public function __construct($props)
    {
        
        //Get Page
        if (($props['page_id'] ?? "site") === 'site') {
            $this->page = site();
        } else {
            $this->page = site()->index(true)->find($props['page_id']);
        }

        //Get container
        if ($props['form_id'] ?? false) {
            
            //Set Container
            $this->container = $this->page->draft($props['form_id']);
            

            //Create if not exists
            if (!$this->container) {
                $this->createContainer($props);
            }

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

        $this->container = $this->page->createChild([
            'slug' => $props['form_id'],
            'template' => 'formcontainer',
            'content' => [ 
                'name' => $props['form_name'] ?? "",
                'openaccordion' => "false",
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

        if (!is_null($request_id)) {

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

            return $this->request->delete();
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

    /**
     * Get infos of requests
     *
     * @param array $kind Kind of infos
     * @param \Kirby\Cms\Page $container Container to init
     * 
     * @return array|string
     * 
     */
    public function info($kind = "count", $container = null)
    {

        if(!is_null($container))
            $this->container = $container;

        $counter = [0,0,0];

        if ($this->container->hasDrafts()) {
            $counter = [
                $this->container->drafts()->count(),
                $this->container->drafts()->filterBy('read', '')->count(),
                $this->container->drafts()->filterBy([['read', ''],['error', '!=', '']])->count()
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
                if ($this->info('read') > 0)
                    return 'new';

                if ($this->info('fail') > 0)
                    return 'error';

                return 'ok';
                break;

            case 'text':
                $text = $this->info('read') . "/" . $this->info('count') . " " . I18n::translate('form.block.inbox.new');

                if ($this->info('fail') > 0)
                    $text .= " & " . $this->info('fail') . " " . I18n::translate('form.block.inbox.failed');

                return $text;
                break;
            
            default:
            
                return [
                    "count" => $this->info('count'),
                    "read" => $this->info('read'),
                    "fail" => $this->info('fail'),
                    "state" => $this->info('state'),
                    "text" => $this->info('text'),
                ];
        }
                    
        
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

        
        if(is_null($this->container) || $this->container == []) {
            $container = $this->page->index(true)->template('formcontainer');
        } else {
            $container = [$this->container];
        }


        foreach ($container as $a) {

            $content = [];
            $read = 0;

            foreach ($a->drafts()->sortBy('received', 'desc') as $b) {
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
                    "hide" => $props['hideheader'],
                    "state" => $this->info('array', $a),
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