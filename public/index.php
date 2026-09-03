<?php

session_start();

require_once __DIR__ . '/../app/controllers/HousekeepingApiController.php';
require_once __DIR__ . '/../app/controllers/AuthController.php';
require_once __DIR__ . '/../app/controllers/HousekeepingController.php';
require_once __DIR__ . '/../app/middleware/auth.php';

$controller = new HousekeepingController();
$apiController = new HousekeepingApiController();
$authController = new AuthController();

$page = $_GET['page'] ?? 'dashboard';

switch ($page) {


case 'api-room-status':
    requireHousekeepingLogin();
    $apiController->roomStatus();
    break;

    case 'room-status':
        requireHousekeepingLogin();
        $controller->roomStatus();
        break;

        case 'manage-room':
             requireHousekeepingLogin();
    $controller->manageRoom();
    break;

case 'update-room':
     requireHousekeepingLogin();
    $controller->updateRoom();
    break;

        case 'tasks':
             requireHousekeepingLogin();
    $controller->tasks();
    break;

    case 'create-task':
         requireHousekeepingLogin();
    $controller->createTask();
    break;

    case 'manage-task':
         requireHousekeepingLogin();
    $controller->manageTask();
    break;

    case 'maintenance':
         requireHousekeepingLogin();
    $controller->maintenance();
    break;

case 'report-maintenance':
     requireHousekeepingLogin();
    $controller->reportMaintenance();
    break;

    case 'manage-maintenance':
         requireHousekeepingLogin();
    $controller->manageMaintenance();
    break;

     case 'login':
    $authController->login();
    break;

    case 'logout':
    $authController->logout();
    break;

 case 'register':
    $authController->register();
    break;

    case 'dashboard':
         requireHousekeepingLogin();
    default:
        $controller->dashboard();
        break;

   
  
}