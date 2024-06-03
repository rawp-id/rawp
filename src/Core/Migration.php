<?php
namespace Core;

class Migration
{
    protected static $tableName;
    protected static $columns = [];
    protected static $pdo;

    public static function init()
    {
        if (self::$pdo === null) {
            self::$pdo = Database::getPdo();
        }
    }

    public static function table($tableName)
    {
        self::init();
        self::$tableName = $tableName;
        self::$columns = [];
    }

    public static function create($tableName, $callback)
    {
        self::table($tableName);
        $callback(new self());
        self::execute();
    }

    public static function integer($columnName, $length = 11)
    {
        return self::addColumn($columnName, 'INT', $length);
    }

    public static function bigInt($columnName, $length = 20)
    {
        return self::addColumn($columnName, 'BIGINT', $length);
    }

    public static function boolean($columnName)
    {
        return self::addColumn($columnName, 'BOOLEAN');
    }

    public static function string($columnName, $length = 255)
    {
        return self::addColumn($columnName, 'VARCHAR', $length);
    }

    public static function text($columnName)
    {
        return self::addColumn($columnName, 'TEXT');
    }

    public static function id($columnName = 'id')
    {
        return self::addColumn($columnName, 'INT', 11)->autoIncrement();
    }

    public static function date($columnName)
    {
        return self::addColumn($columnName, 'DATE');
    }

    public static function datetime($columnName)
    {
        return self::addColumn($columnName, 'DATETIME');
    }

    public static function autoIncrement()
    {
        self::$columns[count(self::$columns) - 1]['autoIncrement'] = true;
        return new self();
    }

    public static function primaryKey()
    {
        self::$columns[count(self::$columns) - 1]['primaryKey'] = true;
        return new self();
    }

    public static function nullable()
    {
        self::$columns[count(self::$columns) - 1]['nullable'] = true;
        return new self();
    }

    public static function uuid($columnName)
    {
        return self::addColumn($columnName, 'UUID');
    }

    public static function unique()
    {
        self::$columns[count(self::$columns) - 1]['unique'] = true;
        return new self();
    }

    public static function foreignKey($columnName)
    {
        self::$columns[count(self::$columns) - 1]['foreign'] = $columnName;
        return new self();
    }

    public static function references($tableName)
    {
        self::$columns[count(self::$columns) - 1]['references'] = $tableName;
        return new self();
    }

    public static function onUpdate($action)
    {
        self::$columns[count(self::$columns) - 1]['onUpdate'] = $action;
        return new self();
    }

    public static function onDelete($action)
    {
        self::$columns[count(self::$columns) - 1]['onDelete'] = $action;
        return new self();
    }

    public static function on($action)
    {
        self::$columns[count(self::$columns) - 1]['on'] = $action;
        return new self();
    }

    public static function timestamps()
    {
        self::addColumn('created_at', 'TIMESTAMP')->nullable();
        self::addColumn('updated_at', 'TIMESTAMP')->nullable();
        return new self();
    }

    public static function addColumn($columnName, $type, $length = null)
    {
        $column = compact('columnName', 'type');
        if ($length !== null) {
            $column['length'] = $length;
        }
        self::$columns[] = $column;
        return new self();
    }

    public static function default($value)
    {
        self::$columns[count(self::$columns) - 1]['default'] = $value;
        return new self();
    }

    public static function foreign($columnName, $referencesTable, $referencesColumn = 'id')
    {
        // Initialize foreign key array if it doesn't exist
        if (!isset(self::$columns[count(self::$columns) - 1]['foreign'])) {
            self::$columns[count(self::$columns) - 1]['foreign'] = [];
        }

        // Add the foreign key to the array
        self::$columns[count(self::$columns) - 1]['foreign'][] = [
            'columnName' => $columnName,
            'referencesTable' => $referencesTable,
            'referencesColumn' => $referencesColumn
        ];

        return new self();
    }

    public static function enum($columnName, array $values)
    {
        $valuesString = "'" . implode("', '", $values) . "'";
        return self::addColumn($columnName, "ENUM({$valuesString})");
    }

    public static function execute()
    {
        $sql = "CREATE TABLE IF NOT EXISTS `" . self::$tableName . "` (";
        foreach (self::$columns as $column) {
            $sql .= "`{$column['columnName']}` {$column['type']}";
            if (isset($column['length'])) {
                $sql .= "({$column['length']})";
            }
            if (isset($column['autoIncrement']) && $column['autoIncrement']) {
                $sql .= " AUTO_INCREMENT";
            }
            if (isset($column['primaryKey']) && $column['primaryKey']) {
                $sql .= " PRIMARY KEY";
            }
            if (isset($column['unique']) && $column['unique']) {
                $sql .= " UNIQUE";
            }
            if (isset($column['nullable']) && $column['nullable']) {
                $sql .= " NULL";
            } else {
                $sql .= " NOT NULL";
            }
            if (isset($column['default'])) {
                $sql .= " DEFAULT '" . $column['default'] . "'";
            }
            if (isset($column['foreign'])) {
                $foreign = $column['foreign'];
                foreach ($foreign as $foreignKey) {
                    $sql .= ", FOREIGN KEY (`{$foreignKey['columnName']}`) REFERENCES `{$foreignKey['referencesTable']}`(`{$foreignKey['referencesColumn']}`)";
                }
            }
            if (isset($column['uuid'])) {
                $sql .= " DEFAULT UUID()";
            }
            if (isset($column['enum'])) {
                $valuesString = "'" . implode("', '", $column['enum']) . "'";
                $sql .= ", ENUM({$valuesString})";
            }
            $sql .= ",";
        }
        $sql = rtrim($sql, ',');
        $sql .= ");";

        // echo ($sql);
        // Execute the SQL query to create the table using the PDO connection
        self::$pdo->exec($sql);

        // Add migration record to the migrations table
        $migrationName = self::$tableName;
        $timestamp = time(); // Current time
        $migrationRecord = [
            'migration' => $migrationName,
            'batch' => $timestamp,
        ];
        self::migrations($migrationRecord);
    }

    public static function migrations($data)
    {
        // Create migrations table if it doesn't exist
        $createTableSql = "CREATE TABLE IF NOT EXISTS migrations (
            id INT AUTO_INCREMENT PRIMARY KEY,
            migration VARCHAR(255),
            batch DATETIME
        )";
        self::$pdo->exec($createTableSql);

        // Convert batch value to datetime format
        $batchDateTime = date('Y-m-d H:i:s', $data['batch']);

        // Check if the migration record already exists
        $migrationName = $data['migration'];
        $existingMigration = self::$pdo->query("SELECT * FROM migrations WHERE migration = '{$migrationName}'")->fetch();

        if ($existingMigration) {
            // If the migration record exists, update the batch value
            $stmt = self::$pdo->prepare("UPDATE migrations SET batch = :batch WHERE migration = :migration");
            $stmt->execute([
                'migration' => $data['migration'],
                'batch' => $batchDateTime,
            ]);
        } else {
            // If the migration record doesn't exist, insert it into the migrations table
            $stmt = self::$pdo->prepare("INSERT INTO migrations (migration, batch) VALUES (:migration, :batch)");
            $stmt->execute([
                'migration' => $data['migration'],
                'batch' => $batchDateTime,
            ]);
        }
    }

    public static function drop($tableName)
    {
        self::init();
        $sql = "DROP TABLE IF EXISTS `{$tableName}`;";
        self::$pdo->exec($sql);
    }

    public static function dropAll()
    {
        self::init();

        $tables = self::$pdo->query("SELECT migration FROM migrations ORDER BY id DESC")->fetchAll(\PDO::FETCH_COLUMN);

        // Create DROP TABLE query for each remaining table except migrations
        foreach ($tables as $table) {
            if ($table !== 'migrations') {
                $dropQuery = "DROP TABLE IF EXISTS `$table`";
                self::$pdo->exec($dropQuery);
            }
        }

        // Delete all records from the migrations table
        self::$pdo->exec("TRUNCATE TABLE migrations");

        echo "All tables except migrations were deleted successfully, and migrations table was reset." . PHP_EOL;
    }

}

// Make sure to call init method at the beginning of the script
// Migration::init();
