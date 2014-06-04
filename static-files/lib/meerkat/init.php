<?php

    use Meerkat\StaticFiles\Js;
    use Meerkat\StaticFiles\Css;

    Css::instance()
        ->add_static('lib/meerkat/css/meerkat.css')
        ->add_static('lib/meerkat/css/meerkat-modal.css');

    Js::instance()
        ->add_static('lib/meerkat/js/meerkat.js')
        ->add_static('lib/meerkat/js/meerkat-modal.js');

