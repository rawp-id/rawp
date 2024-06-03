<?php
namespace Core\Database;

use Core\Database;

class DB
{
    protected static $table;
    protected $select = '*';
    protected $where = '';
    protected $orderBy = '';
    protected $limit = '';
    protected $join = '';
    protected $values = [];
    protected $set = '';

    public static function table($table)
    {
        self::$table = $table;
        return new self();
    }

    public function select(...$columns)
    {
        if (!empty($columns)) {
            $this->select = implode(', ', $columns);
        } else {
            $this->select = '*';
        }
        return $this;
    }

    public function where($column, $value, $operator = '=')
    {
        if (empty($this->where)) {
            $this->where = " WHERE $column $operator ?";
        } else {
            $this->where .= " AND $column $operator ?";
        }
        $this->values[] = $value;
        return $this;
    }
     

    public function orderBy($column, $direction = 'ASC')
    {
        $this->orderBy = " ORDER BY $column $direction";
        return $this;
    }

    public function limit($limit)
    {
        $this->limit = " LIMIT $limit";
        return $this;
    }

    public function join($table, $condition, $type = '')
    {
        $this->join .= " $type JOIN $table ON $condition";
        return $this;
    }

    public function insert($data)
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = rtrim(str_repeat('?, ', count($data)), ', ');
        $values = array_values($data);
        $sql = "INSERT INTO " . self::$table . " ($columns) VALUES ($placeholders)";
        Database::query($sql, $values);
        return Database::getPdo()->lastInsertId();
    }

    public function update($data)
    {
        foreach ($data as $key => $value) {
            $this->set .= "$key = ?, ";
            $this->values[] = $value;
        }
        $this->set = rtrim($this->set, ', ');
        $sql = "UPDATE " . self::$table . " SET $this->set$this->where";
        return Database::query($sql, $this->values)->rowCount();
    }

    public function delete()
    {
        $sql = "DELETE FROM " . self::$table . "$this->where";
        return Database::query($sql, $this->values)->rowCount();
    }

    public function get()
    {
        $sql = "SELECT " . $this->select . " FROM " . self::$table . $this->join . $this->where . $this->orderBy . $this->limit;
        $statement = Database::query($sql, $this->values);
        return $statement->fetchAll(\PDO::FETCH_OBJ);
    }
}
