<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Hotel Management Portal</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f4f6f8;
        }

        .navbar {
            background: #1f2937;
            padding: 18px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar h2 {
            color: white;
        }

        .navbar a {
            color: white;
            text-decoration: none;
            margin-left: 25px;
            font-size: 16px;
        }

        .navbar a:hover {
            color: #60a5fa;
        }

        .hero {
            text-align: center;
            padding: 100px 20px;
        }

        .hero h1 {
            font-size: 42px;
            margin-bottom: 15px;
            color: #1f2937;
        }

        .hero p {
            font-size: 18px;
            color: #6b7280;
            margin-bottom: 40px;
        }

        .modules {
            display: flex;
            justify-content: center;
            gap: 30px;
            flex-wrap: wrap;
           
        }

        .module {
            background: white;
            width: 280px;
            padding: 35px 25px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            position: relative;
        }

        .module h3 {
            margin-bottom: 15px;
            color: #1f2937;
        }

      .module p { 
    color: #6b7280; 
    width: 230px;
    height: 83.64px;
    margin-bottom: 25px;
}

        .module a {
            display: inline-block;
            padding: 12px 22px;
            background: #2563eb;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            position: absolute;
            bottom: 25px;
            right: 100px;

        }

        .module a:hover {
            background: #1d4ed8;
        }
    </style>
</head>

<body>

    <!-- <nav class="navbar">

        <h2>Hotel Management Portal</h2>

        <div>
            <a href="index.php">Home</a>

            <a href="/Group_7_Hotel-Management-System/public/index.php?page=login">
                Housekeeping Supervisor
            </a>

            <a href="/hotel-system/public/index.php?page=login">
                Guest
            </a>
        </div>

    </nav> -->


    <section class="hero">

        <h1> Hotel Management System Portal</h1>

        <p></p>


        <div class="modules">


            <!-- Guest -->

            <div class="module">

                <h3>Guest</h3>

                <p>
                    Access guest services, bookings,
                    reviews and other hotel services.



                </p>

                <p>                              </p>

                <a href="/hotel-system/public/index.php?page=login">
                    Login
                </a>

            </div>



          <div class="module">

                <h3>Admin</h3>

                <p>
                
                Manage hotel operations, rooms, bookings, users, reviews, and financial reports.
                <p>                              </p>

                <a href="/admin_project1_final/admin_project1/dashboard/admin/dashboard.php">
                    Login
                </a>

            </div>


              <!-- Housekeeping Supervisor -->

            <div class="module">

                <h3>Housekeeping Supervisor</h3>

                <p>
                    Manage housekeeping tasks, room status,
                    maintenance issues and inspections.
                </p>

                <a href="/Group_7_Hotel-Management-System/public/index.php?page=login">
                    Login
                </a>

            </div>


                  <div class="module">

                <h3>Receptionist</h3>

                <p>
                    Manage and Run front-desk operations — check-in/out, walk-ins, payments, and daily queues.
                   
                </p>

                <a href="/hotel-receptionist11/View/dashboard.php">
                    Login
                </a>

            </div>
            

        </div>

    </section>

</body>
</html>