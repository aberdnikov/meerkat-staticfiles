<?php
    //$('selector').blink({maxBlinks: 60, blinkPeriod: 1000, speed: 'slow', onBlink: function(){}, onMaxBlinks: function(){}});
    Meerkat\StaticFiles\Js::instance()
        ->add_static('lib/jquery-blink/js/jquery.blink.js');
