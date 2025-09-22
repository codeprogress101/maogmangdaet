// Reusable modal for tourism intro cards with animation
const introCards = document.querySelectorAll(".intro-card");
const tourismModal = document.getElementById("tourismModal");
const tourismModalTitle = document.getElementById("tourismModalTitle");
const tourismModalDescription = document.getElementById("tourismModalDescription");
const tourismModalImage = document.getElementById("tourismModalImage");

if (introCards.length > 0) {
  introCards.forEach(card => {
    card.addEventListener("click", () => {
      // Animate card
      card.classList.add("animate");

      // After animation ends, show modal
      card.addEventListener("animationend", () => {
        card.classList.remove("animate");

        // Use the card's <img> src
        const imgTag = card.querySelector("img");
        const imgSrc = imgTag ? imgTag.src : "";

        // Fill modal
        tourismModalTitle.textContent = card.getAttribute("data-title");
        tourismModalDescription.textContent = card.getAttribute("data-description");
        tourismModalImage.src = imgSrc;

        // Show modal
        const bootstrapModal = new bootstrap.Modal(tourismModal);
        console.log("Tourism Modal image source set to:", imgSrc);
        bootstrapModal.show();
      }, { once: true });
    });
  });
}



mapboxgl.accessToken = "pk.eyJ1IjoiY29kZXByb2dyZXNzIiwiYSI6ImNtZnFqZmJ3ejBpNzAya292Znh5OWRwcWwifQ.SgOMS-LlR3P22epvz0UT2w"; // Replace with your actual Mapbox token

// Init Map
const tourismMap = new mapboxgl.Map({
  container: "tourismMap",
  style: "mapbox://styles/mapbox/streets-v12",
  center: [122.9556, 14.1222],
  zoom: 13
});

// Controls
tourismMap.addControl(new mapboxgl.NavigationControl());

const directions = new MapboxDirections({
  accessToken: mapboxgl.accessToken,
  unit: "metric",
  profile: "mapbox/driving"
});
tourismMap.addControl(directions, "top-left");

// Tourist spots
const spots = [
  {
    name: "Bagasbas Beach",
    coords: [122.9556, 14.1222],
    img: "assets/img/bagasbas.jpg",
    desc: "A popular surfing destination with golden sands."
  },
  {
    name: "Rizal Monument",
    coords: [122.9558, 14.1144],
    img: "assets/img/rizal_monument.jpg",
    desc: "The first monument honoring Dr. Jose Rizal in the Philippines (1898)."
  },
  {
    name: "Pinyasan Festival Grounds",
    coords: [122.9577, 14.1165],
    img: "assets/img/pinyasan.jpg",
    desc: "Home of the vibrant Pinyasan Festival, celebrating Daet pineapples."
  }
];

// Add markers with popups
spots.forEach(spot => {
  new mapboxgl.Marker()
    .setLngLat(spot.coords)
    .setPopup(new mapboxgl.Popup().setHTML(`
      <div style="max-width:200px">
        <img src="${spot.img}" alt="${spot.name}" style="width:100%; border-radius:8px; margin-bottom:5px;">
        <h6>${spot.name}</h6>
        <p style="font-size:0.9rem;">${spot.desc}</p>
        <button class="btn btn-sm btn-primary get-directions" data-coords="${spot.coords}">
          Get Directions
        </button>
      </div>
    `))
    .addTo(tourismMap);
});

// Sidebar click → fly to location
document.querySelectorAll("#map-spots button[data-coords]").forEach(btn => {
  btn.addEventListener("click", () => {
    const [lng, lat] = btn.getAttribute("data-coords").split(",");
    tourismMap.flyTo({ center: [parseFloat(lng), parseFloat(lat)], zoom: 15 });
  });
});

// Handle "Get Directions" in popup
document.addEventListener("click", (e) => {
  if (e.target.classList.contains("get-directions")) {
    const [lng, lat] = e.target.getAttribute("data-coords").split(",");
    directions.setDestination([parseFloat(lng), parseFloat(lat)]);
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(pos => {
        directions.setOrigin([pos.coords.longitude, pos.coords.latitude]);
      });
    }
  }
});

// Style switcher
document.getElementById("mapStyleSelector").addEventListener("change", (e) => {
  tourismMap.setStyle(e.target.value);
});

// Default to user's location
if (navigator.geolocation) {
  navigator.geolocation.getCurrentPosition(
    (pos) => {
      const userCoords = [pos.coords.longitude, pos.coords.latitude];
      tourismMap.flyTo({ center: userCoords, zoom: 14 });
      new mapboxgl.Marker({ color: "blue" })
        .setLngLat(userCoords)
        .setPopup(new mapboxgl.Popup().setText("You are here"))
        .addTo(tourismMap);
      directions.setOrigin(userCoords);

      // === MATRIX API to find nearest spot ===
      const destinations = spots.map(s => s.coords.join(",")).join(";");
      fetch(`https://api.mapbox.com/directions-matrix/v1/mapbox/driving/${userCoords.join(",")};${destinations}?access_token=${mapboxgl.accessToken}`)
        .then(res => res.json())
        .then(data => {
          const durations = data.durations[0].slice(1);
          const minIndex = durations.indexOf(Math.min(...durations));
          console.log("Nearest spot:", spots[minIndex].name, "in", (durations[minIndex] / 60).toFixed(1), "min");

          // Highlight nearest in sidebar
          const nearestBtn = document.querySelectorAll("#map-spots button[data-coords]")[minIndex];
          if (nearestBtn) nearestBtn.classList.add("list-group-item-success");
        });
    },
    () => {
      console.warn("Geolocation denied, using Daet as default.");
    }
  );
}

