<?php

/** @var array $rooms */

/** @var array $tasks */

/** @var int $pendingInspection */

/** @var array $roomStatusCounts */

/** @var int $totalMaintenanceReports */

/** @var int $completedTasks */


?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Housekeeping Dashboard</title>

    <link rel="stylesheet" href="css/housekeeping.css">
</head>

<body>

    <div class="dashboard">

        <!-- Sidebar -->
        <aside class="sidebar">

            <div class="sidebar-header">
                <h2>Hotel Management</h2>
                <p>Housekeeping</p>
            </div>

            <nav class="sidebar-nav">

            <a href="?page=dashboard" class="nav-link active">
                   Dashboard
              </a>
             <a href="?page=room-status" class="nav-link ">
                    Room Status
               </a>


              <a href="?page=tasks" class="nav-link ">
                    Housekeeping Tasks
                 </a>

                <a href="?page=maintenance" class="nav-link ">
                Maintenance
            </a>

            </nav>

        </aside>


        <!-- Main Content -->
        <main class="main-content">

            <!-- Top Header -->
            <header class="topbar">

                <div>
                    <h1>Housekeeping Dashboard</h1>
                   <!-- <p>Monitor room readiness and housekeeping activities.</p> -->
                </div>

             <div class="user-info">
   <a href="index.php?page=login" style="text-decoration: none; font-weight:bold;color:black;">Profile</a>
</div>

            </header>


            <!-- Dashboard Summary Cards -->
            <section class="summary-cards">

                <div class="summary-card">

                    <div class="card-content">
                        <p>Dirty Rooms</p>
                        <h2><?= $roomStatusCounts['dirty'] ?></h2>
                    </div>

                </div>


                <div class="summary-card">

                    <div class="card-content">
                        <p>Pending Inspection</p>
                       <h2><?= $pendingInspection ?></h2>
                    </div>

                </div>


                <div class="summary-card">

                    <div class="card-content">
                        <p>Maintenance Report</p>
                       <h2><?= $totalMaintenanceReports ?></h2>
                    </div>

                </div>


                <div class="summary-card">

                    <div class="card-content">
                        <p>Completed Tasks</p>
                       <h2><?= $completedTasks ?></h2>
                    </div>

                </div>

            </section>


            <!-- Room Status -->
            <section class="dashboard-section">

                <div class="section-header">

                    <div>
                        <h2>Room Status Overview</h2>
                     
                    </div>

                    <a href="?page=room-status" class="view-all">
                        View All
                    </a>

                </div>


                <div class="table-container">

                    <table>

                        <thead>

                            <tr>
                                <th>Room</th>
                                <th>Floor</th>
                                <th>Room Type</th>
                                <th>Status</th>
                            </tr>

                        </thead>

<tbody>

    <?php foreach (array_slice($rooms, 0, 5) as $room): ?>

        <tr>

            <td>
                <?= htmlspecialchars($room['room_number']) ?>
            </td>

            <td>
                <?= htmlspecialchars($room['floor']) ?>
            </td>

            <td>
                <?= htmlspecialchars($room['room_type']) ?>
            </td>

          <td>

    <?php
    $status = $room['status'];

    $statusLabel = match ($status) {
        'available' => 'Available',
        'occupied' => 'Occupied',
        'dirty' => 'Dirty',
        'in_progress' => 'Cleaning',
        'maintenance' => 'Maintenance',
        'blocked' => 'Blocked',
        default => ucfirst($status)
    };

    $statusClass = match ($status) {
        'available' => 'status-ready',
        'occupied' => 'status-occupied',
        'dirty' => 'status-dirty',
        'in_progress' => 'status-cleaning',
        'maintenance' => 'status-maintenance',
        'blocked' => 'status-blocked',
        default => 'status-dirty'
    };
    ?>

    <span class="status <?= $statusClass ?>">
        <?= htmlspecialchars($statusLabel) ?>
    </span>

</td>

        </tr>

    <?php endforeach; ?>

</tbody>
                        
                    </table>

                </div>

            </section>


            <!-- Recent Tasks -->
            <section class="dashboard-section">

                <div class="section-header">

                    <div>
                        <h2>Recent Housekeeping Tasks</h2>
                       
                    </div>

                    <a href="?page=tasks" class="view-all">
                        View All
                    </a>

                </div>


                <div class="table-container">

                    <table>

                        <thead>

                            <tr>
                                <th>Room</th>
                                <th>Task</th>
                                <th>Assigned To</th>
                                <th>Priority</th>
                                <th>Status</th>
                            </tr>

                        </thead>


                       <tbody>

    <?php foreach (array_slice($tasks, 0, 5) as $task): ?>

        <tr>

            <td>
                <?= htmlspecialchars($task['room_number']) ?>
            </td>

            <td>
                <?= htmlspecialchars($task['task_type']) ?>
            </td>

            <td>
                <?= htmlspecialchars($task['assigned_to_name']) ?>
            </td>

            <td>

                <?php
                $priority = $task['priority'];

                $priorityLabel = match ($priority) {
                    'low' => 'Low',
                    'medium' => 'Medium',
                    'high' => 'High',
                    'urgent' => 'Urgent',
                    default => ucfirst($priority)
                };

                $priorityClass = match ($priority) {
                    'low' => 'priority-low',
                    'medium' => 'priority-medium',
                    'high' => 'priority-high',
                    'urgent' => 'priority-high',
                    default => 'priority-medium'
                };
                ?>

                <span class="priority <?= $priorityClass ?>">
                    <?= htmlspecialchars($priorityLabel) ?>
                </span>

            </td>

            <td>

                <?php
                $status = $task['status'];

                $statusLabel = match ($status) {
                    'pending' => 'Pending',
                    'in_progress' => 'In Progress',
                    'done' => 'Completed',
                    default => ucfirst($status)
                };

                $statusClass = match ($status) {
                    'pending' => 'status-dirty',
                    'in_progress' => 'status-cleaning',
                    'done' => 'status-ready',
                    default => 'status-dirty'
                };
                ?>

                <span class="status <?= $statusClass ?>">
                    <?= htmlspecialchars($statusLabel) ?>
                </span>

            </td>

        </tr>

    <?php endforeach; ?>

</tbody>

                    </table>

                </div>

            </section>

        </main>

    </div>

</body>
</html>