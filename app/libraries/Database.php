<?php
    class Database {
        private $host = DB_HOST;
        private $user = DB_USER;
        private $password = DB_PASSWORD;
        private $dbname = DB_NAME;

        private $dbh;   //database handler
        private $stmt;  // statement
        private $error; // for exception handling

        public function __construct() {
            // Set DSN (include port and charset if defined)
            $port = defined('DB_PORT') ? DB_PORT : 3306;
            $charset = defined('DB_CHARSET') ? DB_CHARSET : 'utf8mb4';
            $dsn = 'mysql:host=' . $this->host . ';port=' . $port . ';dbname=' . $this->dbname . ';charset=' . $charset;

            // Set options
            $options = array(
                PDO::ATTR_PERSISTENT => true,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            );

            // Create a new PDO instance
            try {
                $this->dbh = new PDO($dsn, $this->user, $this->password, $options);
            } catch (PDOException $e) {
                $this->error = $e->getMessage();
                // Avoid sending DB errors into HTTP responses (JSON/pages).
                error_log('Database connection error: ' . $this->error);
            }
        }

        public function getError() {
            return $this->error;
        }

        // Other methods for database operations can be added here...
        public function query($sql){
            if (!$this->dbh) {
                if (!$this->error) {
                    $this->error = 'Database connection is not available.';
                }
                $this->stmt = null;
                return false;
            }
            $this->stmt = $this->dbh->prepare($sql);
            return true;
        }

        // Bind values to the prepared statement
        public function bind($param, $value, $type = null) {
            if (!$this->stmt) {
                return false;
            }
            if (is_null($type)) {
                switch (true) {
                    case is_int($value):
                        $type = PDO::PARAM_INT;
                        break;
                    case is_bool($value):
                        $type = PDO::PARAM_BOOL;
                        break;
                    case is_null($value):
                        $type = PDO::PARAM_NULL;
                        break;
                    default:
                        $type = PDO::PARAM_STR;
                }
            }
            $this->stmt->bindValue($param, $value, $type);
            return true;
        }

        // Execute the prepared statement
        public function execute(){
            if (!$this->stmt) {
                return false;
            }
            return $this->stmt->execute();
        }

        // Get multiple records as the result
        public function resultSet(){
            if (!$this->execute()) {
                return [];
            }
            return $this->stmt->fetchAll(PDO::FETCH_OBJ);
        }

        // Get a single record as the result
        public function single(){
            if (!$this->execute()) {
                return false;
            }
            return $this->stmt->fetch(PDO::FETCH_OBJ);
        }

        // Check record existence
        public function rowCount(){
            if (!$this->stmt) {
                return 0;
            }
            return $this->stmt->rowCount();
        }

        // Transaction helpers
        public function beginTransaction() {
            if (!$this->dbh) {
                return false;
            }
            return $this->dbh->beginTransaction();
        }

        public function commit() {
            if (!$this->dbh) {
                return false;
            }
            return $this->dbh->commit();
        }

        public function rollBack() {
            if (!$this->dbh) {
                return false;
            }
            return $this->dbh->rollBack();
        }
                // Return last inserted ID from the PDO instance
        public function lastInsertId(){
            if($this->dbh){
                return $this->dbh->lastInsertId();
            }
            return null;
        }
    }
?>