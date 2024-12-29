<?php
// Database connection
$servername = "localhost";
$username = "root"; // Default for XAMPP
$password = "";     // Default for XAMPP
$dbname = "vehicledb";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(['status' => 'error', 'message' => 'Database connection failed: ' . $conn->connect_error]));
}

// Handle different request methods
$requestMethod = $_SERVER['REQUEST_METHOD'];

if ($requestMethod === 'POST') {
    // Save data to the watchlist
    $data = json_decode(file_get_contents('php://input'), true);

    if (!empty($data)) {
        $vehicle_id = $data['id'];
        $type = $data['type'];
        $location = $data['location'];
        $destination = $data['destination'];
        $seats = $data['seats'];
        $image = $data['image'];

        // Check if the vehicle already exists in the watchlist with the same vehicle_id, location, and destination
        $checkQuery = "SELECT * FROM watchlist WHERE vehicle_id = ? AND location = ? AND destination = ?";
        $stmt = $conn->prepare($checkQuery);
        $stmt->bind_param("iss", $vehicle_id, $location, $destination); // Check vehicle_id, location, and destination
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            echo json_encode(['status' => 'error', 'message' => 'This vehicle with the same location and destination is already in your watchlist.']);
        } else {
            // Insert data into the watchlist table
            $query = "INSERT INTO watchlist (vehicle_id, type, location, destination, seats, image) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("isssis", $vehicle_id, $type, $location, $destination, $seats, $image);

            if ($stmt->execute()) {
                echo json_encode(['status' => 'success', 'message' => 'Vehicle added to watchlist.']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to add vehicle to watchlist.']);
            }
        }

        $stmt->close();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No data received.']);
    }
} elseif ($requestMethod === 'GET') {
    // Fetch data from the watchlist
    $query = "SELECT * FROM watchlist";
    $result = $conn->query($query);

    $watchlist = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $watchlist[] = $row;
        }
    }

    echo json_encode($watchlist);
} elseif ($requestMethod === 'DELETE') {
    // Remove data from the watchlist
    $data = json_decode(file_get_contents('php://input'), true);

    if (!empty($data['id'])) {
        $id = $data['id'];

        // Delete the item from the watchlist table
        $query = "DELETE FROM watchlist WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Item removed from watchlist.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to remove item from watchlist.']);
        }

        $stmt->close();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No ID provided.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}

$conn->close();
?>
