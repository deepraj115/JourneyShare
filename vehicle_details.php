<?php
// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "vehicledb";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch all vehicles for the sidebar
$vehicles = [];
$sql = "SELECT id, vehicle_images, vehicle_type, current_location, destination, vehicle_no, owner_name FROM vehicle_info";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $vehicles[] = $row;
    }
}

// Close the connection
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Advanced Screen Layout</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: Arial, sans-serif;
      display: flex;
      height: 100vh;
      background-color: #f4f4f4;
    }

    .container {
      display: flex;
      width: 100%;
    }

    .sidebar {
      width: 200px;
      background-color: #333;
      padding: 20px;
      color: #fff;
      height: 100vh;
    }

    .image-list {
      display: flex;
      flex-direction: column;
      gap: 10px;
    }

    .sidebar-image {
      width: 100%;
      height: auto;
      cursor: pointer;
      transition: transform 0.3s ease;
    }

    .sidebar-image:hover {
      transform: scale(1.1);
    }

    .image-grid {
      flex-grow: 1;
      display: flex;
      justify-content: center;
      align-items: center;
      background-color: #fff;
      padding: 20px;
    }

    .zoomed-image img {
      max-width: 100%;
      max-height: 100%;
      transition: transform 0.3s ease;
    }

    .data-screen {
      width: 550px;
      background-color: #fff;
      padding: 20px;
      overflow-y: auto;
      height: 100vh;
    }

    .data-container {
      display: flex;
      flex-direction: column;
      gap: 15px;
    }

    .data-item {
      font-size: 16px;
    }

    h2 {
      margin-bottom: 20px;
      font-size: 24px;
    }
  </style>
</head>
<body>
  <div class="container">
    <!-- Sidebar with images -->
    <div class="sidebar">
      <div class="image-list">
        <?php foreach ($vehicles as $vehicle): ?>
          <img src="<?php echo $vehicle['vehicle_images']; ?>" 
               alt="Vehicle Image" 
               class="sidebar-image" 
               data-id="<?php echo $vehicle['id']; ?>" 
               data-type="<?php echo $vehicle['vehicle_type']; ?>" 
               data-location="<?php echo $vehicle['current_location']; ?>" 
               data-destination="<?php echo $vehicle['destination']; ?>" 
               data-owner="<?php echo $vehicle['owner_name']; ?>">
        <?php endforeach; ?>
      </div>
    </div>

    <!-- Image Grid View -->
    <div class="image-grid">
      <div class="zoomed-image">
        <img src="<?php echo $vehicles[0]['vehicle_images'] ?? ''; ?>" alt="Zoomed Image" id="zoomed-image">
      </div>
    </div>

    <!-- Scrollable Data Screen -->
    <div class="data-screen">
      <div class="data-container">
        <h2>Vehicle Information</h2>
        <div class="data-item" id="vehicle-type">
          <strong>Vehicle Type:</strong> <?php echo $vehicles[0]['vehicle_type'] ?? ''; ?>
        </div>
        <div class="data-item" id="current-location">
          <strong>Current Location:</strong> <?php echo $vehicles[0]['current_location'] ?? ''; ?>
        </div>
        <div class="data-item" id="destination">
          <strong>Destination:</strong> <?php echo $vehicles[0]['destination'] ?? ''; ?>
        </div>
        <div class="data-item" id="owner-name">
          <strong>Owner Name:</strong> <?php echo $vehicles[0]['owner_name'] ?? ''; ?>
        </div>
      </div>
    </div>
  </div>

  <script>
    const sidebarImages = document.querySelectorAll('.sidebar-image');
    const zoomedImage = document.getElementById('zoomed-image');
    const vehicleType = document.getElementById('vehicle-type');
    const currentLocation = document.getElementById('current-location');
    const destination = document.getElementById('destination');
    const ownerName = document.getElementById('owner-name');

    sidebarImages.forEach(image => {
      image.addEventListener('click', () => {
        // Update the zoomed image
        zoomedImage.src = image.src;

        // Update vehicle information
        vehicleType.innerHTML = `<strong>Vehicle Type:</strong> ${image.dataset.type}`;
        currentLocation.innerHTML = `<strong>Current Location:</strong> ${image.dataset.location}`;
        destination.innerHTML = `<strong>Destination:</strong> ${image.dataset.destination}`;
        ownerName.innerHTML = `<strong>Owner Name:</strong> ${image.dataset.owner}`;
      });
    });
  </script>
</body>
</html>
