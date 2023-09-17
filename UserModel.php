<?php

namespace nisett\phpmvc;

use nisett\phpmvc\db\DbModel;

/**
 * @package nisett\phpmvc
 */
abstract class UserModel extends DbModel {
    abstract public function getDisplayName(): string;
}

?>