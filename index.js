// DOM Elements
const searchInput = document.querySelector('.search-bar input');
const productCards = document.querySelectorAll('.product');
const heroImage = document.querySelector('.hero img');

document.addEventListener('DOMContentLoaded', () => {
    const locationInfoElement = document.getElementById('location-info');
    const API_KEY = '4d88dbdedbf740a086f1ecd6f3b83bb0'; // Replace with your OpenCage or similar geocoding API key

    // Function to get the current location
    function getLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(fetchCityAndPincode, showError);
        } else {
            locationInfoElement.textContent = 'Geolocation not supported.';
        }
    }

    // Fetch city and pincode using a reverse geocoding API
    function fetchCityAndPincode(position) {
        const { latitude, longitude } = position.coords;
        const geocodeUrl = `https://api.opencagedata.com/geocode/v1/json?q=${latitude}+${longitude}&key=${API_KEY}`;

        fetch(geocodeUrl)
            .then(response => response.json())
            .then(data => {
                if (data && data.results && data.results.length > 0) {
                    const location = data.results[0].components;
                    const city = location.city || location.town || location.village || 'Unknown City';
                    const pincode = location.postcode || 'Unknown Pincode';
                    locationInfoElement.textContent = `${city}, ${pincode}`;
                } else {
                    locationInfoElement.textContent = 'Unable to fetch location.';
                }
            })
            .catch(error => {
                console.error('Error fetching location:', error);
                locationInfoElement.textContent = 'Error fetching location.';
            });
    }

    // Handle geolocation errors
    function showError(error) {
        switch (error.code) {
            case error.PERMISSION_DENIED:
                locationInfoElement.textContent = 'Permission denied.';
                break;
            case error.POSITION_UNAVAILABLE:
                locationInfoElement.textContent = 'Location unavailable.';
                break;
            case error.TIMEOUT:
                locationInfoElement.textContent = 'Request timed out.';
                break;
            case error.UNKNOWN_ERROR:
                locationInfoElement.textContent = 'An error occurred.';
                break;
        }
    }

    // Fetch location on page load
    getLocation();
});


// For search data
document.addEventListener("DOMContentLoaded", () => {
    const searchInput = document.getElementById("searchInput");
    const searchBtn = document.getElementById("searchBtn");
    const vehicleGrid = document.getElementById("vehicleGrid");

    // Function to fetch and display search results
    function fetchSearchResults(query) {
        fetch(`search_destination.php?query=${encodeURIComponent(query)}`)
            .then((response) => response.json())
            .then((data) => {
                vehicleGrid.innerHTML = ""; // Clear existing results

                if (data.length > 0) {
                    data.forEach((vehicle) => {
                        const vehicleCard = `
                            <div class="vehicle-card">
                                <img src="${vehicle.vehicle_images}" alt="${vehicle.vehicle_type}" class="vehicle-images">
                                <div class="vehicle-details">
                                    <h3>${vehicle.vehicle_type}</h3>
                                    <p><strong>Current Location:</strong> ${vehicle.current_location}</p>
                                    <p><strong>Destination:</strong> ${vehicle.destination}</p>
                                    <p><strong>Seats Available:</strong> ${vehicle.seats_available}</p>
                                </div>
                            </div>
                        `;
                        vehicleGrid.innerHTML += vehicleCard;
                    });
                } else {
                    vehicleGrid.innerHTML = "<p>No vehicles found for the given destination.</p>";
                }
            })
            .catch((error) => {
                vehicleGrid.innerHTML = "<p>Error fetching data. Please try again later.</p>";
                console.error("Error:", error);
            });
    }

    // Event listeners for search
    searchBtn.addEventListener("click", () => {
        const query = searchInput.value.trim();
        if (query) fetchSearchResults(query);
    });

    searchInput.addEventListener("keydown", (event) => {
        if (event.key === "Enter") {
            const query = searchInput.value.trim();
            if (query) fetchSearchResults(query);
        }
    });
});



// Notification Icon





// Category Filteration
// Listen for category click
document.querySelectorAll('.category').forEach(item => {
    item.addEventListener('click', function() {
        let selectedCategory = this.getAttribute('data-category');
        fetchVehiclesByCategory(selectedCategory);
    });
});

// Fetch vehicles by category
function fetchVehiclesByCategory(category) {
    fetch('vehicle_category_fetch.php?category=' + category)
        .then(response => response.json())
        .then(data => {
            let vehicleGrid = document.getElementById('vehicleGrid');
            vehicleGrid.innerHTML = ''; // Clear previous results

            if (data.length > 0) {
                data.forEach(vehicle => {
                    let vehicleItem = document.createElement('div');
                    vehicleItem.classList.add('vehicle-item');
                    vehicleItem.innerHTML = `
                        <img src="${vehicle.vehicle_images}" alt="${vehicle.vehicle_type}">
                        <p>Type: ${vehicle.vehicle_type}</p>
                        <p>Location: ${vehicle.current_location}</p>
                        <p>Destination: ${vehicle.destination}</p>
                        <p>Seats Available: ${vehicle.seats_available}</p>
                    `;
                    vehicleGrid.appendChild(vehicleItem);
                });
            } else {
                vehicleGrid.innerHTML = '<p>No vehicles found for this category.</p>';
            }
        })
        .catch(error => console.error('Error fetching vehicles:', error));
}



// Slider Banner
const slider = document.querySelector('.slider');
        const slides = document.querySelectorAll('.slide');
        const prevBtn = document.querySelector('.prev');
        const nextBtn = document.querySelector('.next');
        const indicators = document.querySelectorAll('.indicator');

        let currentIndex = 0;

        function updateSlider() {
            slider.style.transform = `translateX(-${currentIndex * 100}%)`;
            indicators.forEach((indicator, index) => {
                indicator.classList.toggle('active', index === currentIndex);
            });
        }

        function showNextSlide() {
            currentIndex = (currentIndex + 1) % slides.length;
            updateSlider();
        }

        function showPrevSlide() {
            currentIndex = (currentIndex - 1 + slides.length) % slides.length;
            updateSlider();
        }

        nextBtn.addEventListener('click', showNextSlide);
        prevBtn.addEventListener('click', showPrevSlide);

        indicators.forEach((indicator, index) => {
            indicator.addEventListener('click', () => {
                currentIndex = index;
                updateSlider();
            });
        });

        setInterval(showNextSlide, 5000); // Auto slide every 5 seconds


// Vehicle Grid View
document.addEventListener("DOMContentLoaded", function () {
    const vehicleGrid = document.getElementById("vehicleGrid");

    // Fetch data from the PHP file
    fetch("fetch_vehicle.php")
        .then((response) => response.json())
        .then((data) => {
            if (Array.isArray(data)) {
                // Populate the grid with vehicle data
                data.forEach((vehicle) => {
                    const gridItem = document.createElement("div");
                    gridItem.classList.add("grid-item");

                    gridItem.innerHTML = `
                        <a href="vehicle_details.php?id=${vehicle.id}">
                        <img src="${vehicle.image}" alt="Vehicle Image" class="vehicle-image">
                        <h3>${vehicle.type}</h3>
                        <p>From: ${vehicle.location}</p>
                        <p>To: ${vehicle.destination}</p>
                        <p>Seats Available: ${vehicle.seats}</p>
                        </a>
                        <button class="add-to-watchlist" data-id="${vehicle.id}" data-type="${vehicle.type}" data-location="${vehicle.location}" data-destination="${vehicle.destination}" data-seats="${vehicle.seats}" data-image="${vehicle.image}">
                            Add to Watchlist
                        </button>
                    `;

                    vehicleGrid.appendChild(gridItem);
                });

                // Attach event listeners to "Add to Watchlist" buttons
                const addToWatchlistButtons = document.querySelectorAll(".add-to-watchlist");
                addToWatchlistButtons.forEach((button) => {
                    button.addEventListener("click", function () {
                        const vehicle = {
                            id: this.getAttribute("data-id"),
                            type: this.getAttribute("data-type"),
                            location: this.getAttribute("data-location"),
                            destination: this.getAttribute("data-destination"),
                            seats: this.getAttribute("data-seats"),
                            image: this.getAttribute("data-image"),
                        };
                        addToWatchlist(vehicle);
                    });
                });
            } else {
                // Show a message if no vehicles are found
                vehicleGrid.innerHTML = "<p>No vehicles available at the moment.</p>";
            }
        })
        .catch((error) => {
            console.error("Error fetching vehicle data:", error);
            vehicleGrid.innerHTML = "<p>Error loading vehicles. Please try again later.</p>";
        });
});

// Function to add a vehicle to the watchlist
function addToWatchlist(vehicle) {
    fetch("watchlist.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
        },
        body: JSON.stringify(vehicle),
    })
        .then((response) => response.json())
        .then((data) => {
            if (data.status === "success") {
                alert("Vehicle added to your watchlist!");
            } else {
                alert(data.message);
            }
        })
        .catch((error) => {
            console.error("Error adding to watchlist:", error);
            alert("An error occurred. Please try again.");
        });
}




//wacthlist dynamic update
const watchlist = JSON.parse(localStorage.getItem("userWatchlist")) || [];
document.getElementById("watchlistCount").textContent = watchlist.length;
