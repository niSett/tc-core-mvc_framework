<?php

namespace nisett\phpmvc\middlewares;

use nisett\phpmvc\Application;
use nisett\phpmvc\exception\ForbiddenException;

/**
 * @package nisett\phpmvc\middlewares
 */

class AuthMiddleware extends BaseMiddleware {
    public array $actions = [];

    /**
     * @param array $actions
     */
    public function __construct(array $actions = [])
    {
        $this->actions = $actions;
    }

    public function execute()
    {
        if (Application::isGuest()) {
            if (empty($this->actions) || in_array(Application::$app->controller->action, $this->actions)) {
                throw new ForbiddenException();
            }
        }
    }
}
?>