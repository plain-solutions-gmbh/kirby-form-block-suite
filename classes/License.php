<?php

/* Copyright Microman 2023: DO NOT MODIFY THIS FILE! */

namespace microman;

use Kirby\Filesystem\F;
use Kirby\Http\Remote;

class license {

    protected $product_id = '801346';
    protected $product_page = 'https://license.microman.ch?product=801346';
    protected $license_data = array();
    protected $license_key = "";
    protected $error = "";
    protected $success = array();

    public function __construct($fieldsets)
    {        

        $this->license_key = kirby()->option('microman.formblock.license');
        $this->license_data = $this->licensedata();
        $this->success = [
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

    public function checkLicense() {

        $license = $this->readLicense();

        if ($license === $this->licensedata(true)) {
            return $this->success;
        }

        if (!is_null($this->license_key)) {
            return $this->register();
        }

        if (empty($license)) {
            return $this->startTrial();
        }

        return $this->checkTrial();
    }

    private function licenseFile () {

        $cachedir = kirby()->root('cache') . DS . kirby()->system()->indexUrl() . DS . "microman" . DS . "form-block";
        return $cachedir . DS . "AF235SDF32.cache";

    }
    
    private function readLicense() {

        return F::read($this->licenseFile());

    }

    private function checkTrial() {

        $diff = time() - strtotime(hexdec($this->readLicense()));
        $expire = 21 - round($diff / (60 * 60 * 24));

        if ($expire < 1) {
            return $this->answere("The trial period has expired.", true, 'buy');
        } 

        return $this->answere("Trial period ends in {$expire} days.", true, 'buy', true);

    }

    private function startTrial() {

        $this->setLicense(dechex(date('Ymd')));
        return $this->checkTrial();

    }

    private function register() {


        try {

            $request = new Remote('https://license.microman.ch/', array(
                'method' => 'POST',
                'data' => $this->licensedata(false),
                'timeout' => 2
            ));

            $response = $request->json();

        
        } catch (\Throwable $e) {

            $query = http_build_query($this->licensedata(false));

            return $this->answere("Something went wrong with the connection. Try to <a href='https://license.microman.ch/proxy?{$query}' target='_blank'>register manually</a>.");

        }

        if ($response['error'] ?? false === 1) {
            $text = $response['text'] ?? "Something went wrong.";
            return $this->answere($text, true, 'support');
        }
        
        return $this->setLicense($this->licensedata(true));


    }

    private function setLicense($content) {

        try {

            F::write($this->licenseFile(), $content);
            return $this->answere("License has been successfully registered", false, null, true);

        } catch (\Throwable $th) {
            
            return $this->answere("Could not write licensefile.", true, 'support');

        }

    }

    private function answere($msg = null, $isError = true, $additional = null, $success = false) {

        $support = ' Please contact <a href="https://microman.ch/en/microman" target="_blank">the support</a>';
        $buy = " <a href='https://license.microman.ch?product={$this->product_id}' target='_blank'>Buy now!</a>";

        if (!is_null($additional)) {
            $msg .= $$additional;
        }

        $out = [
            'license' => [
                'label' => 'Registration',
                'type' => 'info',
                'theme' => $isError ? 'negative' : 'positive',
                'text' => $msg
            ]
        ];

        return $success ? $out + $this->success : $out;

    }

    private function licensedata($coded = true) {

        $secret = [
            'product'   => $this->product_id,
            'key'       => $this->license_key,
            'site'      => kirby()->system()->indexUrl(),
        ];
        
        return $coded ? md5(json_encode($secret)) : $secret;

    }
}