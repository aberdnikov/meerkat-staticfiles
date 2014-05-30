<?php

    namespace Meerkat\StaticFiles;

    use Meerkat\StaticFiles\Helper;
    use Meerkat\Twig\Twig;

    class Kohana_File {

        protected $config;
        protected $prepare_replace = array();

        function __construct() {
            $this->config = \Kohana::$config->load('meerkat/staticfiles');
        }

        static function need_lib($lib) {
            $file = \Kohana::find_file('static-files/lib/' . $lib, 'init');
            if ($file) {
                include_once $file;
            } else{
                throw new \HTTP_Exception_500('Библиотека '.$lib.' не найдена');
            }
        }

        function prepare_base($source) {
            return Twig::from_string($source);
        }

        function host() {
            return \Kohana::$config->load('meerkat/staticfiles.host');
        }

        function save($file, $data) {
            /**
             * Блокируем файл при записи
             * http://forum.dklab.ru/viewtopic.php?p=96622#96622
             */
            // Вначале создаем пустой файл, ЕСЛИ ЕГО ЕЩЕ НЕТ.
            // Если же файл существует, это его не разрушит.
            fclose(fopen($file, "a+b"));
            // Блокируем файл.
            $f = fopen($file, "r+b") or die("Не могу открыть файл!");
            flock($f, LOCK_EX); // ждем, пока мы не станем единственными
            // В этой точке мы можем быть уверены, что только эта
            // программа работает с файлом.
            fwrite($f, trim($data));
            fclose($f);
        }

        /**
         * Получение информации о том, где брать содержимое файла
         * @return string
         */
        function get_source($url) {
            return \Request::factory($url)
                   ->execute();
        }

        function require_build($build_name, $source) {
            $build_file = Helper::build_file($build_name);
            if (!file_exists($build_file)) {
                if (!file_exists(dirname($build_file))) {
                    mkdir(dirname($build_file), 0777, true);
                }
                $this->save($build_file, trim($source));
            }
        }

        function prepare_replace_add($search, $replace) {
            $this->prepare_replace[$search] = $replace;
        }

        protected function make_file_name($data, $prefix, $ext) {
            $prefix    = strtolower(preg_replace('/[^A-Za-z0-9_\-\/]/', '-', $prefix));
            $prefix    = $prefix ? ($prefix . '/') : '';
            $file_name = md5(\Kohana::$config->load('meerkat/staticfiles.host') . serialize($data));
            return $prefix . substr($file_name, 0, 1) . '/' . substr($file_name, 1, 1) . '/' . $file_name . '.' . $ext;
        }

    }