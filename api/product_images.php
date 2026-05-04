<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, DELETE");
header("Access-Control-Allow-Headers: Content-Type");

require_once '../config/connection_db.php';
require_once '../models/ProductImage.php';
require_once '../controllers/ProductImageController.php';

$method = $_SERVER['REQUEST_METHOD'];
$id = isset($_GET['id']) ? (int)$_GET['id'] : null;
$productId = isset($_GET['product_id']) ? (int)$_GET['product_id'] : null;
$controller = new ProductImageController($conn);

switch ($method) {
    case 'GET':
        if (!$productId) {
            http_response_code(400);
            echo json_encode(["success" => false, "error" => "Missing 'product_id' parameter."]);
            break;
        }
        $controller->getByProductId($productId);
        break;

    case 'POST':
        $controller->create();
        break;

    case 'DELETE':
        if (!$id) {
            http_response_code(400);
            echo json_encode(["success" => false, "error" => "Missing 'id' parameter."]);
            break;
        }
        $controller->delete($id);
        break;

    default:
        http_response_code(405);
        echo json_encode(["success" => false, "error" => "Method not allowed."]);
        break;
}