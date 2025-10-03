<?php
// 1. Start session and connect to database
require_once 'config.php';

// 2. Get dashboard statistics from database
$stats = $conn->query("
    SELECT 
        (SELECT COUNT(*) FROM bookings) AS total_bookings,
        (SELECT SUM(tours.price) FROM bookings JOIN tours ON bookings.tour_id = tours.id) AS total_revenue,
        (SELECT COUNT(*) FROM tours WHERE status = 'active') AS active_tours,
        (SELECT COUNT(*) FROM customers) AS total_customers
")->fetch_assoc();

// 3. Get recent bookings (last 5)
$recent_bookings = $conn->query("
    SELECT bookings.id, bookings.booking_date, bookings.status,
           customers.name AS customer_name,
           tours.title AS tour_title
    FROM bookings
    JOIN customers ON bookings.customer_id = customers.id
    JOIN tours ON bookings.tour_id = tours.id
    ORDER BY bookings.booking_date DESC
    LIMIT 5
");

// 4. Get top tours (by booking count)
$popular_tours = $conn->query("
    SELECT tours.title, COUNT(bookings.id) AS booking_count
    FROM tours
    LEFT JOIN bookings ON tours.id = bookings.tour_id
    GROUP BY tours.id
    ORDER BY booking_count DESC
    LIMIT 3
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Travel Admin Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --sidebar-width: 250px;
            --topbar-height: 70px;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
        }
        .sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            position: fixed;
            background: linear-gradient(180deg, #1e293b 0%, #0f172a 100%);
            color: white;
        }
        .main-content {
            margin-left: var(--sidebar-width);
            padding: 20px;
        }
        .dashboard-card {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: transform 0.2s;
        }
        .dashboard-card:hover {
            transform: translateY(-5px);
        }
        @media (max-width: 992px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .sidebar.active {
                transform: translateX(0);
            }
            .main-content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <!-- Sidebar Navigation -->
        <div class="sidebar" id="sidebar">
            <div class="sidebar-header p-4">
                <h4 class="text-white">
                    <i class="fas fa-plane me-2"></i> Active Motion Kenya
                </h4>
            </div>
            <ul class="nav flex-column px-3">
                <li class="nav-item">
                    <a href="dashboard.php" class="nav-link active">
                        <i class="fas fa-home me-2"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a href="bookings.php" class="nav-link">
                        <i class="fas fa-calendar-alt me-2"></i> Bookings
                    </a>
                </li>
                <li class="nav-item">
                    <a href="tours.php" class="nav-link">
                        <i class="fas fa-map-marked-alt me-2"></i> Tours
                    </a>
                </li>
                <li class="nav-item">
                    <a href="customers.php" class="nav-link">
                        <i class="fas fa-users me-2"></i> Customers
                    </a>
                </li>
            </ul>
        </div>

        <!-- Main Content Area -->
        <div class="main-content" id="mainContent">
            <!-- Top Navigation Bar -->
            <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm mb-4">
                <div class="container-fluid">
                    <button class="btn btn-link d-lg-none" id="sidebarToggle">
                        <i class="fas fa-bars"></i>
                    </button>
                    <div class="d-flex ms-auto align-items-center">
                        <div class="dropdown me-3">
                            <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                                <i class="fas fa-user-circle me-1"></i> Admin
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="#">Profile</a></li>
                                <li><a class="dropdown-item" href="#">Settings</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Dashboard Content -->
            <div class="container-fluid">
                <!-- Statistics Cards Row -->
                <div class="row mb-4">
                    <!-- Total Bookings Card -->
                    <div class="col-md-6 col-lg-3 mb-4">
                        <div class="dashboard-card card bg-white h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6 class="text-muted">Total Bookings</h6>
                                        <h3><?= number_format($stats['total_bookings']) ?></h3>
                                        <span class="text-success small">
                                            <i class="fas fa-arrow-up me-1"></i> 12% from last month
                                        </span>
                                    </div>
                                    <div class="icon bg-primary bg-opacity-10 text-primary p-3 rounded">
                                        <i class="fas fa-calendar-check"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Revenue Card -->
                    <div class="col-md-6 col-lg-3 mb-4">
                        <div class="dashboard-card card bg-white h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6 class="text-muted">Total Revenue</h6>
                                        <h3>$<?= number_format($stats['total_revenue'] ?? 0, 2) ?></h3>
                                        <span class="text-success small">
                                            <i class="fas fa-arrow-up me-1"></i> 18% from last month
                                        </span>
                                    </div>
                                    <div class="icon bg-success bg-opacity-10 text-success p-3 rounded">
                                        <i class="fas fa-dollar-sign"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Active Tours Card -->
                    <div class="col-md-6 col-lg-3 mb-4">
                        <div class="dashboard-card card bg-white h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6 class="text-muted">Active Tours</h6>
                                        <h3><?= number_format($stats['active_tours']) ?></h3>
                                        <span class="text-success small">
                                            <i class="fas fa-arrow-up me-1"></i> 3 new this month
                                        </span>
                                    </div>
                                    <div class="icon bg-warning bg-opacity-10 text-warning p-3 rounded">
                                        <i class="fas fa-map-marked-alt"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Customers Card -->
                    <div class="col-md-6 col-lg-3 mb-4">
                        <div class="dashboard-card card bg-white h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6 class="text-muted">Customers</h6>
                                        <h3><?= number_format($stats['total_customers']) ?></h3>
                                        <span class="text-success small">
                                            <i class="fas fa-arrow-up me-1"></i> 24 new this week
                                        </span>
                                    </div>
                                    <div class="icon bg-info bg-opacity-10 text-info p-3 rounded">
                                        <i class="fas fa-users"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Content Row -->
                <div class="row">
                    <!-- Recent Bookings Table -->
                    <div class="col-lg-8">
                        <div class="dashboard-card card bg-white h-100">
                            <div class="card-header bg-white border-0">
                                <h5>Recent Bookings</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Booking ID</th>
                                                <th>Customer</th>
                                                <th>Tour</th>
                                                <th>Date</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php while ($booking = $recent_bookings->fetch_assoc()): ?>
                                            <tr>
                                                <td>#<?= $booking['id'] ?></td>
                                                <td><?= htmlspecialchars($booking['customer_name']) ?></td>
                                                <td><?= htmlspecialchars($booking['tour_title']) ?></td>
                                                <td><?= date('M j, Y', strtotime($booking['booking_date'])) ?></td>
                                                <td>
                                                    <span class="badge rounded-pill <?= 
                                                        $booking['status'] == 'confirmed' ? 'bg-success' : 
                                                        ($booking['status'] == 'pending' ? 'bg-warning' : 'bg-danger')
                                                    ?>">
                                                        <?= ucfirst($booking['status']) ?>
                                                    </span>
                                                </td>
                                            </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Quick Actions & Popular Tours -->
                    <div class="col-lg-4">
                        <!-- Quick Actions Card -->
                        <div class="dashboard-card card bg-white mb-4">
                            <div class="card-header bg-white border-0">
                                <h5>Quick Actions</h5>
                            </div>
                            <div class="card-body">
                                <div class="d-grid gap-2">
                                    <a href="add_booking.php" class="btn btn-primary">
                                        <i class="fas fa-plus me-2"></i> Add New Booking
                                    </a>
                                    <a href="tours.php" class="btn btn-outline-primary">
                                        <i class="fas fa-map me-2"></i> Manage Tours
                                    </a>
                                    <a href="customers.php" class="btn btn-outline-primary">
                                        <i class="fas fa-users me-2"></i> View Customers
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Popular Tours Card -->
                        <div class="dashboard-card card bg-white">
                            <div class="card-header bg-white border-0">
                                <h5>Popular Tours</h5>
                            </div>
                            <div class="card-body">
                                <ul class="list-group">
                                    <?php while ($tour = $popular_tours->fetch_assoc()): ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <?= htmlspecialchars($tour['title']) ?>
                                        <span class="badge bg-primary rounded-pill">
                                            <?= $tour['booking_count'] ?> bookings
                                        </span>
                                    </li>
                                    <?php endwhile; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle sidebar on mobile
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('active');
        });
    </script>
</body>
</html>my own dashboard?$_COOKIE