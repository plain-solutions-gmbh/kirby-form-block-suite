<?php

namespace microman;

use Kirby\Cms\Blocks;
use Kirby\Http\Environment;
use Kirby\Filesystem\F;

class FormFields extends Blocks
{

    /**
     * Visitor send some values
     *
     * @var Bool
     */
    protected $isFilled;

    /**
     * Magic getter function
     *
     * @param array $params
     * @param object $parent
     * @param string $formid
     * 
     * @return mixed
     */
    public function __construct(array $params, object $parent, string $formid)
    {
        $this->parent = $parent;
        
        //Main check if Form is filled.
        $this->isFilled = Environment::getGlobally('REQUEST_METHOD') === 'POST' && (get('id') or get($formid));

        //Add field object to class
        foreach ($params as $formfield) {

            $this->add(
                new FormField(
                    [
                        "content" => $formfield['content'],
                        'id' => $formfield['id'],
                        //Escape the prefixes form formfield type
                        'type' => preg_replace('/_[0-9]+_|\_/', '/', $formfield['type']),
                        'isFilled' => $this->isFilled()
                    ], $this->parent()
                )
            );

        }
    }

    /**
     * Returns method or field
     *
     * @param string $key
     * @param mixed $arguments
     * 
     * @return mixed
     */
    public function __call(string $key, $arguments)
    {
        // Return method
        if ($this->hasMethod($key) === true) {
            return $this->callMethod($key, $arguments);
        }

        //Return field
        if ($field = $this->findBy('slug', str_replace('_', '-', $key)))
            return $field;

        return NULL;
    }

    /**
     * Download Files in the Form
     * 
     * @return mixed
     */
    public function uploadFiles($container)
    {
        $uploadet = [];

        //Walker through file fields
        foreach ($this as $f) {
                
            if ($tmp_files = $f->files('tmp_name')) {

                //Prepare file info array
                $upload_item = array();

                //Become almighty
                kirby()->impersonate('kirby');

                //Walker through files in fields
                foreach ($tmp_files as $i => $file) {

                    //Read original filename
                    $name = $f->files('name', $i);

                    //Create a random filename
                    $tmp_name = implode('.', [bin2hex(random_bytes(18)), pathinfo($name, PATHINFO_EXTENSION)]);

                    //Save file
                    $localfile = $container->createFile([
                        'source'     => $file,
                        'template'   => 'formfile',
                        'filename'   => $tmp_name,
                        'content'   => [
                            'filename'   => $name,
                            'field'      => $f->slug()
                        ]
                    ]);

                    //Push fileinfos
                    array_push($upload_item, [
                        'name' => F::safeName($name),
                        'tmp_name' => $tmp_name,
                        'location' => $localfile->url(),
                        'size' => $f->files('size', $i)
                    ]);
                }

                //Assign fileinfos 
                $uploadet[$f->slug()->value()] = $upload_item;
            }
        }

        return $uploadet;
        
    }


    /**
     * Returns a list of fields with errors
     * 
     * @param string|NULL $separator Unset returns Array
     * @return string|array
     */
    public function errorFields($separator = NULL)
    {
        $errors = [];

        //Walker through failed fields
        foreach ($this as $f) {
            if (!$f->isValid()) {
                array_push($errors, $f->label()->toString());
            }
        }

        return is_null($separator) ? $errors : implode($separator, $errors);
    }

    /**
     * Check if all field filled properly
     * 
     * @return bool
     */
    public function isValid(): bool
    {
        return count($this->errorFields()) == 0 || !$this->isFilled();
    }

    /**
     * Check if form is filled
     * 
     * @return bool
     */
    public function isFilled(): bool
    {
        return $this->isFilled;
    }

    /**
     * Check if the bear grabs into the honeypot
     * 
     * @param string HoneypotID
     * 
     * @return bool
     */
    public function checkHoneypot($hpId): bool
    {
        
        if ((get($hpId) === NULL || get($hpId) !== "") && $this->isFilled()) {
            $this->isFilled = false;
            return false;
        };
        return true;

    }

}
