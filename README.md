meerkat-static-files
====================

Модуль управления статикой (JS/CSS/images) в MeerkatCMF (on Kohana 3.3)

Располагайте свой файлы JS/CSS/image в директориях 
APPPATH.'static-files'
MODPATH.'<module_name>/static-files'


#CSS
Существуют следующие виды вставки CSS 

###Css::add
#### [base] простая вставка
пример использования
~~~
\Meerkat\StaticFiles\Css::instance()->add('http://getbootstrap.com/dist/css/bootstrap.min.css');
~~~
* результат

~~~
<link type="text/css" href="http://getbootstrap.com/dist/css/bootstrap.min.css" rel="stylesheet" /> 
~~~

#### [media] установка значения аттрибута media
пример использования
~~~
\Meerkat\StaticFiles\Css::instance()->add('http://getbootstrap.com/dist/css/bootstrap.min.css', 'print');
~~~
* результат в случае выключенного параметра Kohana::$config->load('meerkat/staticfiles.css.external.build')

~~~
<link type="text/css" href="http://getbootstrap.com/dist/css/bootstrap.min.css" rel="stylesheet" media="print" /> 
~~~
* результат в случае включенного параметра Kohana::$config->load('meerkat/staticfiles.css.external.build')

~~~
<link type="text/css" href="http://site.com/!/build/1/css/print/d/5/d5254266dd49f6aa6a01a6f405267161.css" 
rel="stylesheet" media="print" />   
~~~

#### [nobuild] отключение билда (когда все подключенные CSS собираются в один файл для клиентской оптимизации)
пример использования
~~~
\Meerkat\StaticFiles\Css::instance()->add('http://getbootstrap.com/dist/css/bootstrap.min.css', null, TRUE);
~~~
* результат в случае выключенного параметра Kohana::$config->load('meerkat/staticfiles.css.external.build')
* результат в случае включенного параметра Kohana::$config->load('meerkat/staticfiles.css.external.build')

~~~
<link type="text/css" href="http://getbootstrap.com/dist/css/bootstrap.min.css" rel="stylesheet" media="print" /> 
~~~

###Css::add_inline
пример использования
~~~
\Meerkat\StaticFiles\Css::instance()->add_inline('
body{
    margin-top:60px;
}');
~~~
результат
~~~
<style type="text/css">
body{
  margin-top:60px;
}
</style>
~~~

###Css::add_static
пример вставки 
\Meerkat\StaticFiles\Css::instance()->add_static('lib/bootstrap-notify/css/bootstrap-notify.css');
будет произведен поиск в директориях с именем "static-files" в APPPAH и всех модулях и сервер отдаст браузеру первый найденный
~~~
<link type="text/css" href="http://site.com/!/static/1/lib/bootstrap-notify/css/bootstrap-notify.css" 
rel="stylesheet" />    
~~~



#JS
Существуют следующие виды вставки JS

###Js::add
#### [base] простая вставка
пример использования
~~~
\Meerkat\StaticFiles\Js::instance()->add('http://platform.twitter.com/widgets.js');
~~~
* результат в случае выключенного параметра Kohana::$config->load('meerkat/staticfiles.js.external.build')

~~~
<script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>
~~~
* результат в случае включенного параметра Kohana::$config->load('meerkat/staticfiles.js.external.build')
~~~
<script type="text/javascript" src="/!/build/1/js/external/1/c/1ca8a65a55783efe7ca212df0a609f2e.js"></script>
~~~

#### [condition] вставка по условию
пример использования
~~~
\Meerkat\StaticFiles\Js::instance()->add('http://platform.twitter.com/widgets.js', 'if IE7');
~~~
* результат в случае выключенного параметра Kohana::$config->load('meerkat/staticfiles.js.external.build')

~~~
<!--[if IE7]><script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script><![endif]-->
~~~
* результат в случае включенного параметра Kohana::$config->load('meerkat/staticfiles.js.external.build')
~~~
<!--[if IE7]><script type="text/javascript" src="/!/build/1/js/external/if-ie7/1/c/1ca8a65a55783efe7ca212df0a609f2e.js"></script><![endif]-->
~~~

#### [nobuild] вставка с отключением автоматической сборки
пример использования
~~~
\Meerkat\StaticFiles\Js::instance()->add('http://platform.twitter.com/widgets.js', null, true);
~~~
* результат в случае выключенного параметра Kohana::$config->load('meerkat/staticfiles.js.external.build')
* результат в случае включенного параметра Kohana::$config->load('meerkat/staticfiles.js.external.build')

~~~
<script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>
~~~


###Js::add_static
#### [base] простая вставка
пример использования
~~~
\Meerkat\StaticFiles\Js::instance()->add_static('script.js');
~~~
* результат в случае выключенного параметра Kohana::$config->load('meerkat/staticfiles.js.external.build')

~~~
<script type="text/javascript" src="http://site.com/!/static/1/widgets.js"></script>
~~~
* результат в случае включенного параметра Kohana::$config->load('meerkat/staticfiles.js.external.build')

~~~
<script type="text/javascript" src="/!/build/1/js/external/1/c/1ca8a65a55783efe7ca212df0a609f2e.js"></script>
~~~


#### [condition] вставка по условию
пример использования
~~~
\Meerkat\StaticFiles\Js::instance()->add_static('script.js', 'if IE7');
~~~
* результат в случае выключенного параметра Kohana::$config->load('meerkat/staticfiles.js.external.build')

~~~
* результат в случае выключенного параметра Kohana::$config->load('meerkat/staticfiles.js.external.build')

~~~
<!--[if IE7]><script type="text/javascript" src="http://site.com/!/static/1/widgets.js"></script><![endif]-->
~~~
* результат в случае включенного параметра Kohana::$config->load('meerkat/staticfiles.js.external.build')

~~~
<!--[if IE7]><script type="text/javascript" src="/!/build/1/js/external/1/c/1ca8a65a55783efe7ca212df0a609f2e.js"></script><![endif]-->
~~~


#### [nobuild] вставка с отключением автоматической сборки
пример использования
~~~
\Meerkat\StaticFiles\Js::instance()->add_static('script.js', null, TRUE);
~~~
* результат в случае выключенного параметра Kohana::$config->load('meerkat/staticfiles.js.external.build')
* результат в случае включенного параметра Kohana::$config->load('meerkat/staticfiles.js.external.build')

~~~
<script type="text/javascript" src="http://site.com/!/static/1/widgets.js"></script>
~~~

#IMAGES/FONTS
Обычно эти статические файлы добавляются в виде ссылок в сам CSS - делается это методом замены подстроки {{ static_url }}
пишем в style.css 
~~~
.delimitter{
	background: url('{{ static_url }}/delimitter.png');
}

@font-face {
  font-family: 'FontAwesome';
  src: url('{{ static_url }}lib/font-awesome/font/fontawesome-webfont.eot?v=3.2.1');
  src: url('{{ static_url }}lib/font-awesome/font/fontawesome-webfont.eot?#iefix&v=3.2.1') 
	format('embedded-opentype'), url('{{ static_url }}lib/font-awesome/font/fontawesome-webfont.woff?v=3.2.1') 
	format('woff'), 
	url('{{ static_url }}lib/font-awesome/font/fontawesome-webfont.ttf?v=3.2.1') format('truetype'), 
	url('{{ static_url }}lib/font-awesome/font/fontawesome-webfont.svg#fontawesomeregular?v=3.2.1') format('svg');
  font-weight: normal;
  font-style: normal;
} ~~~

#PHP
Иногда требуется показать пример использования php-скрипта 
для этого необходимо включить параметр конфига Kohana::$config->load('meerkat/staticfiles.show_php') 
и закинуть в директорию static-files ваш скрипт, например такой:
* example/phpinfo.php

с содержимым
~~~
<?php
phpinfo();
~~~

а потом на него сослаться, тогда при прямом заходе по ссылке или использовании iframe http://site.com/!/static/1/examples/phpinfo.php можно будет увидеть его раскрашенный и отформатированный при помощи функции *highlight_string* код
~~~
<code><span style="color: #000000">
<span style="color: #0000BB">&lt;?php
<br />phpinfo</span><span style="color: #007700">();</span>
</span>
</code>
~~~

#CONFIG
все комментарии включены в исходный код
~~~
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
    return array(
        'js'           => array(
            //внешние подключаемые скрипты
            'external' => array(
                //сжатие
                'min'   => 0,
                //сборка
                'build' => 0,
            ),
            //inline-скрипты
            'inline'   => array(
                //сжатие
                'min'   => 0,
                //сборка
                'build' => 0,
            ),
            //скрипты выполняющиеся после загрузки страницы
            'onload'   => array(
                //сжатие
                'min'   => 0,
                //сборка
                'build' => 0,
            ),
        ),
        'css'          => array(
            //внешние подключаемые стили
            'external' => array(
                //сжатие
                'min'   => 0,
                //сборка
                'build' => 0,
            ),
            //inline-стили
            'inline'   => array(
                //сжатие
                'min'   => 0,
                //сборка
                'build' => 0,
            ),
        ),
        //показывать подсветку синтаксиса скрипта, расположенного в static-files директории
        'show_php'     => 1,
        'deploy'       => 0,
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
~~~

#VERSION
Наверное, самый важный момент - версионирование статики: после того, как вы обновили свои CSS/JS/images вам захочется сбросить кэш браузеров, для этого просто увеличьте версию в конфиге на единичку
* параметр Kohana::$config->load('meerkat/staticfiles.version')

#DEPLOY 
//выкладывать файл на сервер 
* 0: использовать в режиме разработке - отдается PHP скриптом
* 1: использовать в режиме production - выкладывается в DOCROOT и при следующем запросе отдается уже веб-сервером напрямую

* параметр Kohana::$config->load('meerkat/staticfiles.deploy')


В PHP
~~~
\Meerkat\StaticFiles\File::need_lib('jqueryui');
Meerkat\StaticFiles\Js::instance()->add_onload('$( "#datepicker" ).datepicker();');
~~~

В HTML
~~~
<p>Date: <input type="text" id="datepicker"></p>
~~~
