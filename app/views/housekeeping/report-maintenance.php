<?php

/** @var array $rooms */
/** @var array $housekeepers */

?>

<!DOCTYPE html>

<html lang="en">

<head>

```
<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0"
>

<title>Report Maintenance Issue</title>

<link
    rel="stylesheet"
    href="css/housekeeping.css"
>
```

</head>

<body>

<div class="dashboard">

```
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

        <a href="?page=tasks" class="nav-link">
            Housekeeping Tasks
        </a>

        <a href="?page=maintenance" class="nav-link active">
            Maintenance
        </a>

    </nav>

</aside>


<!-- Main Content -->

<main class="main-content">


    <!-- Header -->

    <header class="topbar">

        <div>

            <h1>Report Maintenance Issue</h1>

            <p>
                Record a maintenance problem reported by a housekeeper.
            </p>

        </div>


      
          <div class="user-info">
   <a href="index.php?page=login" style="text-decoration: none; font-weight:bold;color:black;">Profile</a>
</div>


    </header>


    <!-- Maintenance Issue Section -->

    <section class="dashboard-section">


        <div class="section-header">

            <div>

                <h2>Maintenance Issue Details</h2>

                <p>
                    Enter the details of the reported maintenance problem.
                </p>

            </div>

        </div>


        <form
            method="POST"
            action="?page=report-maintenance"
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


            <!-- Reported By -->

            <div class="filter-group">

                <label for="reported_by">
                    Reported By
                </label>

                <select
                    id="reported_by"
                    name="reported_by"
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


            <!-- Description -->

            <div class="filter-group">

                <label for="description">
                    Description
                </label>

                <textarea
                    id="description"
                    name="description"
                    rows="5"
                    placeholder="Describe the maintenance problem..."
                    required
                ></textarea>

            </div>


            <!-- Notes -->

            <div class="filter-group">

                <label for="notes">
                    Notes
                </label>

                <textarea
                    id="notes"
                    name="notes"
                    rows="4"
                    placeholder="Add any additional notes..."
                ></textarea>

            </div>


            <!-- Severity -->

            <div class="filter-group">

                <label for="severity">
                    Severity
                </label>

                <select
                    id="severity"
                    name="severity"
                    required
                >

                    <option value="">
                        Select Severity
                    </option>

                    <option value="low">
                        Low
                    </option>

                    <option value="medium">
                        Medium
                    </option>

                    <option value="high">
                        High
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

                    <option value="open">
                        Open
                    </option>

                    <option value="in_progress">
                        In Progress
                    </option>

                    <option value="resolved">
                        Resolved
                    </option>

                </select>

            </div>


            <!-- Buttons -->

            <div class="section-header">

                <button
                    type="submit"
                    class="primary-button"
                >
                    Report Issue
                </button>


                <a
                    href="?page=maintenance"
                    class="action-button"
                >
                    Cancel
                </a>

            </div>


        </form>


    </section>


</main>
```

</div>

</body>

</html>
