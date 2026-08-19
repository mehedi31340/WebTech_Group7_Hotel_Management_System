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

                <a href="#" class="nav-link active">
                    Dashboard
                </a>

                <a href="#" class="nav-link">
                    Room Status
                </a>

                <a href="#" class="nav-link">
                    Housekeeping Tasks
                </a>

                <a href="#" class="nav-link">
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
                    <p>Monitor room readiness and housekeeping activities.</p>
                </div>

                <div class="user-info">
                    <span>Housekeeping Supervisor</span>
                </div>

            </header>


            <!-- Dashboard Summary Cards -->
            <section class="summary-cards">

                <div class="summary-card">

                    <div class="card-content">
                        <p>Dirty Rooms</p>
                        <h2>8</h2>
                    </div>

                </div>


                <div class="summary-card">

                    <div class="card-content">
                        <p>Pending Inspection</p>
                        <h2>3</h2>
                    </div>

                </div>


                <div class="summary-card">

                    <div class="card-content">
                        <p>Open Maintenance</p>
                        <h2>2</h2>
                    </div>

                </div>


                <div class="summary-card">

                    <div class="card-content">
                        <p>Completed Today</p>
                        <h2>12</h2>
                    </div>

                </div>

            </section>


            <!-- Room Status -->
            <section class="dashboard-section">

                <div class="section-header">

                    <div>
                        <h2>Room Status Overview</h2>
                        <p>Current status of hotel rooms.</p>
                    </div>

                    <a href="#" class="view-all">
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

                            <tr>
                                <td>101</td>
                                <td>1</td>
                                <td>Deluxe</td>

                                <td>
                                    <span class="status status-dirty">
                                        Dirty
                                    </span>
                                </td>
                            </tr>


                            <tr>
                                <td>102</td>
                                <td>1</td>
                                <td>Standard</td>

                                <td>
                                    <span class="status status-ready">
                                        Ready
                                    </span>
                                </td>
                            </tr>


                            <tr>
                                <td>201</td>
                                <td>2</td>
                                <td>Suite</td>

                                <td>
                                    <span class="status status-inspection">
                                        Inspection
                                    </span>
                                </td>
                            </tr>


                            <tr>
                                <td>202</td>
                                <td>2</td>
                                <td>Deluxe</td>

                                <td>
                                    <span class="status status-cleaning">
                                        Cleaning
                                    </span>
                                </td>
                            </tr>

                        </tbody>

                    </table>

                </div>

            </section>


            <!-- Recent Tasks -->
            <section class="dashboard-section">

                <div class="section-header">

                    <div>
                        <h2>Recent Housekeeping Tasks</h2>
                        <p>Latest housekeeping activities.</p>
                    </div>

                    <a href="#" class="view-all">
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

                            <tr>
                                <td>101</td>
                                <td>Room Cleaning</td>
                                <td>Housekeeper 01</td>

                                <td>
                                    <span class="priority priority-high">
                                        High
                                    </span>
                                </td>

                                <td>
                                    <span class="status status-cleaning">
                                        In Progress
                                    </span>
                                </td>
                            </tr>


                            <tr>
                                <td>201</td>
                                <td>Deep Cleaning</td>
                                <td>Housekeeper 03</td>

                                <td>
                                    <span class="priority priority-medium">
                                        Medium
                                    </span>
                                </td>

                                <td>
                                    <span class="status status-ready">
                                        Completed
                                    </span>
                                </td>
                            </tr>


                            <tr>
                                <td>202</td>
                                <td>Bathroom Cleaning</td>
                                <td>Housekeeper 02</td>

                                <td>
                                    <span class="priority priority-low">
                                        Low
                                    </span>
                                </td>

                                <td>
                                    <span class="status status-dirty">
                                        Pending
                                    </span>
                                </td>
                            </tr>

                        </tbody>

                    </table>

                </div>

            </section>

        </main>

    </div>

</body>
</html>