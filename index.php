<?php

use Kirby\Cms\App;
use Kirby\Data\Data;
use Kirby\Exception\Exception;
use Kirby\Filesystem\F;

if (option('microman.formblock.from_email')) {
    throw new Exception('Deprecation error: Option prefix microman.formblock changed to plain.formblock in config.php');
};

F::loadClasses([
    'Plain\Formblock\Blueprint' => __DIR__ . '/classes/Blueprint.php',
    'Plain\Formblock\CaptchaValidator' => __DIR__ . '/classes/CaptchaValidator.php',
    'Plain\Formblock\Field' => __DIR__ . '/classes/Field.php',
    'Plain\Formblock\Fields' => __DIR__ . '/classes/Fields.php',
    'Plain\Formblock\Form' => __DIR__ . '/classes/Form.php',
    'Plain\Formblock\Request' => __DIR__ . '/classes/Request.php',
]);

App::plugin('plain/formblock', [
    'api' => require __DIR__ . '/config/api.php',
    'blockModels' => require __DIR__ . '/config/blockModels.php',
    'blueprints' => [
        ...require __DIR__ . '/config/blueprints.php',
        'blocks/customfields' => __DIR__ . '/blueprints/blocks/customfields.yml',
        'blocks/formfields/01_input' => __DIR__ . '/blueprints/blocks/formfields/01_input.yml',
        'blocks/formfields/02_textarea' => __DIR__ . '/blueprints/blocks/formfields/02_textarea.yml',
        'blocks/formfields/03_checkbox' => __DIR__ . '/blueprints/blocks/formfields/03_checkbox.yml',
        'blocks/formfields/04_radio' => __DIR__ . '/blueprints/blocks/formfields/04_radio.yml',
        'blocks/formfields/05_select' => __DIR__ . '/blueprints/blocks/formfields/05_select.yml',
        'blocks/formfields/06_file' => __DIR__ . '/blueprints/blocks/formfields/06_file.yml',
        'blocks/formfields/07_captcha' => __DIR__ . '/blueprints/blocks/formfields/07_captcha.yml',
        'files/formfile' => __DIR__ . '/blueprints/files/formfile.yml',
        'pages/formcontainer' => __DIR__ . '/blueprints/pages/formcontainer.yml',
        'pages/formrequest' => __DIR__ . '/blueprints/pages/formrequest.yml',
        'snippets/form_confirm' => __DIR__ . '/blueprints/snippets/form_confirm.yml',
        'snippets/form_notify' => __DIR__ . '/blueprints/snippets/form_notify.yml',
        'snippets/form_options' => __DIR__ . '/blueprints/snippets/form_options.yml',
    ],
    'fields' => [
        ...require __DIR__ . '/config/fields.php',
    ],
    'options' => require __DIR__ . '/config/options.php',
    'routes' => require __DIR__ . '/config/routes.php',
    'snippets' => [
        ...require __DIR__ . '/config/snippets.php',
    ],
    'translations' => [
        'de' => Data::read(__DIR__ . '/i18n/de.json'),
        'en' => Data::read(__DIR__ . '/i18n/en.json'),
        'es_ES' => Data::read(__DIR__ . '/i18n/es_ES.json'),
        'fr' => Data::read(__DIR__ . '/i18n/fr.json'),
        'hu' => Data::read(__DIR__ . '/i18n/hu.json'),
        'lt' => Data::read(__DIR__ . '/i18n/lt.json'),
        'nl' => Data::read(__DIR__ . '/i18n/nl.json'),
        'sr@latin' => Data::read(__DIR__ . '/i18n/sr@latin.json'),
    ],
]);
