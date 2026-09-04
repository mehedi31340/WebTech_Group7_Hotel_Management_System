<?php

/** @var array $rooms */
/** @var array $statusCounts */

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Room Status</title>

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

            <a href="?page=room-status" class="nav-link active ">
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

        <!-- Header -->
        <header class="topbar">

            <div>
                <h1>Room Status</h1>

               <!-- <p>
                    Monitor and manage the current status of hotel rooms.
                </p> -->
            </div>

 
          <div class="user-info">
   <a href="index.php?page=login" style="text-decoration: none; font-weight:bold;color:black;">Profile</a>
</div>


        </header>


        <!-- Status Summary -->
        <section class="summary-cards">

            <div class="summary-card">
                <div class="card-content">
                    <p>Available</p>
                    <h2 id="count-available"><?= $statusCounts['available'] ?></h2>
                </div>
            </div>

            <div class="summary-card">
                <div class="card-content">
                    <p>Occupied</p>
                    <h2 id="count-occupied"><?= $statusCounts['occupied'] ?></h2>
                </div>
            </div>

            <div class="summary-card">
                <div class="card-content">
                    <p>Dirty</p>
                   <h2 id="count-dirty"><?= $statusCounts['dirty'] ?></h2>
                </div>
            </div>

                         <div class="summary-card">
    <div class="card-content">
        <p>Blocked</p>
        <h2 id="count-blocked"><?= $statusCounts['blocked'] ?></h2>
    </div>
</div>


            <div class="summary-card">
                <div class="card-content">
                    <p>Maintenance</p>
                   <h2 id="count-maintenance"><?= $statusCounts['maintenance'] ?></h2>
                </div>
            </div>

            

            <div class="summary-card">
                <div class="card-content">
                    <p>IN Progress</p>
                    <h2 id="count-in-progress"><?= $statusCounts['in_progress'] ?></h2>
                </div>
            </div>

        </section>


        <!-- Room Status Section -->
        <section class="dashboard-section">

            <div class="section-header">

                <div>
                    <h2>All Rooms</h2>

                  <!--  <p>
                        Current housekeeping and room conditions.
                    </p> -->
                </div>

                <button
                type="button"
                class="primary-button"
                id="refresh-status"
                >
                    Refresh Status
                </button>

            </div>


            <!-- Filters -->
            <div class="room-filters">

                <div class="filter-group">

                    <label for="floor">
                        Floor
                    </label>

                    <select id="floor" class="room filter">

                        <option value="all">
                            All Floors
                        </option>

                        <option value="1">
                            Floor 1
                        </option>

                        <option value="2">
                            Floor 2
                        </option>

                        <option value="3">
                            Floor 3
                        </option>

                    </select>

                </div>


                <div class="filter-group">

                    <label for="status">
                        Status
                    </label>

                    <select id="status" class="room-filter">

                        <option value="all">
                            All Statuses
                        </option>

                        <option value="available">
                            Available
                        </option>

                        <option value="occupied">
                            Occupied
                        </option>

                        <option value="dirty">
                            Dirty
                        </option>

                        <option value="in_progress">
                            In Progress
                        </option>

                        <option value="maintenance">
                            Maintenance
                        </option>

                        <option value="blocked">
                            Blocked
                        </option>

                    </select>

                </div>


                <div class="filter-group search-group">

                    <label for="room-search">
                        Search
                    </label>

                    <input
                        type="text"
                        id="room-search"
                        placeholder="Search room number..."
                    >

                </div>

            </div>


            <!-- Room Table -->
            <div class="table-container">

                <table id="room-table">

                    <thead>

                        <tr>

                            <th>
                                Room
                            </th>

                            <th>
                                Floor
                            </th>

                            <th>
                                Room Type
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

    <?php if (!empty($rooms)): ?>

        <?php foreach ($rooms as $room): ?>

      <tr
        data-room-number="<?= htmlspecialchars($room['room_number']) ?>"
        data-floor="<?= htmlspecialchars($room['floor']) ?>"
        data-status="<?= htmlspecialchars($room['status']) ?>"
       >
                <td>
                    <strong>
                        <?= htmlspecialchars($room['room_number']) ?>
                    </strong>
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

                <td>

                  
    <a
           href="?page=manage-room&id=<?= htmlspecialchars($room['id']) ?>"
           class="action-button"
    >
        Manage
    </a>


                </td>

            </tr>

        <?php endforeach; ?>

    <?php else: ?>

        <tr>

            <td colspan="5">
                No rooms found.
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