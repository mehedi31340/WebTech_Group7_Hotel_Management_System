<?php

/** @var array $reports */
/** @var array $maintenanceSummary */



?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Maintenance Issues</title>

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

                <h1>Maintenance Issues</h1>

               <!-- <p>
                   Preview of issues reported by housekeepers
                </p> -->

            </div>

            <div class="user-info">
   <a href="index.php?page=login" style="text-decoration: none; font-weight:bold;color:black;">Profile</a>
</div>



        </header>


        <!-- Summary Cards -->
        <section class="summary-cards">

            <div class="summary-card">

                <div class="card-content">

                    <p>Open Issues</p>

                      <h2><?= $maintenanceSummary['open_issues'] ?></h2>

                </div>

            </div>


            <div class="summary-card">

                <div class="card-content">

                    <p>In Progress</p>

                   <h2><?= $maintenanceSummary['in_progress'] ?></h2>

                </div>

            </div>


            <div class="summary-card">

                <div class="card-content">

                    <p>Resolved</p>

                   <h2><?= $maintenanceSummary['resolved'] ?></h2>

                </div>

            </div>


            <div class="summary-card">

                <div class="card-content">

                    <p>High Severity</p>

                   <h2><?= $maintenanceSummary['high_severity'] ?></h2>

                </div>

            </div>

        </section>


        <!-- Maintenance Section -->
        <section class="dashboard-section">

            <div class="section-header">

                <div>

                    <h2>Maintenance Reports</h2>

                    <p>
                        Collect reports from housekeepers
                    </p>

                </div>


                

          <a 
             href="?page=report-maintenance"
             class="action-button"
             type="button"
>
             Report Issue
            </a>

            
        

 
            </div>


            <!-- Filters -->
            <div class="room-filters">

                <div class="filter-group">

                    <label for="maintenance-status">
                        Status
                    </label>

                    <select id="maintenance-status">

                        <option value="all">
                            All Statuses
                        </option>

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


                <div class="filter-group">

                    <label for="severity">
                        Severity
                    </label>

                    <select id="severity">

                        <option value="all">
                            All Severities
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


                <div class="filter-group search-group">

                    <label for="maintenance-search">
                        Search
                    </label>

                    <input
                        type="text"
                        id="maintenance-search"
                        placeholder="Search room..."
                    >

                </div>

            </div>


            <!-- Maintenance Table -->
            <div class="table-container">

                <table id="maintenance-table">

                    <thead>

                        <tr>

                            <th>
                                Room
                            </th>

                            <th>
                                Description
                            </th>

                            <th>
                                Reported By
                            </th>

                            <th>
                                Severity
                            </th>

                            <th>
                                Reported At
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

    <?php foreach ($reports as $report): ?>

        <tr
            data-room-number="<?= htmlspecialchars($report['room_number']) ?>"
            data-status="<?= htmlspecialchars($report['status']) ?>"
            data-severity="<?= htmlspecialchars($report['severity']) ?>"
        >

            <td>
                <strong>
                    <?= htmlspecialchars($report['room_number']) ?>
                </strong>
            </td>


            <td>
                <?= htmlspecialchars($report['description']) ?>
            </td>


            <td>
                <?= htmlspecialchars($report['reported_by_name']) ?>
            </td>


            <td>

                <span
                    class="severity severity-<?= htmlspecialchars($report['severity']) ?>"
                >
                    <?= ucfirst(htmlspecialchars($report['severity'])) ?>
                </span>

            </td>


            <td>
                <?= date(
                    'M d, Y H:i',
                    strtotime($report['reported_at'])
                ) ?>
            </td>


            <td>

                  <span
        class="status
        <?=
            $report['status'] === 'open'
                ? 'status-dirty'
                : (
                    $report['status'] === 'in_progress'
                        ? 'status-cleaning'
                        : 'status-ready'
                )
        ?>"
    >

        <?= $report['status'] === 'in_progress'
            ? 'In Progress'
            : ucfirst(htmlspecialchars($report['status']))
        ?>

    </span>

            </td>


            <td>

               <a
    href="?page=manage-maintenance&id=<?= (int) $report['id'] ?>"
    class="action-button"
>
    Manage
</a>
            </td>

        </tr>

    <?php endforeach; ?>

</tbody>

                </table>

            </div>

        </section>

    </main>

</div>
<script src="js/maintenance.js"></script>
</body>
</html>