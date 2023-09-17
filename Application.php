<?php 
    //use general namespace
    namespace app\core;

use app\models\User;
use app\core\db\Database;
use app\core\db\DbModel;

    /**
     * @package app\core;
     */

    //class that include all logic application
    class Application {
        //var user class
        public string $userClass;
        //var layout
        public string $layout = 'main';
        //var-Router
        public Router $router;
        //var-Request
        public Request $request;
        //var-Response
        public Response $response;
        //var-db
        public Database $db;
        //var-app
        public static Application $app;
        //var-controller
        public ?Controller $controller = null;
        //var-session
        public Session $session;
        //var-dbmodel
        public ?UserModel $user;
        //var-View
        public View $view;

        //var root directory
        public static string $ROOT_DIR;
        

        public function __construct($rootPath, array $config)
        {
            //inlude in var userClass name of the userClass in config file
            $this->userClass = $config['userClass'];

            //static var root path
            self::$ROOT_DIR = $rootPath;
            //var app
            self::$app = $this;
            //include object request in var-request
            $this->request = new Request();
            //include object response in var-response
            $this->response = new Response();
            //include object session in var-session
            $this->session = new Session();
            //include object router in var-router
            $this->router = new Router($this->request, $this->response);
            //include object view in var-view
            $this->view = new View();

            //include object db in var-db
            $this->db = new Database($config['db']);


            //search user on any page
            $primaryValue = $this->session->get('user');
            if ($primaryValue) {
                $primaryKey = $this->userClass::primaryKey(); 
                
                $this->user = $this->userClass::findOne([$primaryKey => $primaryValue]);
                
            } else {
                $this->user = null;
            }
           
        }

        //function run app
        public function run() {
            try {
                echo $this->router->resolve();
            } catch (\Exception $e) {
                $this->response->setStatusCode($e->getCode());
                echo $this->view->renderView('_error', [
                    'exception' => $e
                ]); 
            }
            
        }

        /**
         * @return \app\core\Controller
         */
        public function getController(): \app\core\Controller {
            return $this->controller;
        }
        /**
         * @return \app\core\Controller $controller
         */
        public function setController(\app\core\Controller $controller): void {
            $this->controller = $controller;
        }

        public function login (UserModel $user) {
            $this->user = $user;
            $primaryKey = $user->primaryKey();
            $primaryValue = $user->{$primaryKey};
            $this->session->set('user', $primaryKey);
            return true;
        }

        public function logout() {
            $this->user = null;
            $this->session->remove('user');
        }

        public static function isGuest () {
            return !self::$app->user;
        }
    }
?>