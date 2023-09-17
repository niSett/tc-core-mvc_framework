<?php 
    //use general namespace
    namespace app\core;

use app\core\exception\ForbiddenException;
use app\core\exception\NotFoundException;

    /**
     * @package app\core
     * 
     * @param app\core\Request $request
     * @param app\core\Response $response
     */

    //class that include all routes
    class Router {
        //var-request
        public Request $request;
        //var-response
        public Response $response;
        //array all routes
        protected array $routes = [];

        public function __construct(Request $request, Response $response)
        {
            $this->request = $request;
            $this->response = $response;
        }

        //get method routes
        public function get($path, $callback) {
            $this->routes['get'][$path] = $callback;
        }
        //post method routes
        public function post($path, $callback) {
            $this->routes['post'][$path] = $callback;
        }
        
        public function resolve() {
            $path = $this->request->getPath();
            $method = $this->request->method();
            $callback = $this->routes[$method][$path] ?? false;
            //var_dump($this->routes[$method][$path]);

            if ($callback === false) {
                throw new NotFoundException();
            }
            if(is_string($callback)) {
                return Application::$app->view->renderView($callback);
            }
            if(is_array($callback)) {
                /** @var \app\core\Controller $controller */
                $controller = new $callback[0]();
                Application::$app->controller = $controller;
                $controller->action = $callback[1];
                $callback[0] = $controller;

                foreach ($controller->getMiddlewares() as $middleware) {
                    $middleware->execute();
                }
            }

            echo call_user_func($callback, $this->request, $this->response);
        }
    }
?>