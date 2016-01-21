<?php

class mongoDbase
{
    private $conn = null;
    private $db = null;

    public function connect($host, $port, $user, $pwd, $dbName)
    {
        $server = $host.':'.$port;

        if ($user && $pwd) {
            $server = $user.':'.$pwd.'@'.$server;
        }

        $this->conn = new MongoClient($server);

        $this->conn->connect($server);
        $this->db = $this->conn->selectDB($dbName);
    }

    public function getCollection($collection)
    {
        $collection = $this->db->selectCollection($collection);

        return $collection;
    
    }
}
