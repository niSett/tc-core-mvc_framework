<?php

namespace app\core\exception;

/**
 * @package app\core\exception
 */

class NotFoundException extends \Exception {
    protected $message = 'Page not found';
    protected $code = 404;
}

?>
