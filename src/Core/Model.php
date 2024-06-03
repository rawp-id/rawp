<?php
namespace Core;

use Core\Database;
use Exception;

class Model
{
    protected $table;
    protected $fillable = [];
    protected $guarded = [];
    protected $primaryKey = 'id';
    protected $incrementing = true;
    protected static $relations = [];

    public function __construct($attributes = [])
    {
        foreach ($attributes as $key => $value) {
            $this->$key = $value;
        }
    }

    public static function query()
    {
        return new static;
    }

    public function getTable()
    {
        return $this->table ?: strtolower((new \ReflectionClass($this))->getShortName()) . 's';
    }

    public static function all()
    {
        $instance = new static;
        $sql = "SELECT * FROM " . $instance->getTable();
        $results = Database::query($sql)->fetchAll(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, static::class);
        return $results;
    }

    public static function find($id)
    {
        $instance = new static;
        $sql = "SELECT * FROM " . $instance->getTable() . " WHERE " . $instance->primaryKey . " = ?";
        $result = Database::query($sql, [$id])->fetchObject(static::class);
        return $result;
    }

    public static function create($data)
    {
        $instance = new static;
        $data = $instance->filterData($data);

        if ($instance->incrementing) {
            unset($data[$instance->primaryKey]);
        }

        $columns = implode(', ', array_keys($data));
        $placeholders = rtrim(str_repeat('?, ', count($data)), ', ');
        $values = array_values($data);
        $sql = "INSERT INTO " . $instance->getTable() . " ($columns) VALUES ($placeholders)";
        Database::query($sql, $values);
        $id = Database::getPdo()->lastInsertId();
        return static::find($id);
    }

    public static function update($id, $data)
    {
        $instance = new static;
        $data = $instance->filterData($data);
        $set = '';
        $values = [];
        foreach ($data as $key => $value) {
            $set .= "$key = ?, ";
            $values[] = $value;
        }
        $values[] = $id;
        $set = rtrim($set, ', ');
        $sql = "UPDATE " . $instance->getTable() . " SET $set WHERE " . $instance->primaryKey . " = ?";
        Database::query($sql, $values);
        return static::find($id);
    }

    public static function delete($id)
    {
        $instance = new static;
        $sql = "DELETE FROM " . $instance->getTable() . " WHERE " . $instance->primaryKey . " = ?";
        return Database::query($sql, [$id])->rowCount();
    }

    public function hasMany($related, $foreignKey = null, $localKey = null)
    {
        $relatedInstance = new $related;
        $foreignKey = $foreignKey ?: strtolower((new \ReflectionClass($this))->getShortName()) . '_id';
        $localKey = $localKey ?: $this->primaryKey;

        return $relatedInstance::where($foreignKey, $this->$localKey);
    }

    public function belongsTo($related, $foreignKey = null, $ownerKey = null)
    {
        $relatedInstance = new $related;
        $foreignKey = $foreignKey ?: strtolower($relatedInstance->getTable()) . '_id';
        $ownerKey = $ownerKey ?: $relatedInstance->primaryKey;

        return $relatedInstance::find($this->$foreignKey);
    }

    public static function where($column, $operator = '=', $value = null)
    {
        if ($value === null) {
            $value = $operator;
            $operator = '=';
        }
        $instance = new static;
        $sql = "SELECT * FROM " . $instance->getTable() . " WHERE $column $operator ?";
        $results = Database::query($sql, [$value])->fetchAll(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, static::class);
        return $results;
    }

    public static function with($relations)
    {
        self::$relations = is_array($relations) ? $relations : func_get_args();
        return new static();
    }

    public function get()
    {
        $sql = "SELECT * FROM " . $this->getTable();
        $results = Database::query($sql)->fetchAll(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, static::class);

        foreach ($this->relations as $relation) {
            $method = $relation;
            if (method_exists($this, $method)) {
                foreach ($results as $result) {
                    $result->$relation = $this->$method();
                }
            }
        }

        return $results;
    }

    protected function filterData($data)
    {
        if (!empty($this->guarded)) {
            $data = array_diff_key($data, array_flip($this->guarded));
        }

        if (!empty($this->fillable)) {
            $data = array_intersect_key($data, array_flip($this->fillable));
        }

        return $data;
    }
}
