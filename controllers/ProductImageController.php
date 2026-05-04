<?php
class ProductImageController {
    private $model;

    public function __construct($conn) {
        $this->model = new ProductImage($conn);
    }

    public function getByProductId($productId) {
        $data = $this->model->getByProductId($productId);

        http_response_code(200);
        echo json_encode([
            "success" => true,
            "total" => count($data),
            "product_id" => $productId,
            "images" => $data
        ]);
    }

    public function create() {
        $body = $this->getBody();

        $required = ['image_url', 'product_id'];
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

        $body['is_thumbnail'] = $body['is_thumbnail'] ?? 0;

        $newId = $this->model->create($body);

        if (!$newId) {
            http_response_code(500);
            echo json_encode([
                "success" => false,
                "error"  => "Failed to add image."
            ]);
            return;
        }

        $image = $this->model->getById($newId);

        http_response_code(201);
        echo json_encode([
            "success" => true,
            "message" => "Image added successfully.",
            "image" => $image
        ]);
    }

    public function delete($id) {
        $existing = $this->model->getById($id);
        if (!$existing) {
            http_response_code(404);
            echo json_encode([
                "success" => false,
                "error" => "Image with ID $id not found."
            ]);
            return;
        }

        $this->model->delete($id);

        http_response_code(200);
        echo json_encode([
            "success" => true,
            "message" => "Image ID $id deleted successfully."
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