<?php 

namespace nisett\phpmvc\db;

use nisett\phpmvc\Model;
use nisett\phpmvc\Application;

abstract class DbModel extends Model {

    abstract static public function tableName(): string;

    abstract public function attributes(): array;
    
    abstract static public function primaryKey(): string;

    public function save() {
        $tableName = $this->tableName();
        $attributes = $this->attributes();
        $params = array_map(fn($attr) => ":$attr", $attributes);

        $statement = self::prepare("INSERT INTO $tableName (".implode(',', $attributes).")
                     VALUES(".implode(',', $params).")");
        
        foreach ($attributes as $attribute) {
            $statement->bindValue(":$attribute", $this->{$attribute});
        }

        $statement->execute();
        return true;
    }

    public static function findOne($where) {
        $tableName = static::tableName();
        $attributes = array_keys($where);
        $sql = implode("AND ", array_map(fn($attr) => "$attr = :$attr", $attributes));

        $statement = self::prepare("SELECT * FROM $tableName WHERE $sql");
        foreach ($where as $key => $item) {
            $statement->bindValue(":$key", $item);
        }
        
        $statement->execute();
        // echo '<pre>';
        // var_dump($statement->fetchObject((static::class)));
        // echo '</pre>';

        return $statement->fetchObject(static::class);
    }

    public static function prepare($sql) {
        return Application::$app->db->pdo->prepare($sql);
    }
}

?>