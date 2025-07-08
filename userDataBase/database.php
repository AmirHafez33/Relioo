<?php

class database
{
    public const HOST = "localhost";
    public const USER = "root";
    public const PASSWORD = "";
    public const DBNAME = "reelio";
    public $conn;
    public $tableName;

    public function __construct($tableName)
    {
        $this->tableName = $tableName;
        $this->connect();
    }
    public function connect()
    {

        $this->conn = new mysqli(self::HOST, self::USER, self::PASSWORD, self::DBNAME);
    }
    public function selectAll()
    {
        $select = "SELECT * FROM " . $this->tableName;
        $query = ($this->conn)->query($select);
        $result = mysqli_fetch_all($query);
        return $result;
    }
    public function selectAllPosts()
    {
        $select = "SELECT * FROM " . $this->tableName . " ORDER BY date_time DESC";
        $query = ($this->conn)->query($select);
        $result = [];
        while ($row = $query->fetch_assoc()) {
            $result[] = $row;
        }
        return $result;
    }
    public function select($column, $value)
    {
        $select = "SELECT * FROM " . $this->tableName . " WHERE {$column} = '{$value}'";
        $query = ($this->conn)->query($select);
        // $result = mysqli_fetch_assoc($query);
        $rows = [];
        while ($row = $query->fetch_assoc()) {
            $rows[] = $row;
        }

        return $rows;
        // return $result;
    }
    public function insert($data)
    {
        $keys = array_keys($data);
        $keys = implode(',', $keys);
        $values = array_values($data);
        $values = "'" . implode("','", $values) . "'";
        $insert = "INSERT INTO {$this->tableName} ($keys) VALUES($values)";
        $query = $this->conn->query($insert);
        if (!$query) {
            return ("Query failed: " . $this->conn->error);
        }
    }
    public function update($data, $id)
    {
        $set = '';
        $params = [];
        foreach ($data as $key => $value) {
            $set .= "$key = ?, ";
            $params[] = $value;
        }
        $set = rtrim($set, ', ');
        $params[] = $id;

        $sql = "UPDATE {$this->tableName} SET $set WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param(str_repeat('s', count($params)), ...$params);
        return $stmt->execute();
    }
    public function delete($column, $value)
    {
        $delete = "DELETE FROM " . $this->tableName . " WHERE {$column} = '$value' ";
        $query = ($this->conn)->query($delete);
    }
    public function deletePost($user_id, $post_id)
    {
        $delete = "DELETE FROM " . $this->tableName . " WHERE id = '{$post_id}' AND user_id = '{$user_id}'";
        $query = ($this->conn)->query($delete);
    }
    public function deleteComment($post_id, $user_id)
    {
        $delete = "DELETE FROM " . $this->tableName . " WHERE id = '{$post_id}' AND user_id = '$user_id' ";
        $query = ($this->conn)->query($delete);
    }
}
