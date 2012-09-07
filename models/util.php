<?php

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

?>