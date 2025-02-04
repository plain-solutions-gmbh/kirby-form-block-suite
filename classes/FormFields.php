<?php

namespace microman;

/**
 * @package   Kirby Form Block Suite
 * @author    Roman Gsponer <kirby@microman.ch>
 * @link      https://microman.ch/
 * @copyright Roman Gsponer
 * @license   https://license.microman.ch/license/ 
 */

use Kirby\Cms\Blocks;
use Kirby\Http\Environment;

class FormFields extends Blocks
{

    /**
     * Visitor send some values
     *
     * @var Bool
     */
    protected $isFilled;


    /**
     * Set all attachment for email
     *
     * @var array
     */
    public $attachments = [];

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
        $this->isFilled = Environment::getGlobally('REQUEST_METHOD') === 'POST' && get('hash');

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

        return null;
    }

    /**
     * Download Files in the Form
     * 
     * @return mixed
     */
    public function hasAttachment()
    {

        //Walker through fields looking for file
        foreach ($this as $f) {
            if ($f->type(true) == "file") {
                return true;
            }
        }

        return false;

    }

    /**
     * Returns a list of fields
     * 
     * @param string $attr What value to return
     * @return string|array
     */
    public function errorFields($attr = null)
    {
        $errors = [];

        //Walker through failed fields
        foreach ($this as $f) {
            if (!$f->isValid()) {
                array_push($errors, ($attr) ? $f->$attr()->toString() : $f);
            }
        }

        return $errors;
    }

    /**
     * Check if all field filled properly
     * 
     * @return bool
     */
    public function isValid(): bool
    {
        return count($this->errorFields()) == 0 and $this->isFilled();
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
        
        if ((get($hpId) === null || get($hpId) !== "") && $this->isFilled()) {
            $this->isFilled = false;
            return false;
        };
        return true;

    }

}
