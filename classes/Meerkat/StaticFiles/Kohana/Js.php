<?php

namespace Meerkat\StaticFiles;

use Meerkat\StaticFiles\Js;
use Meerkat\StaticFiles\Css;
use Meerkat\StaticFiles\File;
use Meerkat\StaticFiles\Helper;

class Kohana_Js extends File {
    /* внешние подключаемые скрипты */

    public $js_external = array();
    /* инлайн скрипты */
    public $js_inline = array();
    /* скрипты, которые должны быть выполнены при загрузке странице */
    public $js_onload = array();

    function clear_all() {
        return $this->clearJs()->clearJsInline()->clearJsOnline();
    }

    function clear() {
        $this->js_external = array();
        return $this;
    }

    function clear_inline() {
        $this->js_inline = array();
        return $this;
    }

    function clear_onload() {
        $this->js_onload = array();
        return $this;
    }

    /*
     * Получение singleton
     */

    static function instance() {
        static $js;
        if (!isset($js)) {
            $js = new Js();
        }
        return $js;
    }

    /**
     * Подключение внешнего скрипта, реально лежащего в корне сайта
     * @param string $js
     */
    function add($js, $condition = null, $no_build = false) {
        //если начинается с / значит надо в урл добавить хост
        if (mb_strpos($js, '/') === 0) {
            $js = $this->host() . $js;
        }
        $this->js_external[$js] = array(
            'condition' => $condition,
            'no_build' => $no_build,
        );
        return $this;
    }

    /**
     * Подключение внешнего скрипта, по технологии "static-files"
     * т.е. без учета префикса из конфига
     * @param string $js
     */
    function add_static($js, $condition = null, $no_build = false) {
        $js = Helper::static_url($js);
        return $this->add($js, $condition, $no_build);
    }

    /**
     * Добавление куска инлайн джаваскрипта
     * @param <type> $js
     * @param mixed $id - уникальный флаг куска кода, чтобы можно
     * было добавлять в цикле и не бояться дублей
     */
    function add_inline($js, $id = null) {
        $js = str_replace('{staticfiles_url}', \Kohana::$config->load('meerkat/staticfiles.static_url'), $js);
        if ($id) {
            $this->js_inline[$id] = $js;
        } else {
            $this->js_inline[] = $js;
        }
        return $this;
    }

    /**
     * Добавление кода, который должен выполниться при загрузке страницы
     * @param string $js
     * @param mixed $id - уникальный флаг куска кода, чтобы можно
     * было добавлять в цикле и не бояться дублей
     */
    function add_onload($js, $id = null) {
        $js = str_replace('{staticfiles_url}', \Kohana::$config->load('meerkat/staticfiles.static_url'), $js);
        $this->need_jquery();
        if ($id) {
            $this->js_onload[$id] = $js;
        } else {
            $this->js_onload[] = $js;
        }
        return $this;
    }

    /**
     * Использовать во View для вставки вызова всех скриптов
     * @return string
     */
    function __toString() {
        return trim(
                $this->get_external() . PHP_EOL .
                $this->get_inline() . PHP_EOL .
                $this->get_onload()
        );
    }

    function get_link($js, $condition = null) {
        return ($condition ? '<!--[' . $condition . ']>' : '')
                . '<script type="text/javascript" '
                . "" . 'src="' . $js . '"></script>'
                . ($condition ? '<![endif]-->' : '');

        ;
    }

    /**
     * Только внешние скрипты
     * @return string
     */
    function get_external() {
        if (!count($this->js_external))
            return '';
        //если не надо собирать все в один билд-файл
        if (!$this->get_need_build_external()) {
            $js_code = '';
            foreach ($this->js_external as $js => $_js) {
                $condition = \Arr::get($_js, 'condition');
                //если надо подключать все по отдельности
                $js_code .= $this->get_link($js, $condition) . "\n";
            }
            return $js_code;
        } else {
            $build = array();
            $no_build = array();
            $js_code = '';
            foreach ($this->js_external as $js => $_js) {
                $condition = \Arr::get($_js, 'condition');
                if (\Arr::get($_js, 'no_build')) {
                    $no_build[$condition][] = $js;
                } else {
                    $build[$condition][] = $js;
                }
            }
            foreach ($no_build as $condition => $js) {
                $condition = \Arr::get($_js, 'condition');
                foreach ($js as $url) {
                    $js_code .= $this->get_link($url, $condition) . "<!-- no build -->" . PHP_EOL;
                }
            }
            foreach ($build as $condition => $js) {
                $build_name = $this->make_file_name($this->js_external, 'js/external' . ($condition ? '/' . $condition : ''), 'js');
                $build_file = Helper::build_file($build_name);
                if (!file_exists($build_file)) {
                    //соберем билд в первый раз
                    $build = array();
                    foreach ($js as $url) {
                        $_js = $this->get_source($url);
                        $_js = $this->prepare($_js, $this->get_need_min_external());
                        $build[] = $_js;
                    }
                    //если требуется собирать инлайн скрипты в один внешний файл
                    $this->require_build($build_name, implode("\n", $build));
                }
                $js_code .= $this->get_link(Helper::build_url($build_name), $condition) . PHP_EOL;
            }
            //$build_name = $this->make_file_name($this->js_inline, 'js/onload', 'js');
            return $js_code;
        }
    }

    protected $prepare_replace = array();

    function prepare_replace_add($search, $replace) {
        $this->prepare_replace[$search] = $replace;
    }

    function prepare($source, $need_min) {
        $source = str_replace('{{ static_url }}', \Kohana::$config->load('meerkat/staticfiles.host') .
                \Kohana::$config->load('meerkat/staticfiles.static_url') .
                \Kohana::$config->load('meerkat/staticfiles.version') . '/', $source);
        $source = str_replace(array_keys($this->prepare_replace), array_values($this->prepare_replace), $source);
        if ($need_min) {
            include_once \Kohana::find_file('vendor', 'jsmin');
            $source = \JSMin::minify($source);
        }
        return trim($source);
    }

    /**
     * Только инлайн
     * @return <type>
     */
    function get_inline($as_html = true) {
        if (!$as_html) {
            return $this->js_inline;
        }
        if (!count($this->js_inline))
            return '';
        $js_code = '';
        foreach ($this->js_inline as $js) {
            $js_code .= $this->prepare($js, $this->get_need_min_inline());
        }
        $js_code = trim($this->prepare($js_code, $this->get_need_min_inline()));
        if (!$js_code)
            return '';
        if (!$this->get_need_build_inline()) {
            return '<script type="text/javascript">
' . trim($js_code) . '
</script>';
        }
        //если требуется собирать инлайн скрипты в один внешний файл
        $build_name = $this->make_file_name($this->js_inline, 'js/inline', 'js');
        $this->require_build($build_name, $js);
        return $this->get_link(Helper::build_url($build_name)) . PHP_EOL;
    }

    protected $_need_build_external = null;
    protected $_need_build_inline = null;
    protected $_need_build_onload = null;
    protected $_need_min_external = null;
    protected $_need_min_inline = null;
    protected $_need_min_onload = null;

    function set_need_build_external($val) {
        $this->_need_build_external = (bool) $val;
    }

    function set_need_build_inline($val) {
        $this->_need_build_inline = (bool) $val;
    }

    function set_need_build_onload($val) {
        $this->_need_build_onload = (bool) $val;
    }

    function set_need_min_external($val) {
        $this->_need_min_external = (bool) $val;
    }

    function set_need_min_inline($val) {
        $this->_need_min_inline = (bool) $val;
    }

    function set_need_min_onload($val) {
        $this->_need_min_onload = (bool) $val;
    }

    function get_need_build_external() {
        if (is_null($this->_need_build_external)) {
            $this->_need_build_external = \Kohana::$config->load('meerkat/staticfiles.js.external.build');
        }
        return $this->_need_build_external;
    }

    function get_need_build_inline() {
        if (is_null($this->_need_build_inline)) {
            $this->_need_build_inline = \Kohana::$config->load('meerkat/staticfiles.js.inline.build');
        }
        return $this->_need_build_inline;
    }

    function get_need_build_onload() {
        if (is_null($this->_need_build_onload)) {
            $this->_need_build_onload = \Kohana::$config->load('meerkat/staticfiles.js.onload.build');
        }
        return $this->_need_build_onload;
    }

    function get_need_min_external() {
        if (is_null($this->_need_min_external)) {
            $this->_need_min_external = \Kohana::$config->load('meerkat/staticfiles.js.external.min');
        }
        return $this->_need_min_external;
    }

    function get_need_min_inline() {
        if (is_null($this->_need_min_inline)) {
            $this->_need_min_inline = \Kohana::$config->load('meerkat/staticfiles.js.inline.min');
        }
        return $this->_need_min_inline;
    }

    function get_need_min_onload() {
        if (is_null($this->_need_min_onload)) {
            $this->_need_min_onload = \Kohana::$config->load('meerkat/staticfiles.js.onload.min');
        }
        return $this->_need_min_onload;
    }

    /**
     * Только онлоад
     * @return <type>
     */
    function get_onload($as_html = true) {
        if (!$as_html) {
            return $this->js_onload;
        }
        if (!count($this->js_onload))
            return '';
        $js = '';
        foreach ($this->js_onload as $k => $_js) {
            $js .= trim($_js) . PHP_EOL;
        }
        $js = 'jQuery(document).ready(function(){' . PHP_EOL . $js . '});';
        $js = $this->prepare($js, $this->get_need_min_onload());
        if (!$this->get_need_build_onload()) {
            $ret = '<script>' . PHP_EOL . $js . PHP_EOL . '</script>';
            return $ret;
        }
        //если требуется собирать инлайн скрипты в один внешний файл
        $build_name = $this->make_file_name($this->js_onload, 'js/onload', 'js');
        $this->require_build($build_name, $js);
        return $this->get_link(Helper::build_url($build_name)) . PHP_EOL;
    }

    function need_jquery() {
        File::need_lib('jquery');
    }

    function need_jquery_ui() {
        File::need_lib('jqueryui');
    }

}