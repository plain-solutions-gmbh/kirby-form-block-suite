<?php

namespace microman;

/**
 * @package   Kirby Form Block Suite
 * @author    Roman Gsponer <kirby@microman.ch>
 * @link      https://microman.ch/
 * @copyright Roman Gsponer
 * @license   https://license.microman.ch/license/
 * 
 * If you're reading this, you're probably up to skip the license validation.
 *  
 * Keep in mind, that i spent a lot of time developing this. 
 * You will also save a lot of time with this extension.
 * 
 * It's all about energy balancing and karma.
 * 
 * It is up to you...
 */

use Kirby\Cms\App;
use Kirby\Data\Json;
use Kirby\Panel\Field;
use Kirby\Http\Remote;
use Kirby\Filesystem\F;
use Kirby\Toolkit\V;
use Kirby\Toolkit\Str;
use Kirby\Toolkit\I18n;
use Kirby\Exception\Exception;

class FormLicense
{
    protected static bool $checked = false;
    protected static string $productid = '801346';

    public  static function licenseText(): string
    {
        return static::checkLicense() ? '' : 'form.block.license.info.standalone';
    }

    private static function checkLicense(): bool
    {

        if (static::$checked === true) {
            return true;
        }

        try {
            $license = Json::read(static::licenseFile());
        } catch (\Throwable $th) {
            return false;
        }

        if (
            isset($license["key"], $license["email"], $license["signature"]) !== true
        ) {
            return false;
        }

        $licensedata = static::licensedata($license["key"], $license["email"]);

        if ($license["signature"] !== md5(json_encode($licensedata))) {
            return false;
        }

        return static::$checked = true;
    }

    private static function licenseFile(): string
    {
        return App::instance()->root("config") . "/.formblock_license";
    }

    public static function dialog(): array
    {

        return [
            'load' => function () {
                $system = App::instance()->system();
                $local  = $system->isLocal();
                $text = I18n::template('license.activate.' . ($local ? 'local' : 'domain'), ['host' => $system->indexUrl()]);
                $link = 'https://license.microman.ch/?product=' . FormLicense::productid();
    
                return [
                    'component' => 'k-form-dialog',
                    'props' => [
                        'fields' => [
                            'headline' => [
                                'label' => 'Kirby Form Block Suite',
                                'type'  => 'headline'
                            ],
                            'domain' => [
                                'label' => I18n::translate('license.activate.label'),
                                'type'  => 'info',
                                'theme' => $local ? 'warning' : 'info',
                                'text'  => Str::replace($text, 'Kirby', 'Kirby Form Block Suite')
                            ],
                            'license' => [
                                'label'       => I18n::translate('license.code.label'),
                                'type'        => 'text',
                                'required'    => true,
                                'counter'     => false,
                                'placeholder' => '',
                                'help'        => I18n::translate('license.code.help') . ' ' . '<a href="' . $link . '" target="_blank">' . I18n::translate('license.buy') . ' &rarr;</a>'
                            ],
                            'email' => Field::email(['required' => true])
                        ],
                        'submitButton' => [
                            'icon'  => 'key',
                            'text'  => I18n::translate('activate'),
                            'theme' => 'love',
                        ],
                        'value' => [
                            'license' => null,
                            'email'   => null
                        ]
                    ]
                ];
            },
            'submit' => function () {

			    $kirby = App::instance();

                FormLicense::register (
                    $kirby->request()->get('license'),
                    $kirby->request()->get('email')
                );

                return [
                    'message' => 'license.success'
                ];
                
            }
        ];
    }
    public static function register(string $key, string $email): void
    {
        /* Skip: Older licensecodes have no prefix.
        if (Str::startsWith($key, "KFBS-") === false) {
            static::fail('error.license.format');
        }
        */

        if (V::email($email) === false) {
            static::fail('error.license.email');
        }

        $licensedata = static::licensedata($key, $email);

        try {
            $request = new Remote("https://license.microman.ch/", [
                "method" => "POST",
                "data" => $licensedata,
                "timeout" => 5,
            ]);

            $response = $request->json();

        } catch (\Throwable $e) {
            static::fail("form.block.license.error.connection");
        }

        if ($response["error"] ?? false === 1) {
            $text = $response["text"] ??= 'form.block.license.error.common';
            if (array_key_exists('code', $response)) {
                $text = I18n::template('form.block.license.error.' . $response["code"], $text, $response["placeholder"]);
            }

            if (array_key_exists('support', $response['placeholder'])) {
                $text .= ' ' . I18n::template('form.block.license.error.support', NULL, $response["placeholder"]);
            }
            static::fail($text);
        }

        $licensedata["signature"] = md5(json_encode($licensedata));

        Json::write(static::licenseFile(), $licensedata);

    }

    private static function fail(string $msg): void
    {
        throw new Exception(I18n::translate($msg, $msg));
    }


    private static function licensedata(string $key, string $email): array
    {
        return [
            "product" => static::productid(),
            "key" => $key,
            "email" => Str::lower(trim($email)),
            "site" => App::instance()
                ->system()
                ->indexUrl(),
        ];
    }
    public static function productid(): string
    {
        return static::$productid;
    }
}
