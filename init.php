<?php
    Meerkat\Event\Event::dispatcher()
        ->connect('APP_MODULES_INIT', function (\sfEvent $event, $parameters = null) {

            \Meerkat\Twig\Twig::set_global('static_url', \Kohana::$config->load('meerkat/staticfiles.host')
                . \Kohana::$config->load('meerkat/staticfiles.static_url')
                . \Kohana::$config->load('meerkat/staticfiles.version') . '/');

            \Route::set('static_files', Kohana::$config->load('meerkat/staticfiles.static_route') . '<version>/<file>',
                array('version' => '[0-9]+',
                      'file'    => '.*')
            )
                ->defaults(array('controller' => 'Staticfiles',
                                 'action'     => 'index'));
        });

    Meerkat\Event\Event::dispatcher()
        ->connect('MEERKAT_TWIG_ENVIRONMENT', function (\sfEvent $event, $parameters = null) {
            //**********************************************************************************************************
            //  static_url
            //**********************************************************************************************************
            \Meerkat\Twig\Twig::environment()
                ->addFunction('static_url', new \Twig_Function_Function(function ($file) {
                    return \Meerkat\StaticFiles\Helper::static_url($file);
                }));

        });
