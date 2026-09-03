<?php

/** @var array $task */
/** @var array $housekeepers */

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Manage Housekeeping Task</title>

    <link
        rel="stylesheet"
        href="css/housekeeping.css"
    >

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

            <a href="?page=dashboard" class="nav-link">
                Dashboard
            </a>

            <a href="?page=room-status" class="nav-link">
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

                <h1>Manage Housekeeping Task</h1>

                <p>
                    Update the details and status of this task.
                </p>

            </div>


   
           <div class="user-info">
   <a href="index.php?page=login" style="text-decoration: none; font-weight:bold;color:black;">Profile</a>
</div>


        </header>


        <!-- Manage Task Section -->
        <section class="dashboard-section">

            <div class="section-header">

                <div>

                    <h2>Task Information</h2>

                    <p>
                        Update the housekeeping task below.
                    </p>

                </div>

            </div>


            <form
                method="POST"
                action="?page=manage-task&id=<?= $task['id'] ?>"
            >

                <!-- Room -->
                <div class="filter-group">

                    <label>
                        Room
                    </label>

                    <input
                        type="text"
                        value="Room <?= htmlspecialchars($task['room_number']) ?>"
                        readonly
                    >

                </div>


                <!-- Assigned Housekeeper -->
                <div class="filter-group">

                    <label for="assigned_to">
                        Assign To
                    </label>

                    <select
                        id="assigned_to"
                        name="assigned_to"
                        required
                    >

                        <?php foreach ($housekeepers as $housekeeper): ?>

                            <option
                                value="<?= $housekeeper['id'] ?>"
                                <?= $housekeeper['id'] == $task['assigned_to']
                                    ? 'selected'
                                    : '' ?>
                            >
                                <?= htmlspecialchars($housekeeper['name']) ?>
                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>


                <!-- Task Type -->
                <div class="filter-group">

                    <label for="task_type">
                        Task Type
                    </label>

                    <select
                        id="task_type"
                        name="task_type"
                        required
                    >

                        <option
                            value="cleaning"
                            <?= $task['task_type'] === 'cleaning'
                                ? 'selected'
                                : '' ?>
                        >
                            Cleaning
                        </option>

                        <option
                            value="inspection"
                            <?= $task['task_type'] === 'inspection'
                                ? 'selected'
                                : '' ?>
                        >
                            Inspection
                        </option>

                        <option
                            value="maintenance"
                            <?= $task['task_type'] === 'maintenance'
                                ? 'selected'
                                : '' ?>
                        >
                            Maintenance
                        </option>

                    </select>

                </div>


                <!-- Priority -->
                <div class="filter-group">

                    <label for="priority">
                        Priority
                    </label>

                    <select
                        id="priority"
                        name="priority"
                        required
                    >

                        <option
                            value="normal"
                            <?= $task['priority'] === 'normal'
                                ? 'selected'
                                : '' ?>
                        >
                            Normal
                        </option>

                        <option
                            value="urgent"
                            <?= $task['priority'] === 'urgent'
                                ? 'selected'
                                : '' ?>
                        >
                            Urgent
                        </option>

                    </select>

                </div>


                <!-- Status -->
                <div class="filter-group">

                    <label for="status">
                        Status
                    </label>

                    <select
                        id="status"
                        name="status"
                        required
                    >

                        <option
                            value="pending"
                            <?= $task['status'] === 'pending'
                                ? 'selected'
                                : '' ?>
                        >
                            Pending
                        </option>

                        <option
                            value="in_progress"
                            <?= $task['status'] === 'in_progress'
                                ? 'selected'
                                : '' ?>
                        >
                            In Progress
                        </option>

                        <option
                            value="done"
                            <?= $task['status'] === 'done'
                                ? 'selected'
                                : '' ?>
                        >
                            Done
                        </option>

                    </select>

                </div>


                <!-- Scheduled Date -->
                <div class="filter-group">

                    <label for="scheduled_date">
                        Scheduled Date
                    </label>

                    <input
                        type="date"
                        id="scheduled_date"
                        name="scheduled_date"
                        value="<?= htmlspecialchars($task['scheduled_date']) ?>"
                        required
                    >

                </div>


                <!-- Buttons -->
                <div class="section-header">

                    <button
                        type="submit"
                        class="primary-button"
                    >
                        Save Changes
                    </button>


                    <a
                        href="?page=tasks"
                        class="action-button"
                    >
                        Cancel
                    </a>

                </div>

            </form>

       <form
    method="POST"
    action="?page=manage-task&id=<?= $task['id'] ?>"
    onsubmit="return confirm('Are you sure you want to remove this task?');"
>

    <input
        type="hidden"
        name="action"
        value="delete"
    >

    <button
        type="submit"
        class="action-button"
    >
        Remove Task
    </button>

</form>

</section>

        </section>

    </main>

</div>

</body>

</html>