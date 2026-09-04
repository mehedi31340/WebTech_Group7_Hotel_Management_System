<?php

/** @var array $tasks */
/** @var array $taskSummary */


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Housekeeping Tasks</title>

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

            <a href="?page=dashboard" class="nav-link ">
                Dashboard
            </a>

            <a href="?page=room-status" class="nav-link ">
                Room Status
            </a>

            <a href="?page=tasks" class="nav-link active">
                Housekeeping Tasks
            </a>
            <a href="?page=maintenance" class="nav-link">
                Maintenance
            </a>


        </nav>

    </aside>


    <!-- Main Content -->
    <main class="main-content">

        <!-- Header -->
        <header class="topbar">

            <div>
                <h1>Housekeeping Tasks</h1>

                <p>
                    Assign tasks for housekeepers 
                </p>
            </div>


           <div class="user-info">
   <a href="index.php?page=login" style="text-decoration: none; font-weight:bold;color:black;">Profile</a>
</div>




        </header>


        <!-- Summary Cards -->
        <section class="summary-cards">

            <div class="summary-card">

                <div class="card-content">

                    <p>Pending</p>

                  <h2><?= $taskSummary['pending'] ?? 0 ?></h2>

                </div>

            </div>


            <div class="summary-card">

                <div class="card-content">

                    <p>In Progress</p>

                  <h2><?= $taskSummary['in_progress']?? 0 ?></h2>

                </div>

            </div>


            <div class="summary-card">

                <div class="card-content">

                    <p>Completed</p>

                   <h2><?= $taskSummary['completed']?? 0 ?></h2>

                </div>

            </div>


            <div class="summary-card">

                <div class="card-content">

                    <p>Urgent</p>
  <h2><?= $taskSummary['urgent']?? 0 ?></h2>
                    

                </div>

            </div>

        </section>


        <!-- Tasks Section -->
        <section class="dashboard-section">

            <div class="section-header">

                <div>

                    <h2>Task Management</h2>

                <!--    <p>
                        View and manage housekeeping activities.
                    </p> -->

                </div>


             <a 
             href="?page=create-task"
             class="action-button"
             type="button"
>
             Create Task
            </a>
            </div>


            <!-- Filters -->
            <div class="room-filters">

                <div class="filter-group">

                    <label for="task-status">
                        Status
                    </label>

                    <select id="task-status">

                        <option value="all">
                            All Statuses
                        </option>

                        <option value="pending">
                            Pending
                        </option>

                        <option value="in_progress">
                            In Progress
                        </option>

                        <option value="done">
                            Done
                        </option>

                    </select>

                </div>


                <div class="filter-group">

                    <label for="task-type">
                        Task Type
                    </label>

                    <select id="task-type">

                        <option value="all">
                            All Types
                        </option>

                        <option value="cleaning">
                            Cleaning
                        </option>

                        <option value="inspection">
                            Inspection
                        </option>

                        <option value="maintenance">
                            Maintenance
                        </option>

                    </select>

                </div>


                <div class="filter-group">

                    <label for="priority">
                        Priority
                    </label>

                    <select id="priority">

                        <option value="all">
                            All Priorities
                        </option>

                        <option value="normal">
                            Normal
                        </option>

                        <option value="urgent">
                            Urgent
                        </option>

                    </select>

                </div>


                <div class="filter-group search-group">

                    <label for="task-search">
                        Search
                    </label>

                    <input
                        type="text"
                        id="task-search"
                        placeholder="Search room..."
                    >

                </div>

            </div>


            <!-- Task Table -->
            <div class="table-container">

                <table id="task-table">

                    <thead>

                        <tr>

                            <th>
                                Room
                            </th>

                            <th>
                                Task
                            </th>

                            <th>
                                Assigned To
                            </th>

                            <th>
                                Priority
                            </th>

                            <th>
                                Scheduled
                            </th>

                            <th>
                                Status
                            </th>

                            <th>
                                Action
                            </th>

                        </tr>

                    </thead>

<tbody>

    <?php if (!empty($tasks)): ?>

        <?php foreach ($tasks as $task): ?>

            <tr
    data-room-number="<?= htmlspecialchars($task['room_number']) ?>"
    data-task-status="<?= htmlspecialchars($task['status']) ?>"
    data-task-type="<?= htmlspecialchars($task['task_type']) ?>"
    data-priority="<?= htmlspecialchars($task['priority']) ?>"
>

                <!-- Room -->
                <td>
                    <strong>
                        <?= htmlspecialchars($task['room_number']) ?>
                    </strong>
                </td>


                <!-- Task -->
                <td>
                    <?= htmlspecialchars(ucfirst($task['task_type'])) ?>
                </td>


                <!-- Assigned To -->
                <td>
                    <?= htmlspecialchars($task['assigned_to_name']) ?>
                </td>


                <!-- Priority -->
                <td>

                    <?php

                    $priority = $task['priority'];

                    $priorityLabel = match ($priority) {

                        'urgent' => 'Urgent',
                        'normal' => 'Normal',

                        default => ucfirst($priority)

                    };

                    $priorityClass = match ($priority) {

                        'urgent' => 'priority-urgent',
                        'normal' => 'priority-normal',

                        default => 'priority-normal'

                    };

                    ?>

                    <span class="priority <?= $priorityClass ?>">
                        <?= htmlspecialchars($priorityLabel) ?>
                    </span>

                </td>


                <!-- Scheduled -->
                <td>
                    <?= htmlspecialchars(
                        date('M d, Y', strtotime($task['scheduled_date']))
                    ) ?>
                </td>


                <!-- Status -->
                <td>

                    <?php

                    $status = $task['status'];

                    $statusLabel = match ($status) {

                        'pending' => 'Pending',
                        'in_progress' => 'In Progress',
                        'done' => 'Done',

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


                <!-- Action -->
                <td>

                   <a
    href="?page=manage-task&id=<?= $task['id'] ?>"
    class="action-button"
>
    Manage
</a>

                </td>

            </tr>

        <?php endforeach; ?>

    <?php else: ?>

        <tr>

            <td colspan="7">
                No housekeeping tasks found.
            </td>

        </tr>

    <?php endif; ?>

</tbody>
                   
                </table>

            </div>

        </section>

    </main>

</div>

<script src="js/housekeeping.js"></script>

</body>
</html>