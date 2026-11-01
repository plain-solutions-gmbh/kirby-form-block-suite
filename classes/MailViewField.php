<?php

namespace Plain\Formblock;

/**
 * @package   Kirby Form Block Suite
 * @author    Roman Gsponer <support@plain-solutions.net>
 * @link      https://github.com/plain-solutions-gmbh/
 * @copyright Roman Gsponer
 * @license   MIT /
 */

use Kirby\Form\Field\ValueField;
use Kirby\Reflection\Attributes\Prop;

class MailViewField extends ValueField
{

    /**
     * Format of the received date
     */
    #[Prop]
    protected string $dateformat = 'DD.MM.YYYY HH:mm';

    /**
     * Ids of the forms to show (all forms if empty)
     */
    #[Prop]
    protected array $forms = [];

    protected mixed $value = null;

}
