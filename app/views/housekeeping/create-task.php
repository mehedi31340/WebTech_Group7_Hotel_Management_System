<?php

/** @var array $rooms */
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

    <title>Create Housekeeping Task</title>

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

                <h1>Create Housekeeping Task</h1>

                <p>
                    Assign a new housekeeping activity to a housekeeper.
                </p>

            </div>

 
            <div class="user-info">
   <a href="index.php?page=login" style="text-decoration: none; font-weight:bold;color:black;">Profile</a>
</div>
 </header>


        <!-- Create Task Section -->
        <section class="dashboard-section">

            <div class="section-header">

                <div>

                    <h2>Task Information</h2>

                    <p>
                        Enter the details for the new housekeeping task.
                    </p>

                </div>

            </div>


            <form
                method="POST"
                action="?page=create-task"
            >

                <!-- Room -->
                <div class="filter-group">

                    <label for="room_id">
                        Room
                    </label>

                    <select
                        id="room_id"
                        name="room_id"
                        required
                    >

                        <option value="">
                            Select Room
                        </option>

                        <?php foreach ($rooms as $room): ?>

                            <option
                                value="<?= $room['id'] ?>"
                            >
                                Room <?= htmlspecialchars($room['room_number']) ?>
                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>


                <!-- Housekeeper -->
                <div class="filter-group">

                    <label for="assigned_to">
                        Assign To
                    </label>

                    <select
                        id="assigned_to"
                        name="assigned_to"
                        required
                    >

                        <option value="">
                            Select Housekeeper
                        </option>

                        <?php foreach ($housekeepers as $housekeeper): ?>

                            <option
                                value="<?= $housekeeper['id'] ?>"
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

                        <option value="">
                            Select Task Type
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

                        <option value="">
                            Select Priority
                        </option>

                        <option value="normal">
                            Normal
                        </option>

                        <option value="urgent">
                            Urgent
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
                        required
                    >

                </div>


                <!-- Buttons -->
                <div class="section-header">

                    <button
                        type="submit"
                        class="primary-button"
                    >
                        Create Task
                    </button>


                    <a
                        href="?page=tasks"
                        class="action-button"
                    >
                        Cancel
                    </a>

                </div>

            </form>

        </section>

    </main>

</div>

</body>

</html>