<?php
/**
 * Database connection class
 */
class Database
{
    private static $instance = null;
    private $connection;
    
    private function __construct()
    {
        try {
            // Skip database connection in demo mode
            if (defined('DEMO_MODE') && DEMO_MODE) {
                $this->connection = null;
                return;
            }
            
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $this->connection = new PDO($dsn, DB_USER, DB_PASS);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // In demo mode or development without DB, create mock connection
            if (APP_ENV === 'development') {
                $this->connection = null;
                error_log("Database connection failed: " . $e->getMessage());
            } else {
                die("Database connection failed: " . $e->getMessage());
            }
        }
    }
    
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getConnection()
    {
        return $this->connection;
    }
    
    /**
     * Execute a query and return results
     */
    public function query($sql, $params = [])
    {
        if (!$this->connection) {
            return $this->mockQuery($sql, $params);
        }
        
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            throw new Exception("Database query failed: " . $e->getMessage());
        }
    }
    
    /**
     * Get single row
     */
    public function fetch($sql, $params = [])
    {
        if (!$this->connection) {
            return $this->mockFetch($sql, $params);
        }
        
        $stmt = $this->query($sql, $params);
        return $stmt->fetch();
    }
    
    /**
     * Get all rows
     */
    public function fetchAll($sql, $params = [])
    {
        if (!$this->connection) {
            return $this->mockFetchAll($sql, $params);
        }
        
        $stmt = $this->query($sql, $params);
        return $stmt->fetchAll();
    }
    
    /**
     * Execute insert/update/delete
     */
    public function execute($sql, $params = [])
    {
        if (!$this->connection) {
            return $this->mockExecute($sql, $params);
        }
        
        $stmt = $this->query($sql, $params);
        return $stmt->rowCount();
    }
    
    /**
     * Get last inserted ID
     */
    public function lastInsertId()
    {
        if (!$this->connection) {
            return 1;
        }
        return $this->connection->lastInsertId();
    }
    
    /**
     * Begin transaction
     */
    public function beginTransaction()
    {
        if (!$this->connection) {
            return true;
        }
        return $this->connection->beginTransaction();
    }
    
    /**
     * Commit transaction
     */
    public function commit()
    {
        if (!$this->connection) {
            return true;
        }
        return $this->connection->commit();
    }
    
    /**
     * Rollback transaction
     */
    public function rollback()
    {
        if (!$this->connection) {
            return true;
        }
        return $this->connection->rollBack();
    }
    
    /**
     * Mock query for demo purposes
     */
    private function mockQuery($sql, $params)
    {
        return new MockPDOStatement();
    }
    
    /**
     * Mock fetch for demo purposes
     */
    private function mockFetch($sql, $params)
    {
        // Return demo admin user for login
        if (strpos($sql, 'email = ?') !== false && isset($params[0]) && $params[0] === 'admin@mechanicalfix.com') {
            return [
                'id' => 1,
                'email' => 'admin@mechanicalfix.com',
                'password' => password_hash('admin123', PASSWORD_DEFAULT), // Hash the password correctly
                'role' => 'admin',
                'first_name' => 'Admin',
                'last_name' => 'System',
                'phone' => '555-0000',
                'is_active' => 1,
                'email_verified' => 1,
                'created_at' => '2024-01-01 00:00:00'
            ];
        }
        
        return false;
    }
    
    /**
     * Mock fetchAll for demo purposes
     */
    private function mockFetchAll($sql, $params)
    {
        return [];
    }
    
    /**
     * Mock execute for demo purposes
     */
    private function mockExecute($sql, $params)
    {
        return 1;
    }
}

/**
 * Mock PDO Statement for demo mode
 */
class MockPDOStatement
{
    public function fetch()
    {
        return false;
    }
    
    public function fetchAll()
    {
        return [];
    }
    
    public function rowCount()
    {
        return 1;
    }
}