<?php

namespace Plain\Formblock;

use Kirby\Toolkit\Str;
use Kirby\Http\Remote;

/**
 * @package   Kirby Form Block Suite
 * @author    Roman Gsponer <support@plain-solutions.net>
 * @link      https://plain-solutions.net/
 * @copyright Roman Gsponer
 * @license   MIT / 
 */

class CaptchaValidator
{
    /**
     * Captcha configuration
     * @var array
     */
    private $config;

    /**
     * Field instance
     * @var Field
     */
    private $field;

    public function __construct(Field $field)
    {
        $this->field = $field;
        $this->config = option('formblock.captcha.mode', []);
    }

    /**
     * Validate captcha based on mode
     * @return array Errors array (empty if valid)
     */
    public function validate(): string
    {
        $mode = $this->config['mode'] ?? 'math';

        return match ($mode) {
            'math' => $this->validateMath(),
            'hcaptcha' => $this->validateHCaptcha(),
            'recaptcha_v2' => $this->validateRecaptchaV2(),
            'recaptcha_v3' => $this->validateRecaptchaV3(),
            default => [],
        };
    }

    /**
     * Validate math captcha
     * @return string
     */
    private function validateMath(): string
    {
        $captchaId = get('captcha-id');
        
        if (!$captchaId) {
            return 'captcha_fail';
        }

        $calc = array_sum(explode('_', $captchaId));
        $userAnswer = $this->field->value();

        if (strval($calc) !== $userAnswer) {
            return 'captcha_fail';
        }

        return '';
    }

    /**
     * Validate hCaptcha
     * @return string
     */
    private function validateHCaptcha(): string
    {
        $token = get('h-captcha-response');

        if (!$token) {
            return 'captcha_fail';
        }

        $hcaptchaConfig = $this->config['hcaptcha'] ?? [];
        $secret = $hcaptchaConfig['secret'] ?? null;

        if (!$secret) {
            return 'captcha_fatal_secret';
        }

        try {
            $response = Remote::post('https://api.hcaptcha.com/siteverify', [
                'data' => [
                    'secret' => $secret,
                    'response' => $token,
                ]
            ]);

            $data = json_decode($response, true);

            if (!($data['success'] ?? false)) {
                return 'captcha_fail';
            }

            return '';
        } catch (\Exception $e) {
            return 'captcha_fatal_api';
        }
    }

    /**
     * Validate reCAPTCHA v2
     * @return string
     */
    private function validateRecaptchaV2(): string
    {
        $token = get('g-recaptcha-response');

        if (!$token) {
            return 'captcha_fail';
        }

        return $this->verifyRecaptcha($token, null);
    }

    /**
     * Validate reCAPTCHA v3
     * @return string
     */
    private function validateRecaptchaV3(): string
    {
        $token = get('g-recaptcha-response');

        if (!$token) {
            return 'captcha_fail';
        }

        $threshold = $this->config['recaptcha']['v3']['threshold'] ?? 0.5;

        return $this->verifyRecaptcha($token, $threshold);
    }

    /**
     * Verify reCAPTCHA token
     * @param string $token
     * @param float|null $threshold v3 threshold
     * @return string
     */
    private function verifyRecaptcha(string $token, ?float $threshold = null): string
    {
        $recaptchaConfig = $this->config['recaptcha'] ?? [];
        $secret = $recaptchaConfig['secret'] ?? null;

        if (!$secret) {
            return 'captcha_fatal_secret';
        }

        try {
            $response = Remote::post('https://www.google.com/recaptcha/api/siteverify', [
                'data' => [
                    'secret' => $secret,
                    'response' => $token,
                ]
            ]);

            $data = json_decode($response, true);

            if (!($data['success'] ?? false)) {
                return 'captcha_fail';
            }

            if ($threshold !== null && isset($data['score'])) {
                if ($data['score'] < $threshold) {
                    return 'captcha_fatal_score';
                }
            }

            return '';
        } catch (\Exception $e) {
            return 'captcha_fatal_api';
        }
    }
}
