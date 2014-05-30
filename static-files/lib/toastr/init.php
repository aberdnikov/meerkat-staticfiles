<?php

    Meerkat\StaticFiles\Js::instance()
        ->add_static('lib/toastr/js/toastr.js');
    $js      = '
            toastr.options = {
              "closeButton": ' . Kohana::$config->load('meerkat/toastr.closeButton') . ',
          "debug": ' . Kohana::$config->load('meerkat/toastr.debug') . ',
          "positionClass": "' . Kohana::$config->load('meerkat/toastr.positionClass') . '",';
    $onclick = Kohana::$config->load('meerkat/toastr.onclick');
    if ($onclick) {
        $js .= '"onclick": ' . $onclick . ',';
    }
    $js .= '
          "showDuration": "' . Kohana::$config->load('meerkat/toastr.showDuration') . '",
          "hideDuration": "' . Kohana::$config->load('meerkat/toastr.hideDuration') . '",
          "timeOut": "' . Kohana::$config->load('meerkat/toastr.timeOut') . '",
          "extendedTimeOut": "' . Kohana::$config->load('meerkat/toastr.extendedTimeOut') . '",
          "showEasing": "' . Kohana::$config->load('meerkat/toastr.showEasing') . '",
          "hideEasing": "' . Kohana::$config->load('meerkat/toastr.hideEasing') . '",
          "showMethod": "' . Kohana::$config->load('meerkat/toastr.showMethod') . '",
          "hideMethod": "' . Kohana::$config->load('meerkat/toastr.hideMethod') . '"
        };';
    Meerkat\StaticFiles\Js::instance()
        ->add_inline($js);

    Meerkat\StaticFiles\Css::instance()
        ->add_static('lib/toastr/css/toastr.css');

