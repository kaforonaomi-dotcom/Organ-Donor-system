<?php
require_once '../config/database.php';
require_once '../controllers/DonorController.php';

$database = new Database();
$db = $database->getConnection();

$donorController = new DonorController($db);

$requestMethod = $_SERVER["REQUEST_METHOD"];
$requestUri = explode('/', trim($_SERVER['REQUEST_URI'], '/'));

if ($requestUri[1] === 'donors') {
    switch ($requestMethod) {
        case 'POST':
            $donorController->registerDonor();
            break;
        case 'GET':
            if (isset($requestUri[2])) {
                $donorController->getDonorById($requestUri[2]);
            } else {
                $donorController->getAllDonors();
            }
            break;
        case 'PUT':
            if (isset($requestUri[2])) {
                $donorController->updateDonor($requestUri[2]);
            }
            break;
        case 'DELETE':
            if (isset($requestUri[2])) {
                $donorController->deleteDonor($requestUri[2]);
            }
            break;
        default:
            http_response_code(405);
            echo json_encode(["message" => "Method Not Allowed"]);
            break;
    }
} else {
    http_response_code(404);
    echo json_encode(["message" => "Not Found"]);
}
?>