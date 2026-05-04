<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type");

require_once '../config/connection_db.php';
require_once '../models/Category.php';
require_once '../controllers/CategoryController.php';

$method = $_SERVER['REQUEST_METHOD'];
$id = isset($_GET['id']) ? (int)$_GET['id'] : null;
$controller = new CategoryController($conn);

switch ($method) {
    case 'GET':
        if ($id) {
            $controller->getById($id);
        } else {
            $controller->getAll();
        }
        break;

    case 'POST':
        $controller->create();
        break;

    case 'PUT':
        if (!$id) {
            http_response_code(400);
            echo json_encode(["success" => false, "error" => "Missing 'id' parameter."]);
            break;
        }
        $controller->update($id);
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