

let map, markers = [], center;

// Function to calculate offset for random marker placement
function calculateOffset(centerLat, centerLon, distanceKm, bearingDegrees) {
    const earthRadiusKm = 6371;

    const bearingRad = (bearingDegrees * Math.PI) / 180;
    const latRad = (centerLat * Math.PI) / 180;
    const lonRad = (centerLon * Math.PI) / 180;

    const offsetLatRad = Math.asin(
        Math.sin(latRad) * Math.cos(distanceKm / earthRadiusKm) +
        Math.cos(latRad) * Math.sin(distanceKm / earthRadiusKm) * Math.cos(bearingRad)
    );

    const offsetLonRad = lonRad + Math.atan2(
        Math.sin(bearingRad) * Math.sin(distanceKm / earthRadiusKm) * Math.cos(latRad),
        Math.cos(distanceKm / earthRadiusKm) - Math.sin(latRad) * Math.sin(offsetLatRad)
    );

    return {
        lat: (offsetLatRad * 180) / Math.PI,
        lon: (offsetLonRad * 180) / Math.PI
    };
}

// Function to clear existing custom markers on the map
function clearMarkers() {
    markers.forEach(marker => {
        marker.overlay.setMap(null);
    });
    markers = [];
}

// Function to create a custom marker
function createCustomMarker(markerData, map) {
    const customMarker = document.createElement("div");
    customMarker.className = "custom-marker";
    customMarker.innerHTML = `<strong>${markerData.count}</strong>`;

    const overlay = new google.maps.OverlayView();
    overlay.onAdd = function () {
        const panes = this.getPanes();
        panes.overlayMouseTarget.appendChild(customMarker);
    };
    overlay.draw = function () {
        const projection = this.getProjection();
        const position = projection.fromLatLngToDivPixel(
            new google.maps.LatLng(markerData.position.lat, markerData.position.lng)
        );
        customMarker.style.left = `${position.x}px`;
        customMarker.style.top = `${position.y}px`;
    };
    overlay.onRemove = function () {
        if (customMarker.parentNode) {
            customMarker.parentNode.removeChild(customMarker);
        }
    };
    overlay.setMap(map);

    customMarker.addEventListener("click", () => {
        showInfoCard(markerData);
    });

    markers.push({ overlay, customMarker }); // Store markers for clearing later
}

// Function to initialize or search the map
async function initMap(query = null) {

    const spinner = document.getElementById('loading-spinner');
    spinner.style.display = 'flex';

    try {
        const bodyData = query ? { query } : {};
        const response = await fetch('http://plugin-test.local/wp-json/nl/v1/nearby-installers/', {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(bodyData)
        });

        if (!response.ok) {
            throw new Error(`Network response was not ok: ${response.statusText}`);
        }

        const data = await response.json();
        center = { lat: data.center.lat, lng: data.center.lon };

        clearMarkers();

        data.results.slice(0, 5).forEach((result, index) => {
            const distanceKm = result.distance;
            const randomBearing = Math.random() * 360;
            const offset = calculateOffset(center.lat, center.lng, distanceKm, randomBearing);

            const markerData = {
                position: { lat: offset.lat, lng: offset.lon },
                count: index + 1,
                title: result.data.company.name,
                street: result.data.company.street,
                post_code: result.data.company.post_code,
                city: result.data.company.city,
                phone: result.data.phone,
                email: result.data.email
            };

            createCustomMarker(markerData, map);
        });

        map.setCenter(center);
    } catch (error) {
        console.error('Error initializing map:', error);
    }finally{
        spinner.style.display = 'none'; 
    }
}

// Function to display the info card
function showInfoCard(markerData) {
    const infoCard = document.getElementById('info-card');
    const infoAll = `
        <div style="text-align:end;">
            <i id="close-info-box" class="fa-solid fa-circle-xmark"></i>
        </div>
        <div class="ln-header">
            <h2>${markerData.title}</h2>
        </div>
        <div class="ln-content">
            <p><strong>Address and Contact:</strong></p>
            <p>${markerData.street}, ${markerData.post_code}<br>${markerData.city}</p>
            <p>📞 ${markerData.phone}</p>
            <a href="mailto:${markerData.email}" class="ln-link">✉️ ${markerData.email}</a>
        </div>`;
    infoCard.innerHTML = infoAll;
    infoCard.style.display = "block";

    document.getElementById("close-info-box").addEventListener("click", () => {
        infoCard.style.display = "none";
    });
}

// Function to handle search
function searchLocation() {
    const query = document.getElementById("search-bar").value.trim().toLowerCase();
    if (query) {
        initMap(query);
    } else {
        initMap();
    }
}

// Initialize the map on window load
window.onload = () => {
    map = new google.maps.Map(document.getElementById("map"), {
        zoom: 12,
        mapTypeControl: false,
        streetViewControl: false,
        fullscreenControl: false
    });

    initMap(); // Default map load
};
