<?php

// Database connection settings
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "vehicledb";

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die(json_encode(["error" => "Database connection failed: " . $conn->connect_error]));
}

// Fetch data from the 'vehicle_info' table, including the vehicle_id
$sql = "SELECT id, vehicle_images, current_location, destination, seats_available, vehicle_type FROM vehicle_info";
$result = $conn->query($sql);

// Prepare the response array
$vehicles = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $vehicles[] = [
            "id" => (int)$row["id"], // Fetch vehicle ID
            "image" => $row["vehicle_images"],
            "location" => $row["current_location"],
            "destination" => $row["destination"],
            "seats" => (int)$row["seats_available"],
            "type" => $row["vehicle_type"]
        ];
    }
} else {
    $vehicles = ["message" => "No vehicles found."];
}

// Return data as JSON
header('Content-Type: application/json');
echo json_encode($vehicles);

// Close the connection
$conn->close();

?>
