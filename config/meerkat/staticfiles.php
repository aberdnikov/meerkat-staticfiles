<?php
    //хост с которого отдается статика
    $host = 'http://' . Arr::get($_SERVER, 'HTTP_HOST');
    //директория где лежит статика (в чистом виде)
    $static_route = '!/static/';
    //директория где лежит статика (после сборки в один файл)
    $build_route  = '!/build/';
    //не рекомендуется убирать префикс из восклицательного имени и делать папку например "media" или "static" в корне сайта,
    //так как вполне возможно, что когда-нибудь вам понадобится такой раздел сайта site.ru/media,
    //но этот урл использовать будет нельзя по причине занятости
    $is_optimize = (Kohana::$environment == Kohana::PRODUCTION);
    return array(
        'js'           => array(
            //внешние подключаемые скрипты
            'external' => array(
                //сжатие
                'min'   => 0,
                //сборка
                'build' => $is_optimize,
            ),
            //inline-скрипты
            'inline'   => array(
                //сжатие
                'min'   => 0,
                //сборка
                'build' => $is_optimize,
            ),
            //скрипты выполняющиеся после загрузки страницы
            'onload'   => array(
                //сжатие
                'min'   => 0,
                //сборка
                'build' => $is_optimize,
            ),
        ),
        'css'          => array(
            //внешние подключаемые стили
            'external' => array(
                //сжатие
                'min'   => 0,
                //сборка
                'build' => $is_optimize,
            ),
            //inline-стили
            'inline'   => array(
                //сжатие
                'min'   => 0,
                //сборка
                'build' => $is_optimize,
            ),
        ),
        //показывать подсветку синтаксиса скрипта, расположенного в static-files директории
        'show_php'     => 0,
        //выкладывать файл на сервер (в режиме разработке 0 - отдается PHP скриптом, в режиме production 1 - выкладывается в DOCROOT и при следующем запросе отдается уже веб-сервером напрямую)
        'deploy'       => $is_optimize,
        'static_route' => $static_route,
        'static_url'   => '/' . $static_route,
        'static_dir'   => DOCROOT . $static_route,
        'build_url'    => '/' . $build_route,
        'build_dir'    => DOCROOT . $build_route,

        'version'      => 1,
        /*
         * Для использования Coral CDN
         * добавьте в имени текущего домена со статикой суффикс ".nyud.net"
         * например для домена "google.com" установите хост "google.com.nyud.net"
         * Больше информации тут: http://habrahabr.ru/blogs/i_recommend/82739/
         * Пример заполнения:
         * 1) "" - ссылки будут иметь вид: "/pic.jpg"
         * 2) "http://ya.ru" - ссылки будут иметь вид: "http://ya.ru/pic.jpg"
         * 3) "http://ya.ru.nyud.net" - ссылки будут иметь вид: "http://ya.ru.nyud.net/pic.jpg"
         */
        'host'         => $host,
    );
?>