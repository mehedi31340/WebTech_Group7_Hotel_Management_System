<?php
require_once __DIR__ . '/../models/Room.php';   
require_once __DIR__ . '/../models/HousekeepingTask.php';
require_once __DIR__ . '/../models/MaintenanceReport.php';

$controller = new HousekeepingController();
$apiController = new HousekeepingApiController();
$authController = new AuthController();

class HousekeepingController
{
   public function dashboard()
{
    $roomModel = new Room();

    $rooms = $roomModel->getAllRooms();


     $taskModel = new HousekeepingTask();

    $tasks = $taskModel->getAllTasks();

    $roomStatusCounts = $roomModel->getRoomStatusCounts();

    $pendingInspection = $taskModel->getPendingInspectionCount();

    $maintenanceModel = new MaintenanceReport();

    $totalMaintenanceReports = $maintenanceModel->getTotalReportCount();    

    $completedTasks = $taskModel->getCompletedTaskCount();

    require_once __DIR__ . '/../views/housekeeping/dashboard.php';
}

public function roomStatus()
{
    $roomModel = new Room();

    $rooms = $roomModel->getAllRooms();

    $statusCounts = $roomModel->getRoomStatusCounts();

    require_once __DIR__ . '/../views/housekeeping/room-status.php';
}

    public function tasks()
{

     $taskModel = new HousekeepingTask();

     $tasks = $taskModel->getAllTasks();

      $taskSummary = $taskModel->getTaskSummary();

    require_once __DIR__ . '/../views/housekeeping/tasks.php';
}

public function manageRoom()
{
    $id = (int) ($_GET['id'] ?? 0);

    if ($id <= 0) {
        header('Location: ?page=room-status');
        exit;
    }

    $roomModel = new Room();

    $room = $roomModel->getRoomById($id);

    if (!$room) {
        header('Location: ?page=room-status');
        exit;
    }

    require_once __DIR__ . '/../views/housekeeping/manage-room.php';
}

public function updateRoom()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: ?page=room-status');
        exit;
    }

    $id = (int) ($_POST['id'] ?? 0);
    $status = $_POST['status'] ?? '';
    $notes = trim($_POST['notes'] ?? '');

 $allowedStatuses = [
    'available',
    'occupied',
    'dirty',
    'in_progress',
    'maintenance',
    'blocked'
];

    if (
        $id <= 0 ||
        !in_array($status, $allowedStatuses, true)
    ) {
        header('Location: ?page=room-status');
        exit;
    }

    $roomModel = new Room();

    $roomModel->updateRoom($id, $status, $notes);

    header('Location: ?page=room-status');
    exit;
}

public function createTask()
{

 $taskModel = new HousekeepingTask();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $roomId = (int) $_POST['room_id'];
        $assignedTo = (int) $_POST['assigned_to'];
        $taskType = $_POST['task_type'];
        $priority = $_POST['priority'];
        $scheduledDate = $_POST['scheduled_date'];

        $success = $taskModel->createTask(
            $roomId,
            $assignedTo,
            $taskType,
            $priority,
            $scheduledDate
        );

        if ($success) {
            header("Location: ?page=tasks");
            exit;
        }

        die("Failed to create housekeeping task.");
    }


    $taskModel = new HousekeepingTask();

    $rooms = $taskModel->getAllRooms();

    $housekeepers = $taskModel->getHousekeepers();

    require_once __DIR__ . '/../views/housekeeping/create-task.php';
}

public function manageTask()
{
    $taskModel = new HousekeepingTask();

    $taskId = isset($_GET['id'])
        ? (int) $_GET['id']
        : 0;

    if ($taskId <= 0) {
        die("Invalid task ID.");
    }


    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $action = $_POST['action'] ?? 'update';


        // Remove task
        if ($action === 'delete') {

            $success = $taskModel->deleteTask($taskId);

            if ($success) {

                header("Location: ?page=tasks");

                exit;
            }

            die("Failed to remove housekeeping task.");
        }


        // Update task
        $assignedTo = (int) $_POST['assigned_to'];

        $taskType = $_POST['task_type'];

        $priority = $_POST['priority'];

        $status = $_POST['status'];

        $scheduledDate = $_POST['scheduled_date'];


        $success = $taskModel->updateTask(
            $taskId,
            $assignedTo,
            $taskType,
            $priority,
            $status,
            $scheduledDate
        );


        if ($success) {

            header("Location: ?page=tasks");

            exit;
        }

        die("Failed to update housekeeping task.");
    }


    $task = $taskModel->getTaskById($taskId);


    if (!$task) {

        die("Task not found.");
    }


    $housekeepers = $taskModel->getHousekeepers();


    require_once __DIR__ . '/../views/housekeeping/manage-task.php';
}


public function maintenance()
{
    $maintenanceModel = new MaintenanceReport();

    $reports = $maintenanceModel->getAllReports();

       $maintenanceSummary = $maintenanceModel->getMaintenanceSummary();

    require_once __DIR__ . '/../views/housekeeping/maintenance.php';
}

public function reportMaintenance()
{
    $maintenanceModel = new MaintenanceReport();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $roomId = (int) $_POST['room_id'];

        $reportedBy = (int) $_POST['reported_by'];

        $description = trim($_POST['description']);

        $severity = $_POST['severity'];

        $status = $_POST['status'];


        $success = $maintenanceModel->createReport(
            $roomId,
            $reportedBy,
            $description,
            $severity,
            $status
        );


        if ($success) {

            header("Location: ?page=maintenance");

            exit;
        }


        die("Failed to create maintenance report.");
    }


    $rooms = $maintenanceModel->getAllRooms();

    $housekeepers = $maintenanceModel->getHousekeepers();


    require_once __DIR__ . '/../views/housekeeping/report-maintenance.php';
}

public function manageMaintenance()
{
    $maintenanceModel = new MaintenanceReport();

    $reportId = isset($_GET['id'])
        ? (int) $_GET['id']
        : 0;


    if ($reportId <= 0) {
        die("Invalid maintenance report ID.");
    }


    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

     
    $action = $_POST['action'] ?? 'update';


    // Delete maintenance report

    if ($action === 'delete') {

        $success = $maintenanceModel->deleteReport($reportId);


        if ($success) {

            header("Location: ?page=maintenance");

            exit;
        }


        die("Failed to delete maintenance report.");
    }


    //____________Update maintenance report____________

    $description = trim($_POST['description']);

    $severity = $_POST['severity'];

    $status = $_POST['status'];


    $success = $maintenanceModel->updateReport(
        $reportId,
        $description,
        $severity,
        $status
    );


    if ($success) {

        header("Location: ?page=maintenance");

        exit;
    }


    die("Failed to update maintenance report.");
    }


    $report = $maintenanceModel->getReportById($reportId);


    if (!$report) {

        die("Maintenance report not found.");
    }


    require_once __DIR__ . '/../views/housekeeping/manage-maintenance.php';
}

}