<?php
class ReviewController {
    private $model;

    public function __construct($conn) {
        $this->model = new Review($conn);
    }

    public function getByProductId($productId) {
        $data = $this->model->getByProductId($productId);

        http_response_code(200);
        echo json_encode([
            "success" => true,
            "total" => count($data),
            "product_id" => $productId,
            "reviews" => $data
        ]);
    }

    // POST /api/reviews.php
    public function create() {
        $body = $this->getBody();

        $required = ['rating', 'reviewer_name', 'reviewer_email', 'product_id'];
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

        // validate rating range
        if ($body['rating'] < 1 || $body['rating'] > 5) {
            http_response_code(400);
            echo json_encode([
                "success" => false,
                "error" => "Rating must be between 1 and 5."
            ]);
            return;
        }

        $body['comment'] = $body['comment'] ?? null;

        $newId = $this->model->create($body);

        if (!$newId) {
            http_response_code(500);
            echo json_encode([
                "success" => false,
                "error" => "Failed to create review."
            ]);
            return;
        }

        $review = $this->model->getById($newId);

        http_response_code(201);
        echo json_encode([
            "success" => true,
            "message" => "Review created successfully.",
            "review" => $review
        ]);
    }

    public function delete($id) {
        $existing = $this->model->getById($id);
        if (!$existing) {
            http_response_code(404);
            echo json_encode([
                "success" => false,
                "error" => "Review with ID $id not found."
            ]);
            return;
        }

        $this->model->delete($id);

        http_response_code(200);
        echo json_encode([
            "success" => true,
            "message" => "Review ID $id deleted successfully."
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