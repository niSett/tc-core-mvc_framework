<?php
namespace nisett\phpmvc;

use nisett\phpmvc\Application;
use nisett\phpmvc\middlewares\BaseMiddleware;

    /**
     * @package nisett\phpmvc
     */

    class Controller {
        public string $layout = 'main';
        public string $action = '';

        /**
         * @var \nisett\phpmvc\middlewares\BaseMiddleware[]
         */
        protected array $middlewares = [];

        public function setLayout($layout) {
            $this->layout = $layout;
        }

        public function render($view, $params = []) {
            return Application::$app->view->renderView($view, $params);
        }

        public function registerMiddleware(BaseMiddleware $middleware) {
            $this->middlewares[] = $middleware;
        }

        /**
         * @return \nisett\phpmvc\middlewares\BaseMiddleware[]
         */
        public function getMiddlewares(): array {
            return $this->middlewares;
        }
    }
?>