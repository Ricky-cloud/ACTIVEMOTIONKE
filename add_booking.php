<?php
include 'config.php'; // database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tour_id = $_POST['tour_id'];
    $amount = $_POST['amount'];

    // For now, set default values since no logged-in user
    $customer_id = 1; // placeholder (later use actual logged-in user)
    $booking_date = date("Y-m-d H:i:s");
    $status = "Pending";
    $payment_status = "Unpaid";
    $notes = "Direct booking without login";
    
    $sql = "INSERT INTO bookings (customer_id, tour_id, booking_date, status, amount, payment_status, notes, created_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iissdss", $customer_id, $tour_id, $booking_date, $status, $amount, $payment_status, $notes);

    if ($stmt->execute()) {
        echo "Booking successful!";
        // You could also redirect to dashboard.php
        // header("Location: dashboard.php?msg=Booking successful");
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}
?>
