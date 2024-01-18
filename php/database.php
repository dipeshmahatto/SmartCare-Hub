<?php

class database
{
    private $host;
    private $user;
    private $password;
    private $db;
    public function __construct()
    {
        $this->host = "localhost";
        $this->user = "root";
        $this->password = "";
        $this->db = "hms";
    }

    public function connect()
    {
        $conn = new mysqli($this->host, $this->user, $this->password);
        $conn->select_db($this->db);
        return $conn;
    }


}
?>