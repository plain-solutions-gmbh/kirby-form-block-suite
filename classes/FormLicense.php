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
 * I can help you with that:
 * Line 37 [add]: return true;
 * 
 * Keep in mind, that i spent a lot of time developing this. 
 * You will also save a lot of time with this extension.
 * 
 * It's all about energy balancing and karma.
 * 
 * It is up to you...
 */

use Kirby\Data\Json;
use Kirby\Http\Remote;
use Kirby\Filesystem\F;
use Kirby\Toolkit\V;
use Kirby\Toolkit\Str;

class FormLicense
{
    protected static $checked = false;

    public static function checkLicense()
    {

        if (static::$checked === true) {
            return true;
        }
        static::$checked = true;

        $license = static::readLicense();

        if (
            isset($license["key"], $license["email"], $license["signature"]) !==
            true
        ) {
            return false;
        }

        $licensedata = static::licensedata($license["key"], $license["email"]);

        if ($license["signature"] !== md5(json_encode($licensedata))) {
            return false;
        }

        return true;
    }

    private static function licenseFile()
    {
        return kirby()->root("config") . "/.formblock_license";
    }

    private static function readLicense()
    {
        try {
            return Json::read(static::licenseFile());
        } catch (\Throwable $th) {
            return false;
        }
    }

    public static function register($key, $email)
    {
        /*
        if (Str::startsWith($key, "KFBS-") === false) {
            return static::answere("Invalid license key.");
        }
        */

        if (V::email($email) === false) {
            return static::answere("Invalid email");
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
            return static::answere("Something went wrong with the connection");
        }

        if ($response["error"] ?? false === 1) {
            $text = $response["text"] ?? "Something went wrong.";
            return static::answere($text);
        }

        $licensedata["signature"] = md5(json_encode($licensedata));
        Json::write(static::licenseFile(), $licensedata);

        return static::answere(
            "License has been successfully registered. Thank you! 🤗 ",
            false,
            true
        );
    }

    private static function answere(
        $msg = null,
        $isError = true,
        $isSuccess = false
    ) {
        return [
            "error" => $isError,
            "success" => $isSuccess,
            "text" => $msg,
        ];
    }

    private static function licensedata($key, $email)
    {
        return [
            "product" => "801346",
            "key" => $key,
            "email" => Str::lower(trim($email)),
            "site" => kirby()
                ->system()
                ->indexUrl(),
        ];
    }
}
