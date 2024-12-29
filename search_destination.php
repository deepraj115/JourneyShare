<?php
// Database connection
$host = "localhost";
$user = "root";
$password = "";
$dbname = "vehicledb";

$conn = new mysqli($host, $user, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get user input
$searchQuery = $_GET['query'] ?? '';

if (!empty($searchQuery)) {
    $stmt = $conn->prepare("
        SELECT vehicle_images, current_location, destination, vehicle_type, seats_available
        FROM vehicle_info
        WHERE destination LIKE ?
    ");
    $likeQuery = "%" . $searchQuery . "%";
    $stmt->bind_param("s", $likeQuery);
    $stmt->execute();
    $result = $stmt->get_result();

    $vehicles = [];
    while ($row = $result->fetch_assoc()) {
        $vehicles[] = $row;
    }

    // Return data as JSON
    echo json_encode($vehicles);
} else {
    echo json_encode([]);
}

$conn->close();
?>
