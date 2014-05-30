<?php

namespace Meerkat\StaticFiles;

use Meerkat\StaticFiles\Css;
use Meerkat\StaticFiles\File;
use Meerkat\StaticFiles\Helper;
use \Arr as Arr;
use \Profiler as Profiler;

class Kohana_Css extends File {

    /**
     * Внешние подключаемые файлы стилей
     * @var array
     */
    public $css_external;

    /**
     * inline CSS
     *
     * @var string
     */
    public $css_inline;

    /**
     * singleton
     * @staticvar Css $css
     * @return Css
     */
    static function instance() {
        static $css;
        if (!isset($css)) {
            $css = new Css();
        }
        return $css;
    }

    /**
     * Add external css file
     *
     * @param string $css_inline
     * @param string $condition - condition including script, example [IE7]
     * <!--[if IE 6]><link rel="stylesheet" href="http://habrahabr.ru/css/1302697277/ie6.css" media="all" /><![endif]-->
     * @return Css
     */
    function add($css_file, $media = null, $condition = null, $no_build = false) {
        //если начинается с / значит надо в урл добавить хост
        if (mb_strpos($css_file, '/') === 0) {
            $css_file = self::host() . $css_file;
        }
        $this->css_external[$css_file] = array(
            'condition' => $condition,
            'media' => $media,
            'no_build' => $no_build,
        );
        return $this;
    }

    /**
     *
     * @param string $css_file
     * @param string $condition
     * @param string $media
     * @return Css
     */
    function add_static($css_file, $media = null, $condition = null) {
        $css_file = Helper::static_url($css_file);
        return $this->add($css_file, $media, $condition);
    }

    /**
     * @param string $css_inline
     * @return Css
     */
    function add_inline($css_inline) {
        $css_inline = str_replace('{staticfiles_url}', \Kohana::$config->load('meerkat/staticfiles.static_url') . \Kohana::$config->load('meerkat/staticfiles.version') . '/', $css_inline);
        $this->css_inline[$css_inline] = $css_inline;
        return $this;
    }

    /**
     * @return Css
     */
    function clear_all() {
        $this->clearCss()->clearCssInline();
    }

    /**
     * @return Css
     */
    function clear_inline() {
        $this->css_inline = array();
        return $this;
        ;
    }

    /**
     * @return Css
     */
    function clear_css() {
        $this->css_external = array();
        return $this;
    }

    /**
     * minify css
     * @param string $v
     * @return string
     */
    protected static function minify($v) {
        $v = trim($v);
        $v = str_replace("\r\n", "\n", $v);
        $search = array("/\/\*[\d\D]*?\*\/|\t+/", "/\s+/", "/\}\s+/");
        $replace = array(null, " ", "}\n");
        $v = preg_replace($search, $replace, $v);
        $search = array("/\\;\s/", "/\s+\{\\s+/", "/\\:\s+\\#/", "/,\s+/i", "/\\:\s+\\\'/i", "/\\:\s+([0-9]+|[A-F]+)/i");
        $replace = array(";", "{", ":#", ",", ":\'", ":$1");
        $v = preg_replace($search, $replace, $v);
        $v = str_replace("\n", null, $v);
        return $v;
    }

    function get_link($css, $media = null, $condition = null) {
        //Debug::stop($css);
        if (mb_substr($css, 0, 4) != 'http') {
            $css = Css::instance()->host() . $css;
        }
        if ($media) {
            $attr = array('media' => $media);
        } else {
            $attr = null;
        }
        return ($condition ? '<!--[' . $condition . ']>' : '')
                . \HTML::style($css, $attr)
                . ($condition ? '<![endif]-->' : '');
    }

    function prepare($source, $need_min) {
        $source = str_replace('{{ static_url }}', \Kohana::$config->load('meerkat/staticfiles.host') .
            \Kohana::$config->load('meerkat/staticfiles.static_url') .
            \Kohana::$config->load('meerkat/staticfiles.version') . '/', $source);
        $source = str_replace(array_keys($this->prepare_replace), array_values($this->prepare_replace), $source);
        if ($need_min) {
            $source = self::minify($source);
        }
        return trim($source);
    }


    protected $_need_build_external = null;
    protected $_need_build_inline = null;
    protected $_need_min_external = null;
    protected $_need_min_inline = null;

    function set_need_build_external($val) {
        $this->_need_build_external = (bool) $val;
    }

    function set_need_build_inline($val) {
        $this->_need_build_inline = (bool) $val;
    }

    function set_need_min_external($val) {
        $this->_need_min_external = (bool) $val;
    }

    function set_need_min_inline($val) {
        $this->_need_min_inline = (bool) $val;
    }

    function get_need_build_external() {
        if (is_null($this->_need_build_external)) {
            $this->_need_build_external = \Kohana::$config->load('meerkat/staticfiles.css.external.build');
        }
        return $this->_need_build_external;
    }

    function get_need_build_inline() {
        if (is_null($this->_need_build_inline)) {
            $this->_need_build_inline = \Kohana::$config->load('meerkat/staticfiles.css.inline.build');
        }
        return $this->_need_build_inline;
    }

    function get_need_min_external() {
        if (is_null($this->_need_min_external)) {
            $this->_need_min_external = \Kohana::$config->load('meerkat/staticfiles.css.external.min');
        }
        return $this->_need_min_external;
    }

    function get_need_min_inline() {
        if (is_null($this->_need_min_inline)) {
            $this->_need_min_inline = \Kohana::$config->load('meerkat/staticfiles.css.inline.min');
        }
        return $this->_need_min_inline;
    }

    /**
     * Внешние стили
     * @return string
     */
    function get_external($as_html = true) {
        if (!$as_html) {
            return $this->css_external;
        }
        $benchmark = Profiler::start(__CLASS__, __FUNCTION__);
        if (!count($this->css_external)) {
            Profiler::stop($benchmark);
            return '';
        }
        $css_code = '';
        /* если не надо собирать файлы в один */
        if (!$this->get_need_build_external()) {
            foreach ($this->css_external as $css => $_css) {
                $css_code .= $this->get_link($css, Arr::get($_css, 'media'), Arr::get($_css, 'condition')) . "\n        ";
            }
        } else {
            $build = array();
            $no_builds = array();
            $css_code = '';
            foreach ($this->css_external as $css => $_css) {
                $condition = Arr::get($_css, 'condition');
                $media = Arr::get($_css, 'media');
                $no_build = Arr::get($_css, 'no_build');
                if ($no_build) {
                    $no_builds[$condition . '|' . $media][] = $css;
                } else {
                    $build[$condition . '|' . $media][] = $css;
                }
            }
            //css-url that do not need to build
            //for example http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.3/themes/base/jquery-ui.css
            //Debug::stop($css);
            foreach ($no_builds as $key => $css) {
                list($condition, $media) = explode('|', $key);
                foreach ($css as $_css) {
                    $css_code .= $this->get_link($_css, $media, $condition) . "\n        ";
                }
            }
            foreach ($build as $key => $css) {
                list($condition, $media) = explode('|', $key);
                $build_name = $this->make_file_name($css, 'css/' . $condition . '/' . $media . '/', 'css');
                $prefix = array('css');
                if ($condition) {
                    $prefix[] = str_replace(' ', '', $condition);
                }
                if ($media) {
                    $prefix[] = $media;
                }
                $build_name = $this->make_file_name($css, implode('/', $prefix), 'css');
                $build_file = Helper::build_file($build_name);
                if (!file_exists($build_file)) {
                    //соберем билд в первый раз
                    $build = array();
                    foreach ($css as $url) {
                        $_css = $this->get_source($url);
                        $_css = $this->prepare($_css, $this->get_need_min_external());
                        $build[] = $_css;
                    }
                    if (!file_exists(dirname($build_file))) {
                        mkdir(dirname($build_file), 0777, true);
                    }

                    $this->save($build_file, implode("\n/*---------------------*/\n", $build));
                }
                $css_code .= $this->get_link(Helper::build_url($build_name), $media, $condition) . "\n        ";
            }
        }
        Profiler::stop($benchmark);
        return $css_code;
    }

    /**
     * Формирование инлайновых стилей
     * @return <type>
     */
    function get_inline($as_html = true) {
        if (!$as_html) {
            return $this->css_inline;
        }
        if (!count($this->css_inline)) {
            return '';
        }
        $css_inline = (implode("\n", $this->css_inline));
        $css_inline = $this->prepare($css_inline, $this->get_need_min_inline());
        if ($this->get_need_build_inline()) {
            $build_name = $this->make_file_name($css_inline, 'css/inline', 'css');
            $build_file = Helper::build_file($build_name);
            if (!file_exists($build_file)) {
                if (!file_exists(dirname($build_file))) {
                    mkdir(dirname($build_file), 0777, true);
                }
                $this->save($build_file, $css_inline);
            }
            return $this->get_link(Helper::build_url($build_name), 'all') . "\n        ";
        } else {
            return '<style type="text/css">
' . $css_inline . '
</style>';
        }
    }

    /**
     * Формирование обоих списков (внешние и инлайн стили)
     * @return string
     */
    function __toString() {
        $_css = array();
        $css = $this->get_external();
        if ($css) {
            $_css[] = str_replace("\n", "    \n", $css);
        }
        $css_inline = trim($this->get_inline(true));
        if ($css_inline) {
            $_css[] = $this->beauty($css_inline);
        }
        return implode("\n", $_css);
    }

    protected function beauty($css) {
        $css = str_replace("\r\n", "\r\n    ", $css);
        $css = "        " . str_replace("\n", "\n    ", $css);
        return $css;
    }


}