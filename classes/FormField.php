<?php

namespace microman;

use Kirby\Cms\Block;
use Kirby\Toolkit\A;
use Kirby\Toolkit\V;
use Kirby\Toolkit\F;
use Kirby\Toolkit\Str;
use Kirby\Toolkit\I18n;
use Kirby\Toolkit\Escape;
use Kirby\Cms\Structure;
use Kirby\Filesystem\Mime;

class FormField extends Block
{

    /**
     * Visitor send some values
     *
     * @var Bool
     */
    protected $isFilled;


    /**
     * Visitor send some values
     *
     * @var Bool
     */
    protected $errors;

    /**
     * Creates a field
     * 
     * @param array $params Fieldsdata
     * @param object $parent
     * 
     * @return null
     */
    public function __construct(array $params, object $parent)
    {

        $this->parent = $parent;
        
        if (array_key_exists('options', $params['content'])) {
            $params['content']['opt'] = $this->setOptions($params);
        } else {
            $params['content']['value'] = $this->setValue($params);
        }
        
        $this->isFilled = $params['isFilled'];
        parent::__construct($params);

        $this->errors = $this->getErrorMessages();

    }

    /**
     * Get request value by parameter or array of all if $slug is empty
     * 
     * @param string $slug
     * 
     * @return array|string
     */
    private function request($slug = NULL)
    {
        if (is_null($slug))
            return get();

        return get(is_string($slug) ? $slug : $slug->toString()) ?: "";
    }

    /**
     * Prepare the options to work with them
     * 
     * @param array $field
     * 
     * @return array 
     */
    private function setOptions(array $field): array
    {
        if (!$field['isFilled']) {
            return $field['content']['options'];
        }

        return array_map(function ($option) use ($field) {

            if ($field['type'] == 'formfields/checkbox') {
                $option['selected'] = in_array($option['slug'], array_keys($this->request()));
            } else {
                $option['selected'] = $this->request($field['content']['slug']) ==  $option['slug'];
            }

            return $option;

        }, $field['content']['options']);

    }

    /**
     * Prepare the value to work with them
     * 
     * @param array $field
     * 
     * @return string
     */
    private function setValue(array $field): string
    {
        if ($field['isFilled']) {
            return $this->request($field['content']['slug']);
        }

        if (isset($field['content']['default'])) {
            return $field['content']['default'];
        }

        return "";
    }

    /**
     * Retruns the value of the field
     *
     * @param bool $raw return value without parsing
     * 
     * @return string
     */
    public function value($raw = false): string
    {
        if ($this->hasOptions()) 
            return A::join($this->selectedOptions($raw ? 'slug' : 'label'), ', ');

        if ($this->type(true) == "file" and !is_null($files = $this->files()))
            return implode(', ', array_map(fn($f) => f::safeName($f), $files));

        return $raw ? $this->content()->value() : Escape::html($this->content()->value());
    }

    /**
     * Get Autofill
     *
     * @param bool return with attribute
     * 
     * @return string|null
     */
    public function autofill($html = false)
    {
        $val = $this->content()->autofill();

        if (!$html) return $val;
        if (!$val->isEmpty()) return ' autocomplete="' . $val . '"';

        return "";
    }

    /**
     * Get Aria Error Atribute
     *
     * 
     * @return string|null
     */
    public function ariaAttr()
    {
        if ($this->isValid()) return "";
        return 'invalid aria-describedby="' . $this->id() . '-error-message"';

    }

    /**
     * Get required
     *
     * @param bool|string return with attribute
     * 
     * @return string|bool
     */
    public function required($html = false)
    {
        if (!$html) {
            return $this->content()->required()->isTrue();
        }

        if ($this->content()->required()->isTrue()) {
            if ($html === 'asterisk') return '*';
            if ($html === 'attr') return ' required';
        }
        return "";
    }

    /**
     * Get Tag
     *
     * @param string
     * 
     * @return string
     */
    public function getTag($kind = "container")
    {
        
        if ($kind == "label") 
            return ($this->hasOptions()) ? 'legend' : 'label';

        return ($this->hasOptions()) ? 'fieldset' : 'div';
        
    }


    /**
     * Convert type
     *
     * @param bool $onlyName
     * 
     * @return string
     */
    public function type($onlyName = false): string
    {
        if ($onlyName) {
            return A::last(Str::split($this->type, '/'));
        }
        return $this->type;
    }

    /*********************/
    /** Options Methods **/
    /*********************/


    /**
     * Check if this this field is an option field
     * 
     * @return bool
     */
    public function hasOptions(): bool
    {
        return !$this->options()->isEmpty();
    }

    /**
     * Returns option fields as structure
     * 
     * @return Kirby\Cms\Structure
     */
    
    public function options()
    {
        return $this->opt()->toStructure();
    }

    /**
     * Get Selected options as Array or by $prop
     * 
     * @param array $prop
     * @return array|NULL
     */
    public function selectedOptions($prop = 'label')
    {
        $out = [];
        foreach ($this->options()->toArray() as $value) {
            if ($value['selected'])
                array_push($out,$value[$prop]);
        }
        return $out;
        return $this->options()->filterBy('selected', true)->pluck($prop, true);
    }

    /************************/
    /** Validation Methods **/
    /************************/

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
     * Get Errormessage from translation
     * 
     * @param string 
     * @return string
     */


    /**
     * Get Messages
     *
     * @param string $key 
     * @param array $replaceArray Additional array for replacing
     * 
     * @return string
     */
    public function message($key, $replaceArray = []): string
    {

        $text = $this->__call($key)
                ->or(option('microman.formblock.translations.' . I18n::locale() . '.' .$key))
                ->or(I18n::translate('form.block.' . $key));

        return Str::template($text, $replaceArray);

    }

    /**
     * Get array of all validators (with errors if occur)
     * 
     * @return array
     */
    private function getErrorMessages(): array
    {
        $rules = [];
        $messages = [];

        if (!$this->isFilled)
            return [];            

        //Validate File
        if ($this->type(true) == "file")
            return $this->validateFile();

        //Validate Requirement
        $validator = $this->validate()->toStructure()->toArray();

        if ($this->required()) 
            array_push($validator, ['validate' => 'different', 'different' => '', 'msg' => $this->message('field_message')]);

        foreach ($validator  as $v) {
            $rule = Str::lower($v['validate']);
            $rules[$rule] = [isset($v[$rule]) ? $v[$rule] : "" ];
            $messages[$rule] = $v['msg'] ?: NULL;
        }

        return V::errors($this->value(), $rules, $messages);

    }

    /**
     * Show Files
     * 
     * @return 
     */

    public function files($key = "name", $item = NULL) {

        if ($this->type(true) != "file") 
            return NULL;
    
        if (!array_key_exists($this->slug()->value(), $_FILES))
            return NULL;
        
        $files = $_FILES[$this->slug()->value()][$key];

        if (!is_array($files))
            $files = [$files];
    
        if ( empty($files[0]))
            return NULL;

        if ( is_null($item) )
            return $files;

        return $files[$item];

    }

    /**
     * Validate Attachment
     * 
     * @return array
     */
    private function validateFile(): array
    {
        $errors = [];

        $maxsize = min(
            Str::toBytes(ini_get('post_max_size')),
            Str::toBytes(ini_get('upload_max_filesize')),
            Str::toBytes(ini_get('memory_limit')),
            Str::toBytes($this->maxsize()."M")
        );

        $filesize = $this->files('size');

        if (is_null($filesize)) {

            if ($this->required()) 
                return ['require' => $this->message('file_required')];

            return [];
            
        } 
        
        $maxnumber = $this->maxnumber()->value();

        //Check number of files
        if (count($filesize) > $maxnumber)
            $errors['maxnumber'] = $this->message('file_maxnumber', ['maxnumber' => $maxnumber]);
            
        foreach ($filesize as $i => $value) {

            //Check file size
            if ( $value > $maxsize )
                $errors["filesize"] = $this->message('file_maxsize', ['maxsize' => ($maxsize / 1024 / 1024 )]);

            //Check MIME Types
            $mime = Mime::fromMimeContentType($this->files('tmp_name', $i));
            $accept = $this->accept()->value();

            if(!Mime::isAccepted($mime, $accept) and $this->accept()->isNotEmpty())
                $errors["filesize"] = $this->message('file_accept', ['accept' => $accept]);
            
        }
            
        return $errors;

    }



    /**
     * Get first failed fields message
     * 
     * @return string
     */
    public function errorMessage(): string
    {
        return A::first($this->errors) ?: "";
    }

    /**
     * Get true if everything filled right
     * 
     * @return bool
     */
    public function isValid(): bool
    {
        return count($this->errors) == 0;
    }

    /**
     * Get true if everything failed
     * 
     * @return bool
     */
    public function isInvalid(): bool
    {
        return !$this->isValid();
    }

    /**
     * Controller for the blockfield snippet
     *
     * @return array
     */
    public function controller(): array
    {
        return [
            'formfield'   => $this,
            'content' => $this->content(),
            'id'      => $this->id(),
            'prev'    => $this->prev(),
            'next'    => $this->next()
        ];
    }
}