<?php
class Review {
    private $conn;
    private $table = 'reviews';

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // get all by product
    public function getByProductId($productId) {
        $stmt = $this->conn->prepare(
            "SELECT * FROM {$this->table} 
            WHERE product_id = ? 
            ORDER BY created_at DESC"
        );
        $stmt->bind_param("i", $productId);
        $stmt->execute();
        $result = $stmt->get_result();

        $reviews = [];
        while ($row = $result->fetch_assoc()) {
            $reviews[] = $row;
        }
        return $reviews;
    }

    public function getById($id) {
        $stmt = $this->conn->prepare(
            "SELECT * FROM {$this->table} WHERE review_id = ?"
        );
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function create($data) {
        $stmt = $this->conn->prepare(
            "INSERT INTO {$this->table} 
                (rating, comment, reviewer_name, reviewer_email, product_id)
            VALUES (?, ?, ?, ?, ?)"
        );
        $stmt->bind_param(
            "dsssi",
            $data['rating'],
            $data['comment'],
            $data['reviewer_name'],
            $data['reviewer_email'],
            $data['product_id']
        );

        if ($stmt->execute()) {
            return $this->conn->insert_id;
        }
        return false;
    }

    public function delete($id) {
        $stmt = $this->conn->prepare(
            "DELETE FROM {$this->table} WHERE review_id = ?"
        );
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->affected_rows;
    }
}