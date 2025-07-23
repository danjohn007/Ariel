<?php
/**
 * Base Model class
 */
class Model
{
    protected $db;
    protected $table;
    
    public function __construct()
    {
        $this->db = Database::getInstance();
    }
    
    /**
     * Find all records
     */
    public function findAll($conditions = '', $params = [])
    {
        $sql = "SELECT * FROM {$this->table}";
        if ($conditions) {
            $sql .= " WHERE $conditions";
        }
        return $this->db->fetchAll($sql, $params);
    }
    
    /**
     * Find record by ID
     */
    public function findById($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = ?";
        return $this->db->fetch($sql, [$id]);
    }
    
    /**
     * Find single record by conditions
     */
    public function findOne($conditions, $params = [])
    {
        $sql = "SELECT * FROM {$this->table} WHERE $conditions LIMIT 1";
        return $this->db->fetch($sql, $params);
    }
    
    /**
     * Create new record
     */
    public function create($data)
    {
        $fields = array_keys($data);
        $placeholders = ':' . implode(', :', $fields);
        $fieldsList = implode(', ', $fields);
        
        $sql = "INSERT INTO {$this->table} ($fieldsList) VALUES ($placeholders)";
        
        $params = [];
        foreach ($data as $key => $value) {
            $params[":$key"] = $value;
        }
        
        $this->db->execute($sql, $params);
        return $this->db->lastInsertId();
    }
    
    /**
     * Update record
     */
    public function update($id, $data)
    {
        $fields = [];
        $params = [];
        
        foreach ($data as $key => $value) {
            $fields[] = "$key = :$key";
            $params[":$key"] = $value;
        }
        
        $params[':id'] = $id;
        $fieldsList = implode(', ', $fields);
        
        $sql = "UPDATE {$this->table} SET $fieldsList WHERE id = :id";
        return $this->db->execute($sql, $params);
    }
    
    /**
     * Delete record
     */
    public function delete($id)
    {
        $sql = "DELETE FROM {$this->table} WHERE id = ?";
        return $this->db->execute($sql, [$id]);
    }
    
    /**
     * Count records
     */
    public function count($conditions = '', $params = [])
    {
        $sql = "SELECT COUNT(*) as count FROM {$this->table}";
        if ($conditions) {
            $sql .= " WHERE $conditions";
        }
        $result = $this->db->fetch($sql, $params);
        return $result['count'];
    }
    
    /**
     * Execute custom query
     */
    public function query($sql, $params = [])
    {
        return $this->db->fetchAll($sql, $params);
    }
    
    /**
     * Validate data
     */
    protected function validate($data, $rules)
    {
        $errors = [];
        
        foreach ($rules as $field => $fieldRules) {
            $value = isset($data[$field]) ? $data[$field] : '';
            
            foreach ($fieldRules as $rule) {
                switch ($rule) {
                    case 'required':
                        if (empty($value)) {
                            $errors[$field][] = ucfirst($field) . ' es requerido';
                        }
                        break;
                    case 'email':
                        if (!empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                            $errors[$field][] = ucfirst($field) . ' debe ser un email válido';
                        }
                        break;
                    case 'numeric':
                        if (!empty($value) && !is_numeric($value)) {
                            $errors[$field][] = ucfirst($field) . ' debe ser numérico';
                        }
                        break;
                }
            }
        }
        
        return $errors;
    }
}