<?php
class Category {
    private $conn;
    private $table = 'categories';

    // constructor runs when we do: new Category($conn)
    // it receives the connection and stores it
    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getAll(){
        $query = "SELECT * FROM $this->table";
        $result = $this->conn->query($query);

        $categories = [];
        while ($row = $result-> fetch_assoc()) {
            $categories[] = $row;
        }

        return $categories;
    }

    public function getById($id) {
        $stmt = $this->conn->prepare(
            "SELECT * FROM $this->table WHERE category_id = ?"
        );
        $stmt->bind_param("i", $id);
        $stmt->execute();

        $result = $stmt->get_result();
        return $result->fetch_assoc(); // return one row or null
    }

    public function create($data) {
        $stmt = $this->conn->prepare(
            "INSERT INTO $this->table (category_name, slug) VALUES (?, ?)"
        );
        $stmt->bind_param("ss", $data['category_name'], $data['slug']);

        if($stmt->execute()){
            return $this->conn->insert_id; // returns the new category_id
        }
        return false;
    }

    public function update($id, $data){
        $stmt = $this->conn->prepare(
            "UPDATE $this->table
            SET category_name = ?, slug = ?
            WHERE category_id = ?"
        );
        $stmt->bind_param("ssi",
            $data['category_name'],
            $data['slug'],
            $id
        );

        $stmt->execute();
        return $stmt->affected_rows; // returns 0 if nothing changed
    }

    public function delete($id) {
        $stmt = $this->conn->prepare(
            "DELETE FROM $this->table WHERE category_id = ?"
        );
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->affected_rows;  // returns 0 if not found
    }
}
?>