<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $customer_id = $_POST['customer_id'];
    $tour_id = $_POST['tour_id'];
    $booking_date = $_POST['booking_date'];

    $sql = "INSERT INTO bookings (customer_id, tour_id, booking_date) 
            VALUES ('$customer_id', '$tour_id', '$booking_date')";

    if ($conn->query($sql) === TRUE) {
        header("Location: dashboard.php"); // Redirect back to dashboard
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>