<?php
// Database connection settings
$host = "localhost";
$username = "root"; // Your database username
$password = ""; // Your database password
$dbname = "vehicledb"; // Your database name

// Create connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $vehicle_no = $_POST['vehicle_no'];
    $owner_name = $_POST['owner_name'];
    $current_location = $_POST['current_location'];
    $destination = $_POST['destination'];
    $seats_available = $_POST['seats_available'];
    $vehicle_type = $_POST['vehicle_type'];

    // Handle file uploads
    $uploaded_images = [];
    $target_dir = "uploads/";

    // Ensure 'uploads' directory exists
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0755, true);
    }

    if (isset($_FILES['vehicle_images']) && is_array($_FILES['vehicle_images']['name'])) {
        foreach ($_FILES['vehicle_images']['name'] as $key => $image_name) {
            if (!empty($image_name)) {
                $target_file = $target_dir . basename($image_name);
                $image_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
                $uploadOk = 1;

                // Check if the file is an image
                if (getimagesize($_FILES['vehicle_images']['tmp_name'][$key]) === false) {
                    echo "File is not an image.<br>";
                    $uploadOk = 0;
                }

                // Check file size (limit: 5MB)
                if ($_FILES['vehicle_images']['size'][$key] > 5000000) {
                    echo "File $image_name is too large.<br>";
                    $uploadOk = 0;
                }

                // Allow only certain file formats
                if (!in_array($image_type, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                    echo "File $image_name has an invalid format.<br>";
                    $uploadOk = 0;
                }

                // Upload file if valid
                if ($uploadOk == 1) {
                    if (move_uploaded_file($_FILES['vehicle_images']['tmp_name'][$key], $target_file)) {
                        $uploaded_images[] = $target_file;
                    } else {
                        echo "Error uploading file: $image_name<br>";
                    }
                }
            }
        }
    } else {
        echo "No files uploaded.<br>";
    }

    // Store the image paths in a database-friendly format (comma-separated)
    $images = implode(",", $uploaded_images);

    // Prepare SQL query to insert data into the database
    $sql = "INSERT INTO vehicle_info (vehicle_no, owner_name, current_location, destination, seats_available, vehicle_type, vehicle_images) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";

    // Prepare statement
    if ($stmt = $conn->prepare($sql)) {
        // Bind parameters
        $stmt->bind_param("ssssiss", $vehicle_no, $owner_name, $current_location, $destination, $seats_available, $vehicle_type, $images);

        // Execute the query
        if ($stmt->execute()) {
            echo '<div id="successMessage" style="position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background-color: #4CAF50; color: white; padding: 20px; border-radius: 5px; text-align: center;">
                    Data successfully uploaded!
                  </div>';
            echo "<script>
                    setTimeout(function() {
                        window.location.href = 'index.html';
                    }, 1500);
                  </script>";
        } else {
            echo "Error: " . $stmt->error . "<br>";
        }

        // Close the statement
        $stmt->close();

    } else {
        echo "Error preparing statement: " . $conn->error . "<br>";
    }

    // Close the database connection
    $conn->close();
}
?>
