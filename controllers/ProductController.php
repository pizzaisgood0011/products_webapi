<?php
class ProductController {
    private $model;

    public function __construct($conn) {
        $this->model = new Product($conn);
    }

    // supports pagination: /api/products.php?limit=10&skip=0
    public function getAll() {
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
    $skip = isset($_GET['skip']) ? (int)$_GET['skip'] : 0;
    $search = isset($_GET['search']) ? trim($_GET['search']) : null;
    $category = isset($_GET['category']) ? trim($_GET['category']) : null;

    $data = $this->model->getAll($limit, $skip, $search, $category);
    $total = $this->model->countAll($search, $category);

    http_response_code(200);
    echo json_encode([
        "success" => true,
        "total" => $total,
        "skip" => $skip,
        "limit" => $limit,
        "products" => $data
    ]);
}

    public function getById($id) {
        $data = $this->model->getById($id);

        if (!$data) {
            http_response_code(404);
            echo json_encode([
                "success" => false,
                "error"   => "Product with ID $id not found."
            ]);
            return;
        }

        http_response_code(200);
        echo json_encode([
            "success" => true,
            "product" => $data
        ]);
    }

    public function create() {
        $body = $this->getBody();

        // required fields
        $required = ['product_name', 'product_title', 'SKU', 'price'];
        foreach ($required as $field) {
            if (empty($body[$field])) {
                http_response_code(400);
                echo json_encode([
                    "success" => false,
                    "error" => "Field '$field' is required."
                ]);
                return;
            }
        }

        // set defaults for optional fields
        $body['brand'] = $body['brand'] ?? null;
        $body['description'] = $body['description'] ?? null;
        $body['tags'] = $body['tags'] ?? null;
        $body['discount_percentage'] = $body['discount_percentage'] ?? 0;
        $body['stock'] = $body['stock'] ?? 0;
        $body['quantity'] = $body['quantity'] ?? 0;
        $body['rating'] = $body['rating'] ?? null;
        $body['availability_status'] = $body['availability_status'] ?? 'In Stock';
        $body['thumbnail'] = $body['thumbnail'] ?? null;
        $body['category_id'] = $body['category_id'] ?? null;

        $newId = $this->model->create($body);

        if (!$newId) {
            http_response_code(500);
            echo json_encode([
                "success" => false,
                "error" => "Failed to create product. SKU may already exist."
            ]);
            return;
        }

        // return the newly created product
        $product = $this->model->getById($newId);

        http_response_code(201);
        echo json_encode([
            "success" => true,
            "message" => "Product created successfully.",
            "product" => $product
        ]);
    }

    public function update($id) {
        $existing = $this->model->getById($id);
        if (!$existing) {
            http_response_code(404);
            echo json_encode([
                "success" => false,
                "error" => "Product with ID $id not found."
            ]);
            return;
        }

        $body = $this->getBody();

        if (empty($body)) {
            http_response_code(400);
            echo json_encode([
                "success" => false,
                "error" => "Provide at least one field to update."
            ]);
            return;
        }

        // keep existing values if not provided
        $body['product_name'] = $body['product_name'] ?? $existing['product_name'];
        $body['product_title'] = $body['product_title'] ?? $existing['product_title'];
        $body['SKU'] = $body['SKU'] ?? $existing['SKU'];
        $body['brand'] = $body['brand'] ?? $existing['brand'];
        $body['description'] = $body['description'] ?? $existing['description'];
        $body['tags'] = $body['tags'] ?? $existing['tags'];
        $body['price'] = $body['price'] ?? $existing['price'];
        $body['discount_percentage'] = $body['discount_percentage'] ?? $existing['discount_percentage'];
        $body['stock'] = $body['stock'] ?? $existing['stock'];
        $body['quantity'] = $body['quantity'] ?? $existing['quantity'];
        $body['rating'] = $body['rating'] ?? $existing['rating'];
        $body['availability_status'] = $body['availability_status'] ?? $existing['availability_status'];
        $body['thumbnail'] = $body['thumbnail'] ?? $existing['thumbnail'];
        $body['category_id'] = $body['category_id'] ?? $existing['category_id'];

        $this->model->update($id, $body);

        // return the updated product
        $product = $this->model->getById($id);

        http_response_code(200);
        echo json_encode([
            "success" => true,
            "message" => "Product ID $id updated successfully.",
            "product" => $product
        ]);
    }

    public function delete($id) {
        $existing = $this->model->getById($id);
        if (!$existing) {
            http_response_code(404);
            echo json_encode([
                "success" => false,
                "error" => "Product with ID $id not found."
            ]);
            return;
        }

        $this->model->delete($id);

        http_response_code(200);
        echo json_encode([
            "success" => true,
            "message" => "Product ID $id deleted successfully."
        ]);
    }

    // get body method
    private function getBody() {
        $body = json_decode(file_get_contents("php://input"), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            http_response_code(400);
            echo json_encode(["success" => false, "error" => "Invalid JSON body."]);
            exit();
        }

        return $body ?? [];
    }
}