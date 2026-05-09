<?php
class Product{
    private $conn;
    private $table = 'products';

    public function __construct($conn){
        $this->conn = $conn;
    }

    // get all - includes category, images, reviews
    public function getAll($limit = 10, $skip = 0, $search = null, $category = null) {
        $query = "SELECT p.*, 
                        c.category_name, c.slug AS category_slug
                FROM {$this->table} p
                LEFT JOIN categories c ON p.category_id = c.category_id
                WHERE 1=1";

        // dynamically build WHERE clause
        $params = [];
        $types = "";

        if ($search) {
            $query .= " AND (p.product_name LIKE ? OR p.product_title LIKE ? OR p.brand LIKE ?)";
            $search = "%$search%";
            $params[] = &$search;
            $params[] = &$search;
            $params[] = &$search;
            $types .= "sss";
        }

        if ($category) {
            $query .= " AND c.slug = ?";
            $params[] = &$category;
            $types .= "s";
        }

        $query .= " ORDER BY p.created_at DESC LIMIT ? OFFSET ?";
        $params[] = &$limit;
        $params[] = &$skip;
        $types .= "ii";

        $stmt = $this->conn->prepare($query);

        if (!empty($params)) {
            array_unshift($params, $types);
            call_user_func_array([$stmt, 'bind_param'], $params);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        $products = [];
        while ($row = $result->fetch_assoc()) {
            $row['images'] = $this->getImages($row['product_id']);
            $row['reviews'] = $this->getReviews($row['product_id']);
            $products[]  = $row;
        }

        return $products;
    }

    // count all - for total in response - search
    public function countAll($search = null, $category = null) {
        $query  = "SELECT COUNT(*) AS total 
                FROM {$this->table} p
                LEFT JOIN categories c ON p.category_id = c.category_id
                WHERE 1=1";

        $params = [];
        $types = "";

        if ($search) {
            $query .= " AND (p.product_name LIKE ? OR p.product_title LIKE ? OR p.brand LIKE ?)";
            $search = "%$search%";
            $params[] = &$search;
            $params[] = &$search;
            $params[] = &$search;
            $types .= "sss";
        }

        if ($category) {
            $query .= " AND c.slug = ?";
            $params[] = &$category;
            $types .= "s";
        }

        $stmt = $this->conn->prepare($query);

        if (!empty($params)) {
            array_unshift($params, $types);
            call_user_func_array([$stmt, 'bind_param'], $params);
        }

        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return (int)$row['total'];
    }

    public function getById($id) {
        $stmt = $this->conn->prepare(
            "SELECT p.*, 
                c.category_name, c.slug AS category_slug
            FROM {$this->table} p
            LEFT JOIN categories c ON p.category_id = c.category_id
            WHERE p.product_id = ?"
        );
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $product = $result->fetch_assoc();

        if ($product) {
            $product['images']  = $this->getImages($id);
            $product['reviews'] = $this->getReviews($id);
        }

        return $product;
    }

    public function create($data) {
        $stmt = $this->conn->prepare(
            "INSERT INTO {$this->table} (
                product_name, product_title, SKU, brand, description,
                tags, price, discount_percentage, stock, quantity,
                rating, availability_status, thumbnail, category_id
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );

        $stmt->bind_param(
            "sssssssdiiidsi",
            $data['product_name'], $data['product_title'], $data['SKU'], $data['brand'],
            $data['description'], $data['tags'], $data['price'], $data['discount_percentage'],
            $data['stock'], $data['quantity'], $data['rating'], $data['availability_status'],
            $data['thumbnail'], $data['category_id']
        );

        if ($stmt->execute()) {
            return $this->conn->insert_id;
        }
        return false;
    }

    public function update($id, $data) {
        $stmt = $this->conn->prepare(
            "UPDATE {$this->table}
            SET product_name = ?, product_title = ?, SKU = ?, brand = ?, description = ?,
                tags = ?, price = ?, discount_percentage = ?, stock = ?, quantity = ?,
                rating = ?, availability_status = ?, thumbnail = ?, category_id = ?
            WHERE product_id = ?"
        );

        $stmt->bind_param(
            "ssssssddiidssii",
            $data['product_name'], $data['product_title'], $data['SKU'], $data['brand'],
            $data['description'], $data['tags'], $data['price'], $data['discount_percentage'],
            $data['stock'], $data['quantity'], $data['rating'], $data['availability_status'],
            $data['thumbnail'],$data['category_id'],
            $id
        );

        $stmt->execute();
        return $stmt->affected_rows;
    }

    public function delete($id) {
        $stmt = $this->conn->prepare(
            "DELETE FROM {$this->table} WHERE product_id = ?"
        );
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->affected_rows;
    }

    // private methods
    // get all images for a product
    private function getImages($productId) {
        $stmt = $this->conn->prepare(
            "SELECT image_id, image_url, is_thumbnail 
            FROM product_images 
            WHERE product_id = ?"
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

    // get all reviews for a product
    private function getReviews($productId) {
        $stmt = $this->conn->prepare(
            "SELECT review_id, rating, comment, 
                reviewer_name, reviewer_email, created_at
            FROM reviews 
            WHERE product_id = ?"
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
}
?>
