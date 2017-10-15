<?php

use Doctrine\Common\Cache\ApcuCache;
use Polidog\Esa\Client;
use Silex\Application;
use Silex\Provider\AssetServiceProvider;
use Silex\Provider\FormServiceProvider;
use Silex\Provider\LocaleServiceProvider;
use Silex\Provider\SessionServiceProvider;
use Silex\Provider\TranslationServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\ServiceControllerServiceProvider;
use Silex\Provider\HttpFragmentServiceProvider;
use Silex\Provider\ValidatorServiceProvider;
use Ttskch\CategoryChecker;
use Ttskch\Esa;

$app = new Application();
$app->register(new ServiceControllerServiceProvider());
$app->register(new AssetServiceProvider());
$app->register(new TwigServiceProvider());
$app->register(new HttpFragmentServiceProvider());

$app->register(new SessionServiceProvider());
$app->register(new FormServiceProvider());
$app->register(new ValidatorServiceProvider());
$app->register(new LocaleServiceProvider());
$app->register(new TranslationServiceProvider(), [
    'locale_fallbacks' => ['ja'],
]);

$app['twig'] = $app->extend('twig', function ($twig, $app) {
    // add custom globals, filters, tags, ...

    return $twig;
});

$app->extend('translator', function ($translator, $app) {
    /** @var \Symfony\Component\Translation\Translator $translator */
    $translator->addResource('xliff', __DIR__.'/../vendor/symfony/validator/Resources/translations/validators.ja.xlf', 'ja');

    return $translator;
});

$app['service.esa'] = $app->factory(function () use ($app) {
    $client = new Client($app['esa.access_token'], $app['esa.team_name']);
    $cache = new ApcuCache();

    return new Esa($app, $client, $cache);
});

$app['service.category_checker'] = $app->factory(function () use ($app) {
    return new CategoryChecker($app['esa.public_categories'], $app['esa.private_categories']);
});

return $app;
