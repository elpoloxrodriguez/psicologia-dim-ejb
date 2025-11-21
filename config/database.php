<?php
class Database {
    private $host = '10.110.100.12';
    private $db_name = 'psicologia';
    private $username = 'psicologia';
    private $password = 'psicologia';
    private $conn;
    
    public function connect() {
        $this->conn = null;
        
        try {
            $dsn = "pgsql:host=" . $this->host . ";dbname=" . $this->db_name;
            $this->conn = new PDO($dsn, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            // echo "Conexión exitosa a PostgreSQL";
        } catch(PDOException $e) {
            error_log("Error de conexión a PostgreSQL: " . $e->getMessage());
            throw new Exception("Error de conexión a la base de datos");
        }
        
        return $this->conn;
    }
}

?>