<?php
require_once 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get and sanitize form data
    $tour_name = "Green Valley Backroads Bike Tour"; // This would normally come from a hidden field
    $customer_name = htmlspecialchars($_POST['customer_name']);
    $customer_email = filter_var($_POST['customer_email'], FILTER_SANITIZE_EMAIL);
    $customer_phone = htmlspecialchars($_POST['customer_phone']);
    $departure_date = $_POST['departure_date'];
    $travelers = intval($_POST['travelers']);
    $special_requests = htmlspecialchars($_POST['special_requests']);
    $bike_package = isset($_POST['bike_package']) ? 1 : 0;
    
    // Calculate total amount (base price + bike package if selected)
    $base_price = 2500.00;
    $bike_package_price = 1500.00;
    $total_amount = $base_price * $travelers;
    
    if ($bike_package) {
        $total_amount += ($bike_package_price * $travelers); nj
    }

    try {
        // Insert booking into database
        $stmt = $pdo->prepare("INSERT INTO bookings 
                              (tour_name, customer_name, customer_email, customer_phone, 
                               departure_date, travelers, special_requests, bike_package, total_amount) 
                              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        $stmt->execute([
            $tour_name, $customer_name, $customer_email, $customer_phone,
            $departure_date, $travelers, $special_requests, $bike_package, $total_amount
        ]);
        
        // Success response
        echo json_encode([
            'success' => true, 
            'message' => 'Booking confirmed successfully! Your booking ID: ' . $pdo->lastInsertId(),
            'booking_id' => $pdo->lastInsertId()
        ]);
        
    } catch (PDOException $e) {
        // Error response
        echo json_encode([
            'success' => false, 
            'message' => 'Error processing booking: ' . $e->getMessage()
        ]);
    }
} else {
    // Invalid request method
    echo json_encode([
        'success' => false, 
        'message' => 'Invalid request method'
    ]);
}
?>