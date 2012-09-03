<?php
require_once("settings.php");

$ALL_MODEL_LIST = array("Author", "WorkItem", "WorkText", "WorkGallery");

class Database {
    private static $db_instance;

    private function __construct() {
        $this->con = NULL;
    }

    public static function getInstance() {
        if (!self::$db_instance) {
            self::$db_instance = new Database();
        }
        return self::$db_instance;
    }

    private function connect() {
        global $user_name;
        global $password;
        global $database;
        if ($this->con == NULL) {
            $this->con = new mysqli("localhost", $user_name, $password, $database);
        }
    }

    public function close() {
        if ($this->con != NULL) {
            $this->con->close();
            $this->con = NULL;
        }
    }

    public function query($query, $resultmode = MYSQLI_STORE_RESULT) {
        $this->connect();
        return $this->con->query($query, $resultmode);
    }

    public function prepare($query) {
        $this->connect();
        return $this->con->prepare($query);
    }

    public function getError() {
        return "( " . $this->con->errno . " ) " . $this->con->error;
    }
}

/* Found on http://www.php.net/manual/en/mysqli-stmt.bind-param.php */
/* Edited to match this answer
http://stackoverflow.com/questions/280798/is-there-a-way-to-bind-an-array-to-mysqli-prepare */
class BindParam{
    private $values = array(), $types = '';

    public function add( $type, &$value ){
        $this->values[] = &$value;
        $this->types .= $type;
    }

    public function bind($stmt){
        $params = $this->values;
        array_unshift($params, $this->types);
        call_user_func_array( array( $stmt, 'bind_param'), $params);
        return $stmt;
    }

    public function getInsertValuesMarks() {
        $queryString = "";
        $first = True;
        foreach($this->values as $v) {
            if ($first) {
                $first = False;
                $queryString .= "?";
                continue;
            }
            $queryString .= ", ?";
        }
        return $queryString;
    }
}

abstract class Blob {
    const PACKET_SIZE_IN_BYTES=8192;

    function __construct($param_num, $data) {
        $this->param_num = $param_num;
        $this->data = $data;
    }

    protected function getData() {
        print "File Blobs should use fopen / fread here.";
    }

    protected function isDataLeft() {
        print "Return true if data is left, false otherwise";
    }

    protected function doneData() {
        print "Cleanup";
    }

    public function sendData($stmt) {
        while ($this->isDataLeft()) {
            $stmt->send_long_data($this->param_num, $this->getData());
        }
        $this->doneData();
        return $stmt;
    }
}

class FileBlob extends Blob {
    const PACKET_SIZE_IN_BYTES=8192;

    function __construct($param_num, $fileName) {
        $this->file = NULL;
        parent::__construct($param_num, $fileName);
    }

    protected function openFile() {
        if ($this->file == NULL) {
            $this->file = fopen($this->data, "r");
        }
    }

    protected function getData() {
        $this->openFile();
        return fread($this->file, self::PACKET_SIZE_IN_BYTES);
    }

    protected function isDataLeft() {
        $this->openFile();
        return (!feof($this->file));
    }

    protected function doneData() {
        fclose($this->file);
    }
}

abstract class Model {
    function __construct($table_name) {
        $this->table_name = $table_name;
    }

    public function getSchema() {
        return "Abstract models do not have schema";
    }

    public function getName() {
        return $this->table_name;
    }

    public function create() {
        $con = Database::getInstance();
        if (!$con->query("CREATE TABLE " . $this->table_name . "(" . $this->getSchema() . ")")) {
            die("Failed to create table " . $con->getError());
        }
        $con->close();
        return True;
    }

    public function exists() {
        $con = Database::getInstance();
        if($con->query("DESCRIBE " . $this->table_name)) {
            $con->close();
            return True;
        }
        print $con->getError();
        $con->close();
        return False;
    }

    protected function insertValues($bindParam, $columns, $blobs = NULL) {
        $con = Database::getInstance();
        $status = True;
        $query = "INSERT INTO " . $this->table_name . " (" . $columns .
            ") VALUES (" . $bindParam->getInsertValuesMarks() . ")";
        $stmt = $con->prepare($query);
        $stmt = $bindParam->bind($stmt);

        if ($blobs != NULL) {
            foreach ($blobs as $blob) {
                $stmt = $blob->sendData($stmt);
            }
        }

        if (!$stmt->execute()) {
            print("<br>Inserting values failed " . $con->getError());
            $status = False;
        };
        $stmt->close();
        $con->close();
        return $status;
    }

    public function createRow($array) {
        print "Create a BindParam based on array, and pass to insertValues";
    }
}

class Author extends Model {
    function __construct() {
        parent::__construct("Author");
    }

    public function getSchema() {
        return "id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,\n" .
            "first_name VARCHAR(30),\n" .
            "last_name VARCHAR(30),\n" .
            "email VARCHAR(50),\n" .
            "PRIMARY KEY(id)," .
            "UNIQUE(email)";
    }

    public function createRow($array) {
        $bindParam = new BindParam();
        $bindParam->add('s', $array['first_name']);
        $bindParam->add('s', $array['last_name']);
        $bindParam->add('s', $array['email']);
        return $this->insertValues($bindParam, "first_name, last_name, email");
    }
}

class WorkItem extends Model {
    function __construct() {
        parent::__construct("WorkItem");
    }

    public function getSchema() {
        $my_string = "id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,\n" .
            "name VARCHAR(30),\n" .
            "repository_url VARCHAR(250),\n" .
            "submission_date DATE,\n" .
            "author_id INT(10) UNSIGNED,\n" .
            "PRIMARY KEY(id),\n" .
            "FOREIGN KEY(author_id) REFERENCES Author(id),\n" .
            "UNIQUE(repository_url),\n" .
            "UNIQUE(name)";
        return $my_string;
    }

    public function createRow($array) {
        $bindParam = new BindParam();
        $bindParam->add('s', $array['name']);
        $bindParam->add('s', $array['repository_url']);
        $bindParam->add('s', $array['submission_date']);
        $bindParam->add('i', $array['author_id']);
        return $this->insertValues($bindParam, "name, repository_url, submission_date, author_id");
    }
}

class WorkText extends Model {
    function __construct() {
        parent::__construct("WorkText");
    }

    public function getSchema() {
        $my_string = "work_id INT(10) UNSIGNED,\n" .
            "body TEXT,\n" .
            "PRIMARY KEY(work_id),\n" .
            "FOREIGN KEY(work_id) REFERENCES WorkItem(id)";
        return $my_string;
    }

    public function createRow($array) {
        $bindParam = new BindParam();
        $null = NULL;
        $bindParam->add('i', $array['work_id']);
        $bindParam->add('b', &$null);
        $blobs = array(new FileBlob(1, $array['body']));
        return $this->insertValues($bindParam, "work_id, body", $blobs);
    }

    public function retrieveText($id) {
        /* This function is not fit for production */
        $text = "";
        $db = Database::getInstance();
        if ($result = $db->query("SELECT * FROM WorkText WHERE work_id = " . $id)) {
            $row = $result->fetch_array();
            $text = $row['body'];
        } else {
            print $db->getError();
        }
        $db->close();
        return $text;
    }
}

class WorkGallery extends Model {
    function __construct() {
        parent::__construct("WorkGallery");
    }

    public function getSchema() {
        $my_string = "id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,\n" .
            "work_id INT(10) UNSIGNED,\n" .
            "img BLOB,\n" .
            "PRIMARY KEY(id),\n" .
            "FOREIGN KEY(work_id) REFERENCES WorkItem(id)";
        return $my_string;
    }

    public function createRow($array) {
        $bindParam = new BindParam();
        $null = NULL;
        $bindParam->add('i', $array['work_id']);
        $bindParam->add('b', &$null);
        $blobs = array(new FileBlob(1, $array['img']));
        return $this->insertValues($bindParam, "work_id, img", $blobs);
    }
}

?>