<?php
class CategoryController{
    private $model;

    // receives $conn from api/categories.pgp and passes it to the model
    public function __construct($conn){
        $this->model = new Category($conn);
    }

    public function getAll() {
        $data = $this->model->getAll();

        http_response_code(200);
        echo json_encode([
            "success" => true,
            "total" => count($data),
            "categories" => $data
        ]);
    }

    public function getById($id) {
        $data = $this->model->getById($id);
        if (!$data) {
            http_response_code(404);
            echo json_encode([
                "success" => false,
                "error" => "Category with ID $id not found."
            ]);
            return;
        }

        http_response_code(200);
        echo json_encode([
            "success" => true,
            "category" => $data
        ]);
    }

    public function create() {
        $body = $this->getBody();
        // validate required fields
        if (empty($body['category_name']) || empty($body['slug'])) {
            http_response_code(400);
            echo json_encode([
                "success" => false,
                "error" => "Fields 'category_name' and 'slug' are required."
            ]);
            return;
        }

        $newId = $this->model->create($body);
        if (!$newId) {
            http_response_code(500);
            echo json_encode([
                "success" => false,
                "error" => "Failed to create category. Slug may already exist."
            ]);
            return;
        }

        http_response_code(201);
        echo json_encode([
            "success" => true,
            "message" => "Category created successfully.",
            "category_id" => $newId
        ]);
    }

    public function update($id) {
        $existing = $this->model->getById($id);
        if (!$existing) {
            http_response_code(404);
            echo json_encode([
                "success" => false,
                "error"   => "Category with ID $id not found."
            ]);
            return;
        }

        $body = $this->getBody();

        if (empty($body['category_name']) && empty($body['slug'])) {
            http_response_code(400);
            echo json_encode([
                "success" => false,
                "error"   => "Provide at least one field to update."
            ]);
            return;
        }

        // if a field is not provided, keep the existing value
        $body['category_name'] = $body['category_name'] ?? $existing['category_name'];
        $body['slug']          = $body['slug'] ?? $existing['slug'];

        $this->model->update($id, $body);

        http_response_code(200);
        echo json_encode([
            "success" => true,
            "message" => "Category ID $id updated successfully."
        ]);
    }

    public function delete($id) {
        $existing = $this->model->getById($id);
        if (!$existing) {
            http_response_code(404);
            echo json_encode([
                "success" => false,
                "error" => "Category with ID $id not found."
            ]);
            return;
        }

        $this->model->delete($id);

        http_response_code(200);
        echo json_encode([
            "success" => true,
            "message" => "Category ID $id deleted successfully."
        ]);
    }

    // helpe reads and parses the JSON request body
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
?>