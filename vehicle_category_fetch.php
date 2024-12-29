<?php
// Database connection
$servername = "localhost";
$username = "root"; // Replace with your DB username
$password = ""; // Replace with your DB password
$dbname = "vehicledb"; // Database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get category from the URL parameter
$category = $_GET['category'];

// Query to fetch vehicles of the selected category
$sql = "SELECT vehicle_images, current_location, destination, seats_available, vehicle_type 
        FROM vehicle_info WHERE vehicle_type = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $category);
$stmt->execute();
$result = $stmt->get_result();

// Check if any vehicles were found
$vehicles = [];
while ($row = $result->fetch_assoc()) {
    $vehicles[] = $row;
}

// Return vehicles as JSON
echo json_encode($vehicles);

// Close connection
$stmt->close();
$conn->close();
?>
