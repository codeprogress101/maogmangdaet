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