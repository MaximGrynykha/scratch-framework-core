<?php

namespace Ismaxim\ScratchFrameworkCore\db;

use Ismaxim\ScratchFrameworkCore\Application;
use PDO;

class Database
{
    public PDO $pdo;

    public function __construct(array $config)
    {
        $dsn = $config['dsn'] ?? '';
        $user = $config['user'] ?? '';
        $password = $config['password'] ?? '';

        $this->pdo = new PDO($dsn, $user, $password);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function applyMigrations()
    {
        $this->createMigrationsTable();
        $appliedMigrations = $this->getAppliedMigrations();

        $newMigrations = [];

        $files = scandir(Application::$ROOT_DIR . '/migrations');
        $toApplyMigrations = array_diff($files, $appliedMigrations);

        foreach ($toApplyMigrations as $migration) {
            if ($migration === '.' || $migration === '..') {
                continue;
            }

            require_once Application::$ROOT_DIR . '/migrations/' . $migration;
            $className = 'app\\migrations\\' . pathinfo($migration, PATHINFO_FILENAME);
            $instance = new $className();

            $this->log("Applying migration $migration");
            $instance->up();
            $this->log("Successfully applied migration $migration", "s");

            $newMigrations[] = $migration;
        }

        if (! empty($newMigrations)) {
            $this->saveMigrations($newMigrations);
        } else {
            $this->log("All migrations are applied", 'i');
        }
    }

    public function getAppliedMigrations()
    {
        $statement = $this->pdo->prepare("SELECT migration FROM migrations");
        $statement->execute();

        return $statement->fetchAll(\PDO::FETCH_COLUMN);
    }

    public function saveMigrations(array $migrations)
    {
        $migrations_string = implode(', ', array_map(fn ($migration) => "('$migration')", $migrations));

        $statement = $this->pdo->prepare("INSERT INTO migrations (migration) VALUES $migrations_string");
        $statement->execute();
    }

    public function createMigrationsTable()
    {
        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS migrations (
                id INT AUTO_INCREMENT PRIMARY KEY,
                migration VARCHAR(255),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=INNODB;
        ");
    }

    protected function log(string $message, string $type = 'd')
    {
        switch ($type) {
            case 'd': // default
                echo "\033[97m[".date('Y-m-d H:i:s')."] $message \033[0m\n";
            break;
            case 'e': //error
                echo "\033[31m[".date('Y-m-d H:i:s')."] $message \033[0m\n";
            break;
            case 's': //success
                echo "\033[32m[".date('Y-m-d H:i:s')."] $message \033[0m\n";
            break;
            case 'w': //warning
                echo "\033[33m[".date('Y-m-d H:i:s')."] $message \033[0m\n";
            break;  
            case 'i': //info
                echo "\033[36m[".date('Y-m-d H:i:s')."] $message \033[0m\n";
            break;      
        }
    }

    public function prepare($sql)
    {
        return $this->pdo->prepare($sql);
    }
}
