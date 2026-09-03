<?php

/** @var array $report */

?>

<!DOCTYPE html>

<html lang="en">

<head>


<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0"
>

<title>Manage Maintenance Report</title>

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

            <h1>Manage Maintenance Report</h1>

            <p>
                Review and update the maintenance issue.
            </p>

        </div>


     
           <div class="user-info">
   <a href="index.php?page=login" style="text-decoration: none; font-weight:bold;color:black;">Profile</a>
</div>


    </header>


    <!-- Report Section -->

    <section class="dashboard-section">


        <div class="section-header">

            <div>

                <h2>
                    Room <?= htmlspecialchars($report['room_number']) ?>
                </h2>

                <p>
                    Maintenance report details
                </p>

            </div>

        </div>


        <!-- Edit Report Form -->

        <form
            method="POST"
            action="?page=manage-maintenance&id=<?= (int) $report['id'] ?>"
        >


            <!-- Room -->

            <div class="filter-group">

                <label for="room">
                    Room
                </label>

                <input
                    type="text"
                    id="room"
                    value="Room <?= htmlspecialchars($report['room_number']) ?>"
                    readonly
                >

            </div>


            <!-- Reported By -->

            <div class="filter-group">

                <label for="reported_by">
                    Reported By
                </label>

                <input
                    type="text"
                    id="reported_by"
                    value="<?= htmlspecialchars($report['reported_by_name']) ?>"
                    readonly
                >

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
                    required
                ><?= htmlspecialchars($report['description']) ?></textarea>

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
                ><?= htmlspecialchars($report['notes'] ?? '') ?></textarea>

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

                    <option
                        value="low"
                        <?= $report['severity'] === 'low'
                            ? 'selected'
                            : '' ?>
                    >
                        Low
                    </option>

                    <option
                        value="medium"
                        <?= $report['severity'] === 'medium'
                            ? 'selected'
                            : '' ?>
                    >
                        Medium
                    </option>

                    <option
                        value="high"
                        <?= $report['severity'] === 'high'
                            ? 'selected'
                            : '' ?>
                    >
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

                    <option
                        value="open"
                        <?= $report['status'] === 'open'
                            ? 'selected'
                            : '' ?>
                    >
                        Open
                    </option>

                    <option
                        value="in_progress"
                        <?= $report['status'] === 'in_progress'
                            ? 'selected'
                            : '' ?>
                    >
                        In Progress
                    </option>

                    <option
                        value="resolved"
                        <?= $report['status'] === 'resolved'
                            ? 'selected'
                            : '' ?>
                    >
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
                    Save Changes
                </button>


                <a
                    href="?page=maintenance"
                    class="action-button"
                >
                    Cancel
                </a>

            </div>


        </form>


        <!-- Remove Report -->

        <div class="danger-zone">

            <h2>Remove Report</h2>

            <p>
                Removing this maintenance report cannot be undone.
            </p>


            <form
                method="POST"
                action="?page=manage-maintenance&id=<?= (int) $report['id'] ?>"
                onsubmit="return confirm('Are you sure you want to remove this maintenance report?');"
            >

                <input
                    type="hidden"
                    name="action"
                    value="delete"
                >

                <button
                    type="submit"
                    class="danger-button"
                >
                    Remove Report
                </button>

            </form>

        </div>


    </section>


</main>


</div>

</body>

</html>
