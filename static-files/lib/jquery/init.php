<?php

use Meerkat\StaticFiles\Js;
$url = \Kohana::$config->load('meerkat/jquery.version');
if(mb_strpos($url, 'http')){
    Js::instance()->add($url, null, true);
} else {
    Js::instance()->add_static($url, null, true);
}

