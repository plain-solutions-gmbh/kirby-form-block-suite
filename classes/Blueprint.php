<?php

namespace Plain\Formblock;

/**
 * @package   Kirby Form Block Suite
 * @author    Roman Gsponer <support@plain-solutions.net>
 * @link      https://plain-solutions.net/
 * @copyright Roman Gsponer
 * @license   https://plain-solutions.net/terms/ 
 */

use Kirby\Filesystem\Dir;
use Kirby\Filesystem\F;
use Kirby\Data\Yaml;

class Blueprint
{

    /**
     * Get Blueprint as array
     * 
     * @param Array $path Filename or path of Blueprint
     * 
     * @return array
     */
    public static function getBlueprint(String $path, Bool $merge = false): array
    {

        $plugindata = Yaml::read(__DIR__ . "/../blueprints/$path.yml");
        $userfile = kirby()->root('blueprints') . "/$path.yml";
        if (F::exists($userfile)) {
            return $merge ? array_merge($plugindata, Yaml::read($userfile)) : Yaml::read($userfile);
        }

        return $plugindata;
    }



    /**
     * Get inbox tab
     * 
     * @return array|bool
     */
    public static function getInbox()
    {
        if (!static::isEnabled('inbox')) {
            return false;
        };

        return [
            'label' => 'form.block.inbox',
            'fields' => [
                'formid' => ['type' => 'hidden'],
                'mailview' => [
                    'type' => 'mailview'
                ]
            ]
        ];
    }

    /**
     * Get form tab
     * 
     * @return array
     */
    public static function getForm(): array
    {
        return [
            'label' => 'form.block.fromfields',
            'fields' => static::getFormfields()
        ];
    }

    /**
     * Get option tab
     * 
     * @return array
     */
    public static function getOptions(): array
    {
        return [
            'label' => 'form.block.options',
            'fields' => static::mergeField(
                [
                    'name' => [
                        'type' => 'hidden'
                    ],
                    'info' => static::getInfoText()
                ],
                (static::isEnabled('notify')) ? static::getBlueprint('snippets/form_notify') : [],
                (static::isEnabled('confirm')) ? static::getBlueprint('snippets/form_confirm') : [],
                static::getBlueprint('snippets/form_options')
            )
        ];
    }

    /**
     * Merge field in formfield
     * 
     * @param array $fields Formfields to merge
     * 
     * @return array
     */
    public static function mergeField(array ...$fields): array
    {
        $out = [];
        foreach ($fields as $collection) {
            foreach ($collection as $key => $value) {
                $out[$key] = $value;
            }
        }
        return $out;
    }

    /**
     * Get formfields from user/plugin blueprints
     * 
     * @return array
     */
    private static function getFormfields(): array
    {

        $fieldsets = [];

        $out = [];

        $customfields = static::getBlueprint('blocks/customfields', true);

        $fieldsets = static::mergeFormfields(__DIR__ . '/../blueprints/blocks/formfields', $fieldsets, $customfields);
        
        if (Dir::exists($userlocation = kirby()->root('blueprints') . '/blocks/formfields')) {
            $fieldsets = static::mergeFormfields($userlocation, $fieldsets, $customfields);
        }

        return [
            'formfields'    => [
                'type' => 'blocks',
                'fieldsets' => $fieldsets
            ],
            'display'       => [
                'type' => 'text',
                'label' => 'form.block.fromfields.display',
                'help' => 'form.block.fromfields.display.help'
            ]
        ];
    }

    /**
     * Merge field in formfield
     * 
     * @param string $formblockfolder Path to blueprint
     * @param array $out Previous blueprint
     * @param array $customfields Fields to add on each formfield
     * 
     * @return array
     */
    private static function mergeFormfields(string $formblockfolder, array $out, array $customfields): array
    {
        foreach (Dir::read($formblockfolder, null, true) as $f) {

            //Convert formblock to array
            $this_block = Yaml::read($f);
            $identifier = 'formfields_' . pathinfo($f)['filename'];

            if (count($this_block) == 0) {

                //Users formblock is empty -> delete formblock from plugin 
                unset($out[$identifier]);
            } else {
                //Merge custom- and user-fields and add it to fieldset-array
                $this_block['fields'] = array_merge($customfields, $this_block['fields']);
                $this_block['label'] = "{{ label }}";
                $out[$identifier] = $this_block;
            }
        };
        return $out;
    }

    /**
     * Get info text from placeholderfields 
     * 
     * @return array|bool
     */
    private static function getInfoText()
    {

        if (!static::isEnabled('placeholder_hint')) {
            return false;
        };

        $text = '** With &#123;&#123; &#125;&#125; you can insert incoming values using placeholder.**';
        foreach (static::getPlaceholders() as $key => $value) {
            $text .= "\n**\{\{ $key \}\}**: ".$value['label'];
        }
        return [
            'text' => $text
        ];

    }

    /**
     * Users/plugin placeholders
     * 
     * @return array
     */
    public static function getPlaceholders(): array
    {
        return array_merge([
            'summary' => [
                'label' => "Summary",
                'value' => function ($fields) {

                    $table = "<table>";

                    foreach ($fields as $field) {
                        $table .= "<tr><td valign='top'>" . $field->label() . "</td><td>" . nl2br($field->value()) . "</td></tr>";
                    }

                    $table .= "</table>";
                    return $table;
                }
            ]
        ], kirby()->option('plain.formblock.placeholders') ?? []);
    }

    /**
     * Check if tab/function is enabled in config
     * 
     * @param string $fnc 
     * 
     * @return bool
     */
    private static function isEnabled($fnc): bool
    {
        $option_value = ".formblock.disable_$fnc";
        return empty(kirby()->option("plain.$option_value"));
    }

}