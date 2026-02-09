<?php
class Patient
{
    private $conn;
    private $table_name = "patients";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // 1. Create Patient
    public function create($data)
    {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET name=:name, age=:age, gender=:gender, phone=:phone, address=:address, user_email=:user_email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":name", $data['name']);
        $stmt->bindParam(":age", $data['age']);
        $stmt->bindParam(":gender", $data['gender']);
        $stmt->bindParam(":phone", $data['phone']);
        $stmt->bindParam(":address", $data['address']);
        $stmt->bindParam(":user_email", $data['user_email']);
        return $stmt->execute();
    }

    // 2. Update Patient (Returns Statement for rowCount check)
    public function update($id, $email, $data)
    {
        $query = "UPDATE " . $this->table_name . " 
                  SET name=:name, age=:age, gender=:gender, phone=:phone, address=:address 
                  WHERE id=:id AND user_email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":name", $data['name']);
        $stmt->bindParam(":age", $data['age']);
        $stmt->bindParam(":gender", $data['gender']);
        $stmt->bindParam(":phone", $data['phone']);
        $stmt->bindParam(":address", $data['address']);
        $stmt->execute();
        return $stmt;
    }

    // 3. Delete Patient (Returns Statement for rowCount check)
    public function delete($id, $email)
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id AND user_email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->bindParam(":email", $email);

        $stmt->execute();
        return $stmt; // Boolean-ku bathila statement-ai return pannunga!
    }

    public function readByUser($email)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE user_email = :email ORDER BY id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $email);
        $stmt->execute();
        return $stmt;
    }

    public function readOne($id, $email)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id AND user_email = :email LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->bindParam(":email", $email);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
