<?php

    use Meerkat\StaticFiles\Js;
    use Meerkat\StaticFiles\Css;

    Js::instance()
        ->add_static('lib/bootstrap/js/bootstrap.js', null)
        ->add_static('lib/bootstrap/js/holder.js');
    Css::instance()
        ->add_static('lib/bootstrap/css/bootstrap.css')
        ->add_static('lib/bootstrap/css/bootstrap-theme.css')
        ->add_static('lib/bootstrap/css/bootstrap-sidenav.css')
    ;

