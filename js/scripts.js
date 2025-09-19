/*!
* Start Bootstrap - Grayscale v7.0.6 (https://startbootstrap.com/theme/grayscale)
* Copyright 2013-2023 Start Bootstrap
* Licensed under MIT (https://github.com/StartBootstrap/startbootstrap-grayscale/blob/master/LICENSE)
*/
//
// Scripts
// 

window.addEventListener('DOMContentLoaded', event => {

    // Navbar shrink function
    var navbarShrink = function () {
        const navbarCollapsible = document.body.querySelector('#mainNav');
        if (!navbarCollapsible) {
            return;
        }
        if (window.scrollY === 0) {
            navbarCollapsible.classList.remove('navbar-shrink')
        } else {
            navbarCollapsible.classList.add('navbar-shrink')
        }

    };

    // Shrink the navbar 
    navbarShrink();

    // Shrink the navbar when page is scrolled
    document.addEventListener('scroll', navbarShrink);

    // Activate Bootstrap scrollspy on the main nav element
    const mainNav = document.body.querySelector('#mainNav');
    if (mainNav) {
        new bootstrap.ScrollSpy(document.body, {
            target: '#mainNav',
            rootMargin: '0px 0px -40%',
        });
    };

    // Collapse responsive navbar when toggler is visible
    const navbarToggler = document.body.querySelector('.navbar-toggler');
    const responsiveNavItems = [].slice.call(
        document.querySelectorAll('#navbarResponsive .nav-link')
    );
    responsiveNavItems.map(function (responsiveNavItem) {
        responsiveNavItem.addEventListener('click', () => {
            if (window.getComputedStyle(navbarToggler).display !== 'none') {
                navbarToggler.click();
            }
        });
    });

});


// Weather Forecast Script for Daet
document.addEventListener("DOMContentLoaded", () => {
  const API_KEY = "fdd7ecf54c471b5746f28023438ab8c5"; // your OWM key
  const CITY = "Daet,PH";

  // Get 5-day forecast (3-hour intervals)
  fetch(`https://api.openweathermap.org/data/2.5/forecast?q=${CITY}&units=metric&appid=${API_KEY}`)
    .then(res => res.json())
    .then(data => {
      // Today (from first forecast item)
      const today = data.list[0];
      const weatherToday = document.getElementById("weather-today");
      if (weatherToday) {
        weatherToday.innerHTML = `
          <p class="mb-1">Today: ${Math.round(today.main.temp)}°C</p>
          <p class="mb-2">${today.weather[0].description}</p>
        `;
      }

      // Next 3 days (pick noon forecasts ~12:00)
      const forecastDiv = document.getElementById("weather-forecast");
      if (forecastDiv) {
        forecastDiv.innerHTML = "";

        const noonForecasts = data.list.filter(item => item.dt_txt.includes("12:00:00"));
        noonForecasts.slice(0, 3).forEach(day => {
          const date = new Date(day.dt * 1000);
          const weekday = date.toLocaleDateString("en-US", { weekday: "short" });

          forecastDiv.innerHTML += `
            <div class="text-center mx-1">
              <strong>${weekday}</strong><br>
              ${Math.round(day.main.temp)}°C<br>
              <img src="https://openweathermap.org/img/wn/${day.weather[0].icon}.png" alt="${day.weather[0].description}">
            </div>
          `;
        });
      }
    })
    .catch(err => {
      console.error("Weather API error:", err);
      const weatherToday = document.getElementById("weather-today");
      if (weatherToday) {
        weatherToday.innerHTML = "<p>Weather unavailable</p>";
      }
    });
});



document.addEventListener("DOMContentLoaded", () => {
    const video = document.getElementById("opportunityVideo");

    // Intersection Observer to detect when video is visible
    const observer = new IntersectionObserver(entries => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          video.play().catch(err => console.log("Autoplay blocked:", err));
        } else {
          video.pause();
        }
      });
    }, { threshold: 0.5 }); // 50% visible = considered "in view"

    observer.observe(video);
  });


document.addEventListener("DOMContentLoaded", () => {
  const largePhoto = document.getElementById("large-photo");
  const smallPhotos = document.querySelectorAll(".gallery-small img");

  let index = 0;

  setInterval(() => {
    // Add fade+zoom effect
    largePhoto.classList.add("fade-out");

    setTimeout(() => {
      // Swap src between large and current small photo
      const tempSrc = largePhoto.src;
      largePhoto.src = smallPhotos[index].src;
      smallPhotos[index].src = tempSrc;

      // Remove fade-out to show fade-in
      largePhoto.classList.remove("fade-out");

      // Move to next small photo
      index = (index + 1) % smallPhotos.length;
    }, 800); // matches CSS transition time
  }, 4000); // every 4s
});




// what's new in daet
document.addEventListener("DOMContentLoaded", () => {
  const elements = document.querySelectorAll(".animate-on-scroll");

  const observer = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          // run one-time fade animation
          entry.target.classList.add("animate__animated", entry.target.dataset.animate, "visible");

          // if extra infinite animation is needed (like pulse), add it after fade finishes
          if (entry.target.dataset.extra) {
            const extras = entry.target.dataset.extra.split(" ");
            setTimeout(() => {
              // remove the fadeInUp class so it doesn’t loop
              entry.target.classList.remove(entry.target.dataset.animate);
              entry.target.classList.add(...extras);
            }, 1000); // matches Animate.css fadeInUp duration
          }

          observer.unobserve(entry.target); // stop observing after first run
        }
      });
    },
    { threshold: 0.2 }
  );

  elements.forEach((el) => observer.observe(el));
});



document.addEventListener('DOMContentLoaded', () => {
  const modalEl = document.getElementById('imageModal');
  const modalImage = document.getElementById('modalImage');

  if (!modalEl || !modalImage) {
    console.warn('Image modal or modal image element not found. Make sure #imageModal and #modalImage exist.');
    return;
  }

  // Bootstrap modal instance
  const bsModal = bootstrap.Modal.getOrCreateInstance(modalEl);

  /**
   * Helper: given a clicked element, return the best image source.
   * Supports:
   *  - <img> (uses currentSrc || src)
   *  - elements with data-full or data-src (useful for lazy loading or high-res)
   *  - <picture> children -> still works because clicked element should be <img>
   */
  function getImageSource(el) {
    if (!el) return '';

    // If element has explicit high-res URL
    if (el.dataset && (el.dataset.full || el.dataset.src)) {
      return el.dataset.full || el.dataset.src;
    }

    // If it's an <img>, prefer currentSrc (works with <picture> and srcset)
    if (el.tagName && el.tagName.toLowerCase() === 'img') {
      return el.currentSrc || el.src || '';
    }

    // If background image style (e.g., div with background-image: url(...))
    const bg = window.getComputedStyle(el).backgroundImage;
    if (bg && bg !== 'none') {
      // extract url("...") content
      const match = bg.match(/url\(["']?(.*?)["']?\)/);
      if (match) return match[1];
    }

    return '';
  }

  // Event delegation: listen for clicks on any image you want to open in modal.
  // Use a selector that matches your images (adjust 'history-img' if needed).
  document.addEventListener('click', (evt) => {
    // Try to find the nearest clickable image element:
    const clicked = evt.target.closest('img.history-img, [data-image-modal], .history-img'); 
    // - img.history-img : <img class="history-img">
    // - [data-image-modal] : elements explicitly marked to open modal
    // - .history-img : fallback if image is a div with background-image

    if (!clicked) return;
    evt.preventDefault();

    const src = getImageSource(clicked);
    if (!src) {
      console.warn('No image source found for clicked element', clicked);
      return;
    }

    // Set modal image src and alt
    modalImage.src = src;
    modalImage.alt = clicked.alt || clicked.dataset.alt || '';

    // If the clicked <img> had a srcset, copy it to modal for responsive high-res
    if (clicked.tagName && clicked.tagName.toLowerCase() === 'img') {
      if (clicked.hasAttribute('srcset')) {
        modalImage.setAttribute('srcset', clicked.getAttribute('srcset'));
      } else {
        modalImage.removeAttribute('srcset');
      }
    } else {
      modalImage.removeAttribute('srcset');
    }

    // Show the bootstrap modal
    bsModal.show();
  });

  // Clear modal image on hide to free memory / stop downloads
  modalEl.addEventListener('hidden.bs.modal', () => {
    modalImage.src = '';
    modalImage.removeAttribute('srcset');
    modalImage.alt = '';
  });
});



// for socio economic

document.addEventListener("DOMContentLoaded", () => {
  // Age Distribution (Bar Chart)
  const ageCtx = document.getElementById("ageChart").getContext("2d");
  new Chart(ageCtx, {
    type: "bar",
    data: {
      labels: ["0-14", "15-24", "25-54", "55-64", "65+"],
      datasets: [{
        label: "Population by Age Group",
        data: [18000, 22000, 45000, 10000, 5000], // sample data
        backgroundColor: "#0d6efd"
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: { display: false }
      },
      scales: {
        y: { beginAtZero: true }
      }
    }
  });

  // Gender Distribution (Pie Chart)
  const genderCtx = document.getElementById("genderChart").getContext("2d");
  new Chart(genderCtx, {
    type: "pie",
    data: {
      labels: ["Male", "Female"],
      datasets: [{
        data: [52000, 59600], // sample data
        backgroundColor: ["#0d6efd", "#dc3545"]
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: {
          position: "bottom"
        }
      }
    }
  });

  // Economy (Doughnut Chart)
  const economyCtx = document.getElementById("economyChart").getContext("2d");
  new Chart(economyCtx, {
    type: "doughnut",
    data: {
      labels: ["Agriculture", "Commerce & Services", "Fisheries", "Others"],
      datasets: [{
        data: [40, 35, 15, 10], // sample percentages
        backgroundColor: ["#198754", "#0d6efd", "#20c997", "#ffc107"]
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: {
          position: "bottom"
        }
      }
    }
  });
});



// trial for tourism

// Optional smooth parallax effect
document.addEventListener("scroll", () => {
  const scrolled = window.scrollY;
  document.querySelectorAll(".parallax").forEach(el => {
    el.style.backgroundPositionY = -(scrolled * 0.3) + "px";
  });
});


document.addEventListener("DOMContentLoaded", () => {
  const globeContainer = document.getElementById("globeViz");

  // Initialize globe
  const globe = Globe()(globeContainer)
    .globeImageUrl("//unpkg.com/three-globe/example/img/earth-night.jpg") // Night Earth texture
    .bumpImageUrl("//unpkg.com/three-globe/example/img/earth-topology.png") // 3D terrain
    .backgroundColor("#ffffff") // Transparent/white background
    .pointOfView({ lat: 14.1226, lng: 122.9553, altitude: 2 }); // Focus near Daet

  // Marker for Daet, Camarines Norte
  const markers = [
    {
      lat: 14.1226, // Daet latitude
      lng: 122.9553, // Daet longitude
      size: 0.5,
      color: "red",
      name: "Daet, Camarines Norte"
    }
  ];

  globe.pointsData(markers)
       .pointAltitude("size")
       .pointColor("color");

  // Tooltip on hover
  globe.onPointHover(d => globeContainer.title = d ? d.name : "");
});



// 

document.addEventListener("DOMContentLoaded", () => {
  mapboxgl.accessToken =
    "pk.eyJ1IjoiY29kZXByb2dyZXNzIiwiYSI6ImNtZnFqZmJ3ejBpNzAya292Znh5OWRwcWwifQ.SgOMS-LlR3P22epvz0UT2w";

  const map = new mapboxgl.Map({
    container: "map",
    style: "mapbox://styles/mapbox/satellite-streets-v12",
    center: [122.9553, 14.1226], // Daet
    zoom: 12,
    pitch: 60,
    bearing: -20
  });

  map.addControl(new mapboxgl.NavigationControl());

  // Directions plugin
  const directions = new MapboxDirections({
    accessToken: mapboxgl.accessToken,
    unit: "metric",
    profile: "mapbox/driving",
    alternatives: true,
    geometries: "geojson",
    controls: { instructions: true }
  });
  map.addControl(directions, "top-left");

  // Hotspots
  const hotspots = [
    {
      name: "Bagasbas Beach",
      desc: "Famous surfing destination with golden sand.",
      coords: [122.9560, 14.1300],
      color: "blue"
    },
    {
      name: "First Rizal Monument",
      desc: "Historical landmark honoring Dr. Jose Rizal (1898).",
      coords: [122.9558, 14.1190],
      color: "green"
    },
    {
      name: "Pinyasan Festival Grounds",
      desc: "Venue for the annual pineapple festival every June.",
      coords: [122.9575, 14.1228],
      color: "orange"
    },
    {
      name: "Daet Town Center",
      desc: "The capital town and provincial hub of Camarines Norte.",
      coords: [122.9553, 14.1226],
      color: "red"
    }
  ];

  map.on("load", () => {
    // 3D terrain
    map.addSource("mapbox-dem", {
      type: "raster-dem",
      url: "mapbox://mapbox.terrain-rgb",
      tileSize: 512,
      maxzoom: 14
    });
    map.setTerrain({ source: "mapbox-dem", exaggeration: 1.5 });

    // Add markers + sidebar items
    const sidebar = document.getElementById("hotspot-list");

    hotspots.forEach((spot, index) => {
      // Marker
      new mapboxgl.Marker({ color: spot.color })
        .setLngLat(spot.coords)
        .setPopup(
          new mapboxgl.Popup().setHTML(`
            <h6>${spot.name}</h6>
            <p>${spot.desc}</p>
            <button onclick="setRoute(${spot.coords[0]}, ${spot.coords[1]})" class="btn btn-sm btn-primary mt-2">Get Directions</button>
          `)
        )
        .addTo(map);

      // Sidebar entry
      const item = document.createElement("button");
      item.className = "list-group-item list-group-item-action";
      item.innerHTML = `<strong>${spot.name}</strong><br><small>${spot.desc}</small>`;
      item.addEventListener("click", () => {
        map.flyTo({ center: spot.coords, zoom: 15, pitch: 60, bearing: -20 });
        directions.setDestination(spot.coords);
      });
      sidebar.appendChild(item);
    });
  });

  // Function for "Get Directions" button inside popups
  window.setRoute = (lng, lat) => {
    directions.setDestination([lng, lat]);
  };
});

