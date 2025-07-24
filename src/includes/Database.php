<?php
/**
 * Clase para manejo de conexión a base de datos
 * Sistema de Análisis de Precios y Programa de Obra
 */

class Database {
    private static $instance = null;
    private $connection;
    
    private function __construct() {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
            ];
            
            $this->connection = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            if (APP_DEBUG) {
                die("Error de conexión a la base de datos: " . $e->getMessage());
            } else {
                die("Error de conexión a la base de datos. Contacte al administrador.");
            }
        }
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->connection;
    }
    
    public function prepare($sql) {
        return $this->connection->prepare($sql);
    }
    
    public function query($sql) {
        return $this->connection->query($sql);
    }
    
    public function lastInsertId() {
        return $this->connection->lastInsertId();
    }
    
    public function beginTransaction() {
        return $this->connection->beginTransaction();
    }
    
    public function commit() {
        return $this->connection->commit();
    }
    
    public function rollback() {
        return $this->connection->rollback();
    }
    
    // Método para ejecutar consultas preparadas de forma segura
    public function execute($sql, $params = []) {
        try {
            $stmt = $this->connection->prepare($sql);
            $result = $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            if (APP_DEBUG) {
                throw new Exception("Error en consulta SQL: " . $e->getMessage() . " - SQL: " . $sql);
            } else {
                throw new Exception("Error en la base de datos.");
            }
        }
    }
    
    // Método para obtener un solo registro
    public function fetchOne($sql, $params = []) {
        $stmt = $this->execute($sql, $params);
        return $stmt->fetch();
    }
    
    // Método para obtener múltiples registros
    public function fetchAll($sql, $params = []) {
        $stmt = $this->execute($sql, $params);
        return $stmt->fetchAll();
    }
    
    // Método para contar registros
    public function count($sql, $params = []) {
        $stmt = $this->execute($sql, $params);
        return $stmt->fetchColumn();
    }
    
    // Método para insertar un registro y obtener el ID
    public function insert($table, $data) {
        $columns = implode(',', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        
        $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";
        $this->execute($sql, $data);
        
        return $this->lastInsertId();
    }
    
    // Método para actualizar registros
    public function update($table, $data, $where, $whereParams = []) {
        $setClause = '';
        foreach (array_keys($data) as $column) {
            $setClause .= "{$column} = :{$column}, ";
        }
        $setClause = rtrim($setClause, ', ');
        
        $sql = "UPDATE {$table} SET {$setClause} WHERE {$where}";
        $params = array_merge($data, $whereParams);
        
        return $this->execute($sql, $params);
    }
    
    // Método para eliminar registros
    public function delete($table, $where, $whereParams = []) {
        $sql = "DELETE FROM {$table} WHERE {$where}";
        return $this->execute($sql, $whereParams);
    }
    
    // Prevenir clonación de la instancia
    private function __clone() {}
    
    // Prevenir deserialización de la instancia
    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }
}