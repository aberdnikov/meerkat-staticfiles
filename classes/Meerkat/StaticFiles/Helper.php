<?php

    namespace Meerkat\StaticFiles;

    class Helper {

        static function static_original($file) {
            $info    = pathinfo($file);
            $dirname = \Arr::get($info, 'dirname');
            $dir     = ('.' != $dirname) ? $dirname . '/' : '';
            return \Kohana::find_file('static-files', $dir . \Arr::get($info, 'filename'), \Arr::get($info, 'extension'));
        }

        static function static_file($file) {
            return \Kohana::$config->load('meerkat/staticfiles.static_dir') .
            \Kohana::$config->load('meerkat/staticfiles.version') . '/' . $file;
        }

        static function static_url($file) {
            return \Kohana::$config->load('meerkat/staticfiles.host') .
            \Kohana::$config->load('meerkat/staticfiles.static_url') .
            \Kohana::$config->load('meerkat/staticfiles.version') . '/' . $file;
        }

        static function build_file($file) {
            return \Kohana::$config->load('meerkat/staticfiles.build_dir') .
            \Kohana::$config->load('meerkat/staticfiles.version') . '/' . $file;
        }

        static function build_url($file) {
            return \Kohana::$config->load('meerkat/staticfiles.host') .
            \Kohana::$config->load('meerkat/staticfiles.build_url') .
            \Kohana::$config->load('meerkat/staticfiles.version') . '/' . $file;
        }

    }