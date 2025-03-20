<?php

namespace Plain\Formblock;

/**
 * @package   Kirby Form Block Suite
 * @author    Roman Gsponer <support@plain-solutions.net>
 * @link      https://plain-solutions.net/
 * @copyright Roman Gsponer
 * @license   https://plain-solutions.net/terms/ 
 */

use Kirby\Cms\Block;
use Kirby\Toolkit\I18n;
use Kirby\Toolkit\A;
use Kirby\Toolkit\Str;
use Kirby\Filesystem\F;
use Kirby\Filesystem\Dir;
use Kirby\Exception\Exception;

class Form extends Block
{

    /**
     * Formfields
     *
     * @var \Plain\Fields
     */
    protected $fields;

    /**
     * Errormessage - if occur
     *
     * @var string
     */
    protected $error;

    /**
     * RequestHandler
     *
     * @var \Kirby\Cms\Page
     */
    protected $request;

    /**
     * Files to transfer
     *
     * @var Array
     */
    protected $attachments;


    /**
     * Contains an unique id
     *
     * @var string
     */
    protected $hash;


    /**
     * Creates new form block
     *
     * @param array $params
     */
    public function __construct(array $params)
    {

        parent::__construct($this->setDefault($params));

        //Hands away from panel!
        if (preg_match('/^\/(api|panel)\//', $_SERVER['REQUEST_URI']) > 0) {
            return false;
        }

        $this->fields = new Fields($this->formfields()->toBlocks()->toArray(), $this->parent(), $this->id());

        //Resolve Honeypot
        if (!$this->fields->checkHoneypot($this->honeypotId())) {
            $this->setError("Trapped into honeypot");
        };

        $this->runProcess();
    }

    /**
     * Get the default Language code
     * 
     * @return string
     */
    static function getLang(): string
    {

        if ($lang = kirby()->language()) 
            return $lang->code();

        return option('plain.formblock.default_language');

    }

    /**
     * Get generated hash
     * 
     * @return string
     */
    public function hash($renew = false): string
    {
        if (!$this->hash or $renew)
            $this->hash = bin2hex(random_bytes(18));

        return get("hash") ?? $this->hash;
    }

    /**
     * Set default values to new form block (Run if block create)
     * 
     * @param string $path
     * 
     * @return array
     */
    private function getDefault(string $path, string $postfix = ""): array
    {

        //ToDo get from Default

        $filename = "formblock_default";

        if ($out = F::read($path . $filename . $postfix)){
            return json_decode($out, true);
        }

        if ($out = F::read($path . $filename . '.json')) {
            return json_decode($out, true);
        }

        return [];
    }

    /**
     * Set default values to new form block (Run if Block create)
     * 
     * @param array $params
     * 
     * @return array
     */
    private function setDefault(array $params): array
    {
        if (!isset($params['id'])) {

            $postfix = "_" . self::getLang() . ".json";

            if(count($defaults = $this->getDefault(site()->kirby()->root('config') . "/", $postfix)) == 0){
                $defaults = $this->getDefault(__DIR__ . "/../lib/defaults/", $postfix);
            };
        
            if (!isset($defaults[0]['content'])) {
                throw new Exception("Getting defaults failed. Check formblock_default_".self::getLang().".json in config folder.");
            }

            $params['content'] =  $defaults[0]['content'];
        }
        
        return $params;
    }

    /**********************/
    /** Formdata Methods **/
    /**********************/

    /**
     * Parse to String if needed
     * 
     * @param mixed $value
     * 
     * @return string
     */
    private function parseString($value): ?string
    {
        if (!(is_null($value))) {
            return (is_string($value)) ? $value : $value->value();
        }

        return null;
    }

    /**
     * Formfield by Name
     *
     * @param string $slug Name of the formfield (returns formfield object)
     * @param string|array $attrs Specific Value (returns array with specific value)
     * 
     * @return array|object
     */
    public function form_field(string $slug, $attrs= null)
    {
        if (is_null($attrs)) {
            return $this->fields()->$slug();
        }

        if ($field = $this->fields->$slug()) {
            if (!is_array($attrs)) {
                return $this->parseString($field->$attrs());
            } else {
                $result = [];
                foreach ($attrs as $attr) {
                    $result[$attr] = $this->parseString($field->$attr());
                }
                return $result;
            }
        }
        return null;
    }

    /**
     * Formfields as Array
     * 
     * @param string|null $attrs Set attribute in array (instead field object)
     *
     * @return array|object
     */
    public function fields($attrs = null)
    {
        if (is_null($attrs)) {
            return $this->fields;
        }
        $fields = [];
        foreach ($this->fields() as $field) {
            $fieldSlug = $field->slug()->toString();
            $fields[$fieldSlug] = $this->form_field($fieldSlug, $attrs);
        }

        return $fields;
    }

    /**
     * Formfields as Array
     * 
     * @param string|null $attrs Set attribute in array (instead field object)
     *
     * @return array|object
     */
    public function attachmentFields()
    {
        $fields = [];

        foreach ($this->fields() as $field) {
            if ($field->type(true) == "file") {
                array_push($fields, $field->slug()->toString());
            }
        }

        return $fields;
    }

    /**
     * Get formdata with custom Placeholder
     * 
     * @param string $attr Defines which atribute (value/label) of the placeholder should returned
     * 
     * @return array
     */
    public function fieldsWithPlaceholder($attr = 'value'): array
    {
        $fields = [];
        foreach (option('plain.formblock.placeholders') as $placeholder => $item) {

            if (!isset($item['value']) || !($item['value'] instanceof \Closure)) {
                throw new Exception("Check plain.formblock.placeholders.$placeholder in config.");
            }
            $item['value'] = $item['value']($this->fields);
            $fields[$placeholder] = $attr ? $item[$attr] : $item;
        }

        return array_merge($this->fields($attr), $fields);
    }

    /**
     * Get honeypot name
     * 
     * @return string
     */
    public function honeypotId(): string
    {
        $out = option('plain.formblock.honeypot_variants');

        foreach ($this->fields() as $field) {
            $out = array_diff($out, [$field->autofill(), $field->slug()]);
        }

        return count($out) > 0 ? array_values($out)[0] : "honeypot";
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
        return $this->fields->isFilled() || array_key_exists('HTTP_PAGE', $_SERVER);
    }

    /**
     * Check if all field filled right
     * 
     * @return bool
     */
    public function isValid(): bool
    {
        return $this->fields->isValid();
    }

    /**
     * Check if error occurs
     * 
     * @return bool
     */
    public function isFatal(): bool
    {
        return !empty($this->error);
    }

    /**
     * Check if request send successfully
     * 
     * @return bool
     */
    public function isSuccess(): bool
    {
        return $this->isFilled() && $this->isValid() && !$this->isFatal();
    }
    
    /**
     * Check if form could shown
     * 
     * @return bool
     */
    public function showForm(): bool
    {
        if(!option('plain.formblock.dynamic_validation'))
            return (!$this->isFilled() || !$this->isValid()) && !$this->isFatal();

        return (!$this->isFilled());
    }


    /*********************/
    /** Message Methods **/
    /*********************/

    /**
     * Get Messages
     *
     * @param string $key 
     * @param array $replaceArray Additional array for replacing
     * 
     * @return string
     */
    public function message($key, $replaceArray = [], $fallback = null): string
    {
        $replaceArray = A::merge($this->fieldsWithPlaceholder('value'), $replaceArray);

        return self::translate($key, $this->__call($key), $replaceArray, $fallback);

    }

    /**
     * Get translatin from options or translation files.
     *
     * @param string $key 
     * @param object $default if set, return it. 
     * @param array $replaceArray Additional array for replacing
     * 
     * @return string
     */

static function translate($key, $default, $replace = [], $fallback = null) {

    $output = Str::template(
        $default
            ->or(option('plain.formblock.translations.' . self::getLang() . '.' . $key))
            ->or(I18n::translate('form.block.message.' . $key, "", self::getLang()))
            ->or(option('plain.formblock.translations.en.' . $key))
            ->or(I18n::translate('form.block.message.' . $key, $fallback ?? "Translation for '". $key. "' not found.", "en"))
        , $replace);

    return nl2br($output);

}

    /**
     * Returns error message
     *
     * @return string
     */
    public function errorMessage(): string
    {

        //Return fatal-error if there is one
        if ($this->isFatal())
            return $this->error;

        //Return invalid-message if form invalid
        if (!$this->isValid())
            return $this->message('invalid_message');

        return $this->message('fatal_message');

    }

    /**
     * Returns success message
     *
     * @return string
     */
    public function successMessage(): string
    {
        if ($this->isSuccess()) {
            return $this->message('success_message');
        }
        return "";

    }

    /**
     * Get email aderss from the form
     *
     * @return string
     */
    private function getEmail($forReplyTo = false) {

        $email_field = option('plain.formblock.email_field', 'email');

        if ($email = $this->form_field($email_field, 'value')) {
            return $email;
        }

        $emails = $this->fields()->filter('inputtype', 'email');
        
        if ($emails->count() === 0 && !$forReplyTo) {
            throw new Exception("You need at least one field that is use for an email.");
        }

        return $emails->first()?->value() ?? "";

    }

    /**
     * parse Email to array
     *
     * @return array
     */
    function parseEmail($input, $fallback = null) {


        if (empty($input)) {
            $input = $fallback ?? option('plain.formblock.from_email');
        }

        if (is_array($input)) {
            return $input;
        }

        $result = [];

        $input = explode(';', $input);

        foreach ($input as $part) {
            
            preg_match('/(?:([^\<]+)<([^>]+)>|([^,\s]+))/', $part, $match);

            // If email address and name are present
            if (!empty($match[1]) && !empty($match[2])) {
                $result[$match[2]] = trim($match[1]);
            }
            // If only email address is present
            elseif (!empty($match[3])) {
                $result[$match[3]] = '';
            }

        }
    
        return $result;
    }


    /******************/
    /** Send Methods **/
    /******************/

    /**
     * Send notification email to operator - returns error message if failed
     *
     * @param string|null $body Mailtext - set custom notification body if not set
     * @param string|null $recipent Recipent - set custom notification email if not set
     */
    public function sendNotification($body = null, $to = null)
    {
        if (option('plain.formblock.disable_notify')) {
            return;
        }

        //Get email
        $to ??= $this->parseEmail(
            $this->message('notify_email', [], "")
        );

        $from = $this->parseEmail(
            $this->message('notify_from', [], "")
        );

        $body ??= $this->message('notify_body');

        try {

            $emailData = [
                'from' => $from,
                'to' => $to,
                'subject' => $this->message('notify_subject'),
                'attachments' => $this->attachments
            ];

            if($template = option('plain.formblock.email_template_notification')) {
                $emailData['template'] = $template;
                $emailData['data'] = compact('body');
            }else {
                if (option('plain.formblock.disable_html') === false) {
                    $emailData["body"]['html'] = $body;
                }

                $emailData['body']['text'] = Str::unhtml($body);
            }

            $reply = $this->message('notify_reply', [], $this->getEmail($forReplyTo = true));
            if (!empty($reply)) {
                $emailData['replyTo'] = $reply;
            }

            $bcc = $this->message('notify_bcc', [], "");
            if (!empty($bcc)) {
                $emailData['bcc'] = $bcc;
            }

            site()->kirby()->email($emailData);

            $this->request->update(['notify-send' => date('Y-m-d H:i:s', time())]);

        } catch (\Throwable $error) {
            $this->setError("Error sending notification: " . $error->getMessage());
        }
  
    }

    /**
     * Send confirmation email to visitor - returns error message if failed
     *
     * @param string|null $body Mailtext - set custom notification body if not set
     * @param string|null $from Reply - set custom reply email if not set
     */
    public function sendConfirmation($body = null, $from = null)
    {

        if (option('plain.formblock.disable_confirm')) {
            return;
        }


        $from ??= $this->parseEmail(
            $this->message('confirm_email', [], "")
        );


        $to = $this->parseEmail(
            $this->message('confirm_to', [], ""),
            $this->getEmail()
        );

        $body ??= $this->message('confirm_body');

        try {

            $emailData = [
                'from' => $from,
                'to' => $to,
                'subject' => $this->message('confirm_subject'),
            ];

            if($template = option('plain.formblock.email_template_confirmation')) {
                $emailData['template'] = $template;
                $emailData['data'] = compact('body');
            }else {
                if (option('plain.formblock.disable_html') === false) {
                    $emailData["body"]['html'] = $body;
                }

                $emailData['body']['text'] = Str::unhtml($body);
            }

            $reply = $this->message('confirm_reply', [], "");
            if (!empty($reply)) {
                $emailData['replyTo'] = $reply;
            }

            $bcc = $this->message('confirm_bcc', [], "");
            if (!empty($bcc)) {
                $emailData['bcc'] = $bcc;
            }

            site()->kirby()->email($emailData);

            $this->request->update(['confirm-send' => date('Y-m-d H:i:s', time())]);
            
        } catch (\Throwable $error) {

            $this->setError("Error sending confirmation: " . $error->getMessage());

        }
    }

    /**
     * Throw mail error
     *
     * @param string $error Error message
     * @param bool $save Save error message to request
     * 
     * @return string
     */
    public function setError($error = "An error occured", $save = true): string
    {
        if ($save && !is_null($this->request)) {
            $this->request->update(['error' => $error]);
        }
        return $this->error = option('debug') ? $error : $this->message('fatal_message');
    }


    /**
     * Return Form template
     *
     * @param string $template Name of the template
     * @param array $params Name of the template
     * 
     * @return string
     */
    public function template($template, $props = []): string
    {
        if ($template == 'field_error' && $props["field"]->isValid())
            return '<div class="formblock__message--hidden" data-form="fields_error" data-field="' . $props["field"]->slug() . '" id="' . $props["field"]->id() . '-error-message"></div>';

        if ($template == 'form_error' && (($this->isValid() || get('field_validation')) && !$this->isFatal() || !$this->isFilled()))
            return '<div class="formblock__message--hidden" data-form="form_error"></div>';

        if ($template == 'form_success' && !$this->isSuccess())
            return '<div class="formblock__message--hidden" data-form="form_success"></div>';
                
        if(!option('plain.formblock.dynamic_validation')) {
            
            if ($template == "script")
                return "";
        
            if ($template == "validation") {

                if ($this->isSuccess() ) {

                    if ($this->redirect()->isTrue()) {
                        go($this->success_url()->toPage()->url());
                    }

                    $template = 'form_success';

                } elseif ($this->isFatal()) {

                    $template = 'form_error';

                }

            }
        }

        $templatefolder = (in_array($template, ['hidden', 'validation', 'script', 'styles'])) ? 'formcore/' : 'formtemplates/'; 

        return kirby()->snippet("blocks/".$templatefolder.$template, array_merge($props, [
            'form' => $this,
            'fields' => $this->fields()
        ]));

    }


    /**
     * Formblock Snippets
     * 
     * $param string $root 
     * 
     * @return array
     */
    static function snippets($root): array
    {
        $dirs = array_merge(
            Dir::index($root . '/snippets/blocks/formtemplates', false, ["/formtemplates"], "blocks/formtemplates"),
            Dir::index($root . '/snippets/blocks/formfields', false, ["/formtemplates"], 'blocks/formfields'),
            Dir::index($root . '/snippets/blocks/formcore', false, ["/formcore"], 'blocks/formcore'),
            ['blocks/form.php'],
        );

        $out = array();

        foreach ($dirs as $dir) {
            $out[substr($dir, 0, -4)] = $root . '/snippets/' . $dir;
        }
        return $out;
    }

    /***************************/
    /** Let the magic happen! **/
    /***************************/

    /**
     * Save and Send the request
     */
    private function runProcess()
    {

        if (
            ($this->id() === get('id')) &&
            $this->isFilled() && 
            $this->isValid() && 
            is_null(get('field_validation'))
        ) {
            
            $this->request = new Request([
                'page_id'       => $this->parent()->id(),
                'form_id'       => $this->id(),
                'form_name'     => $this->name()->value() ?? $this->id(),
                'allowCreate'   => true
            ]);    

            $request = $this->request->create( [
                'received' => date('Y-m-d H:i:s', time()),
                'formdata' => json_encode($this->fieldsWithPlaceholder()),
                'formfields' => json_encode($this->fieldsWithPlaceholder('label')),
                'read' => "",
                'display' => $this->message('display')
            ], $this->hash());

            //Reqeust not already exists
            if(!is_null($request)) {

                $this->attachments = $this->request->uploadFiles($this->attachmentFields());
                
                // Send notification mail
                if (!option('plain.formblock.disable_notify') && !$this->isFatal() && $this->enable_notify()->isTrue()) {
                    $this->sendNotification();
                }
                
                // Send confirmation mail
                if (!option('plain.formblock.disable_confirmation') && !$this->isFatal() && $this->enable_confirm()->isTrue()) {
                    $this->sendConfirmation();
                }
                
                $this->hash('true');

                kirby()->trigger('formblock.success:after', [
                    'request' => $this->request,
                ]);

            }

        }
    }


    /**
     * Controller for the formblock snippet
     *
     * @return array
     */
    public function controller(): array
    {
        return [
            'form'    => $this,
            'block'   => $this,
            'content' => $this->content(),
            'id'      => $this->id(),
            'prev'    => $this->prev(),
            'next'    => $this->next()
        ];
    }

}
