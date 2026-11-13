<?php
require_once '../controllers/DonorController.php';

$donorController = new DonorController();

$requestMethod = $_SERVER['REQUEST_METHOD'];
$uri = explode('/', trim($_SERVER['REQUEST_URI'], '/'));

if ($uri[1] === 'donors') {
    switch ($requestMethod) {
        case 'POST':
            $donorController->registerDonor();
            break;
        case 'GET':
            if (isset($uri[2])) {
                $donorController->getDonorById($uri[2]);
            } else {
                $donorController->getAllDonors();
            }
            break;
        case 'PUT':
            if (isset($uri[2])) {
                $donorController->updateDonor($uri[2]);
            }
            break;
        case 'DELETE':
            if (isset($uri[2])) {
                $donorController->deleteDonor($uri[2]);
            }
            break;
        default:
            http_response_code(405);
            echo json_encode(['message' => 'Method Not Allowed']);
            break;
    }
} else {
    http_response_code(404);
    echo json_encode(['message' => 'Not Found']);
}
?>