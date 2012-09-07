<?php

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

?>