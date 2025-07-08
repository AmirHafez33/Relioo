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
    public function selectUser($column,$value)
    {
        $select = "SELECT * FROM " . $this->tableName . " WHERE {$column} = '{$value}'";
        $query = ($this->conn)->query($select);
        $result = mysqli_fetch_assoc($query);
        return $result;
    }
    public function select($column, $value)
{
    $select = "SELECT * FROM " . $this->tableName . " WHERE {$column} = '{$value}' ";
    $query = ($this->conn)->query($select);

    $rows = [];
    if ($query) {
        while ($row = mysqli_fetch_assoc($query)) {
            $rows[] = $row;
        }
    }

    return $rows;
}
public function selectUserPosts($column, $value)
{
    // تأكد من أن اسم العمود آمن (لا تستخدم إدخال المستخدم مباشرة)
    if (!in_array($column, ['user_id', 'username'])) {
        return []; // أو ممكن ترمي Exception
    }

    // استخدم prepared statement لتجنب SQL injection
    $stmt = $this->conn->prepare("SELECT * FROM {$this->tableName} WHERE {$column} = ? ORDER BY date_time DESC");
    $stmt->bind_param("s", $value);
    $stmt->execute();

    $result = $stmt->get_result();
    $rows = [];

    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }

    return $rows;
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
    public function deleteComment($comment_id, $user_id)
    {
        $delete = "DELETE FROM " . $this->tableName . " WHERE id = '{$comment_id}' AND user_id = '$user_id' ";
        $query = ($this->conn)->query($delete);
    }
    public function addNotification($userId, $message, $post_id,$post_text,$by_user_id) {
        
        $stmt = $this->conn->prepare("INSERT INTO notifications (user_id, message, post_id , post_text,by_user_id) VALUES (?, ?, ?, ?,?)");
        $stmt->bind_param("isisi", $userId, $message, $post_id,$post_text,$by_user_id);
        $stmt->execute();
    }

    public function addActivity($userId, $message, $post_id,$post_text,$action) {
        $is_read = 0 ;
        $stmt = $this->conn->prepare("INSERT INTO activities (user_id, message, post_id , post_text,action ,is_read) VALUES (?, ?, ?, ?,?,?)");
        $stmt->bind_param("isissi", $userId, $message, $post_id,$post_text,$action,$is_read);
        $stmt->execute();
    }
    

 public function selectAllrelation($tableRelation,$columnrelation){
$select = "SELECT * FROM " . $this->tableName . 
          " LEFT JOIN " . $tableRelation . 
          " ON " . $this->tableName . "." . $columnrelation . 
          " = " . $tableRelation . ".id".
          " ORDER BY " . $this->tableName . ".date_time DESC";
        $query = ($this->conn)->query($select);
            $rows = [];
            if ($query) {
                while ($row = mysqli_fetch_assoc($query)) {
                    $rows[] = $row;
                }
            }

            return $rows;
    }
}
