<?php

/** @var array $room */ ?>


<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Manage Room</title>

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

            <a
                href="?page=dashboard"
                class="nav-link"
            >
                Dashboard
            </a>

            <a
                href="?page=room-status"
                class="nav-link active"
            >
                Room Status
            </a>

            <a
                href="?page=tasks"
                class="nav-link"
            >
                Housekeeping Tasks
            </a>

            <a
                href="?page=maintenance"
                class="nav-link"
            >
                Maintenance
            </a>

        </nav>

    </aside>


    <!-- Main Content -->
    <main class="main-content">

        <header class="topbar">

            <div>

                <h1>
                    Manage Room
                </h1>

                <p>
                    Update room status and housekeeping notes.
                </p>

            </div>


           <div class="user-info">
   <a href="index.php?page=login" style="text-decoration: none; font-weight:bold;color:black;">Profile</a>
</div>


        </header>


        <!-- Room Information -->

        <section class="dashboard-section">

            <div class="section-header">

                <div>

                    <h2>
                        Room <?= htmlspecialchars($room['room_number']) ?>
                    </h2>

                    <p>
                        Manage the current condition of this room.
                    </p>

                </div>

            </div>


            <!-- Room Details -->

            <div class="room-details-grid">

                <div class="detail-card">

                    <span class="detail-label">
                        Room Number
                    </span>

                    <strong>
                        <?= htmlspecialchars($room['room_number']) ?>
                    </strong>

                </div>


                <div class="detail-card">

                    <span class="detail-label">
                        Room Type
                    </span>

                    <strong>
                        <?= htmlspecialchars($room['room_type']) ?>
                    </strong>

                </div>


                <div class="detail-card">

                    <span class="detail-label">
                        Floor
                    </span>

                    <strong>
                        <?= htmlspecialchars($room['floor']) ?>
                    </strong>

                </div>


                <div class="detail-card">

                    <span class="detail-label">
                        Current Status
                    </span>

                    <strong>
                        <?= htmlspecialchars(ucwords(str_replace('_', ' ', $room['status']))) ?>
                    </strong>

                </div>

            </div>


            <!-- Update Form -->

            <form
                method="POST"
                action="?page=update-room"
                class="room-form"
            >

                <input
                    type="hidden"
                    name="id"
                    value="<?= htmlspecialchars($room['id']) ?>"
                >


                <div class="form-group">

                    <label for="status">
                        Room Status
                    </label>

                    <select
                        name="status"
                        id="status"
                        required
                    >

                        <option
                            value="available"
                            <?= $room['status'] === 'available' ? 'selected' : '' ?>
                        >
                            Available
                        </option>

                        <option
                            value="occupied"
                            <?= $room['status'] === 'occupied' ? 'selected' : '' ?>
                        >
                            Occupied
                        </option>

                        <option
                            value="dirty"
                            <?= $room['status'] === 'dirty' ? 'selected' : '' ?>
                        >
                            Dirty
                        </option>

                        <option
                            value="in_progress"
                            <?= $room['status'] === 'in_progress' ? 'selected' : '' ?>
                        >
                            Cleaning
                        </option>

                        <option
                            value="maintenance"
                            <?= $room['status'] === 'maintenance' ? 'selected' : '' ?>
                        >
                            Maintenance
                        </option>

                        <option
                            value="blocked"
                            <?= $room['status'] === 'blocked' ? 'selected' : '' ?>
                        >
                            Blocked
                        </option>

                        <option
                           value="in_progress"
                      <?= $room['status'] === 'in_progress' ? 'selected' : '' ?>
                      >
                         In Progress
                       </option>

                    </select>

                </div>


                <div class="form-group">

                    <label for="notes">
                        Housekeeping Notes
                    </label>

                    <textarea
                        name="notes"
                        id="notes"
                        rows="5"
                        placeholder="Enter notes about the room..."
                    ><?= htmlspecialchars($room['notes'] ?? '') ?></textarea>

                </div>


                <div class="form-actions">

                    <a
                        href="?page=room-status"
                        class="secondary-button"
                    >
                        Cancel
                    </a>

                    <button
                        type="submit"
                        class="primary-button"
                    >
                        Save Changes
                    </button>

                </div>

            </form>

        </section>

    </main>

</div>

</body>
</html>