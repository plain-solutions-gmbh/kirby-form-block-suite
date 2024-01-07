<?php

namespace microman;

/**
 * @package   Kirby Form Block Suite
 * @author    Roman Gsponer <kirby@microman.ch>
 * @link      https://microman.ch/
 * @copyright Roman Gsponer
 * @license   https://license.microman.ch/license/ 
 */

use Kirby\Cms\Block;
use Kirby\Cms\Structure;
use Kirby\Toolkit\A;
use Kirby\Toolkit\V;
use Kirby\Toolkit\F;
use Kirby\Toolkit\Str;
use Kirby\Toolkit\I18n;
use Kirby\Toolkit\Escape;
use Kirby\Http\Request\Files;
use Kirby\Filesystem\Mime;
use microman\Form;

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
     * Fileobject if it's Field
     *
     * @var Array
     */
    protected $files;

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

        if ($this->type(true) == "file") {
            $file_obj = new Files();
            $this->files = $file_obj->get($this->slug());
        }

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
        if ($this->hasOptions()) {
            return $this->isFilled() ? A::join($this->selectedOptions($raw ? 'slug' : 'label'), ', ') : "";
        } 

        
        if (!is_null($this->files)) {
            return implode(', ', array_map(fn($f) => f::safeName($f['name']), $this->files));
        }

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
        return 'aria-labelledby="label-' . $this->id() . '" aria-describedby="' . $this->id() . '-error-message"';
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
     * Get Messages
     *
     * @param string $key 
     * @param array $replaceArray Additional array for replacing
     * 
     * @return string
     */
    public function message($key, $replaceArray = []): string
    {
        
        return Form::translate($key, $this->__call($key), $replaceArray);

    }

    /**
     * Get array of all validators (with errors if occur)
     * 
     * @return array
     */
    public function getErrorMessages(): array
    {
        $rules = [];
        $messages = [];

        if (!$this->isFilled)
            return [];            

        //Validate File
        if (!is_null($this->files) )
            return $this->validateFile();

        //Validate Requirement
        $validator = $this->validate()->toStructure()->toArray();

        if ($this->required()) 
            array_push($validator, ['validate' => 'different', 'different' => '', 'msg' => $this->message('required_fail')]);

        foreach ($validator as $v) {
            $rule = Str::lower($v['validate']);
            $rules[$rule] = [isset($v[$rule]) ? $v[$rule] : "" ];
            $messages[$rule] = $v['msg'] ?? NULL;
        }

        $errors = V::errors($this->value(), $rules, $messages);

        return kirby()->apply('formblock.validation:before', [
            'type'          => $this->type(true),
            'value'         => $this->value(),
            'errors'        => $errors,
            'field'         => $this

        ], 'errors');

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


        //Max Number of files
        if (count($this->files) > $this->maxnumber()->value()) {
            $errors['maxnumber'] = $this->message('file_maxnumber', ['maxnumber' => $this->maxnumber()->value()]);
        }     
            
        foreach ($this->files as $f) {

            //No files
            if ($f['error'] == 4) {
                if ($this->required()){
                    $errors['require'] = $this->message('file_required');
                }
                return [];
            } 
            
            //Check file size
            if ( $f['size'] > $maxsize)
                $errors["filesize"] = $this->message('file_maxsize', ['maxsize' => ($maxsize / 1024 / 1024 )]);

            //Check MIME Types
            $mime = Mime::fromMimeContentType($f['tmp_name']);
            $accept = $this->accept()->value();

            if(!Mime::isAccepted($mime, $accept) and $this->accept()->isNotEmpty())
                $errors["mime"] = $this->message('file_accept', ['accept' => $accept]);

            if ($f['error'] > 0) {
                $errors["fatal"] = $this->message('file_fatal', ['error' => $f['error']]);
            }
            
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