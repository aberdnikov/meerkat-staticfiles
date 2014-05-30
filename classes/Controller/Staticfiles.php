<?php

use Meerkat\StaticFiles\Css;
use Meerkat\StaticFiles\Js;
use Meerkat\StaticFiles\Helper;

/**
 * Суть контроллера: иметь возможность создания компактных модулей, в которых бы
 * можно было хранить и css, и js, и картинки выше DOCUMENT_ROOT, чтобы
 * при развертывании проекта не забывать копировать их куда надо
 * Просто бросаем модуль в modules, прописываем его в bootstrapper
 * Затем текущий контроллер, при первом же запросе
 */
class Controller_Staticfiles extends Controller {

    /**
     * Развертывание статики по мере необходимости
     */
    function action_index() {
        Kohana::$profiling = false;
        $file = Request::current()->param('file');
        $this->auto_render = FALSE;
        $info = pathinfo($file);
        if (($orig = Helper::static_original($file))) {
            $content = file_get_contents($orig);
            switch ($info['extension']) {
                case 'php':
                    if (Kohana::$config->load('meerkat/staticfiles.show_php')) {
                        $content = highlight_string($content, true);
                    } else {
                        $content = 'Disable show php scripts in production environment';
                    }
                    break;
                case 'css':
                    $content = Css::instance()->prepare($content, Css::instance()->get_need_min_external());
                    break;
                case 'js':
                    $content = Js::instance()->prepare($content, (strpos('.min.', $file) !== false));
                    break;
                default:
                    break;
            }
            //производим deploy статического файла, в следующий раз его будет
            //отдавать сразу веб-сервер без запуска PHP, но ТОЛЬКО для режима разработки
            if (Kohana::$config->load('meerkat/staticfiles.deploy')) {
                $deploy = Helper::static_file($file);
                if (!file_exists(dirname($deploy))) {
                    mkdir(dirname($deploy), 0777, true);
                }
                file_put_contents($deploy, $content);
            }
            //}
            //а пока отдадим файл руками
            //$this->response->check_cache(sha1($this->request->uri()) . filemtime($orig));
            $this->response->body(trim($content));
            if ('php' != $info['extension']) {
                $this->response->headers('Content-Type', File::mime_by_ext($info['extension']));
            } else {
                $this->response->headers('Content-Type', File::mime_by_ext('html'));
            }
            $this->response->headers('Content-Length', filesize($orig));
            $this->response->headers('Last-Modified', date('r', filemtime($orig)));
        } else {
            // Return a 404 status
            $this->response->body('404 File not found');
            $this->response->status(404);
        }
    }

}

?>