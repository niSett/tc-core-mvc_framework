<?php 
    //use general namespace
    namespace nisett\phpmvc;

use nisett\phpmvc\models\User;
use nisett\phpmvc\db\Database;
use nisett\phpmvc\db\DbModel;

    /**
     * @package nisett\phpmvc;
     */

    //class that include all logic application
    class Application {
        const EVENT_BEFORE_REQUEST = 'beforeRequest';
        const EVENT_AFTER_REQUEST = 'afterRequest';

        protected array $eventListeners = [];

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
            $this->triggerEvent(self::EVENT_BEFORE_REQUEST);
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
         * @return \nisett\phpmvc\Controller
         */
        public function getController(): \nisett\phpmvc\Controller {
            return $this->controller;
        }
        /**
         * @return \nisett\phpmvc\Controller $controller
         */
        public function setController(\nisett\phpmvc\Controller $controller): void {
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

        public function triggerEvent($eventName) {
            $callbacks = $this->eventListeners[$eventName] ?? [];
            foreach ($callbacks as $callback) {
                call_user_func($callback);
            }
        }

        public function on ($eventName, $callback) {
            $this->eventListeners[$eventName][] = $callback;
        }
    }
?>