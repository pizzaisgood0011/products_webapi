<?php
class ProductImage {
    private $conn;
    private $table = 'product_images';

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getByProductId($productId) {
        $stmt = $this->conn->prepare(
            "SELECT * FROM {$this->table} 
            WHERE product_id = ? 
            ORDER BY created_at DESC"
        );
        $stmt->bind_param("i", $productId);
        $stmt->execute();
        $result = $stmt->get_result();

        $images = [];
        while ($row = $result->fetch_assoc()) {
            $images[] = $row;
        }
        return $images;
    }

    // READ ONE
    public function getById($id) {
        $stmt = $this->conn->prepare(
            "SELECT * FROM {$this->table} WHERE image_id = ?"
        );
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function create($data) {
        $stmt = $this->conn->prepare(
            "INSERT INTO {$this->table} (image_url, is_thumbnail, product_id)
            VALUES (?, ?, ?)"
        );
        $stmt->bind_param(
            "sii",
            $data['image_url'],
            $data['is_thumbnail'],
            $data['product_id']
        );

        if ($stmt->execute()) {
            return $this->conn->insert_id;
        }
        return false;
    }

    public function delete($id) {
        $stmt = $this->conn->prepare(
            "DELETE FROM {$this->table} WHERE image_id = ?"
        );
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->affected_rows;
    }
}