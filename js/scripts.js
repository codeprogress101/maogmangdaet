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



// Weather Forecast Script
const API_KEY = "fdd7ecf54c471b5746f28023438ab8c5"; // replace with your real key
const LAT = 14.11;  // Daet latitude
const LON = 122.95; // Daet longitude

fetch(`https://api.openweathermap.org/data/2.5/onecall?lat=${LAT}&lon=${LON}&units=metric&exclude=minutely,hourly,alerts&appid=${API_KEY}`)
  .then(res => res.json())
  .then(data => {
    // Today
    const today = data.current;
    const weatherToday = document.getElementById("weather-today");
    if (weatherToday) {
      weatherToday.innerHTML = `
        <p class="mb-1">Today: ${Math.round(today.temp)}°C</p>
        <p class="mb-2">${today.weather[0].description}</p>
      `;
    }

    // 3-day forecast
    const forecastDiv = document.getElementById("weather-forecast");
    if (forecastDiv) {
      forecastDiv.innerHTML = "";
      data.daily.slice(1, 4).forEach((day, i) => {
        const date = new Date(day.dt * 1000);
        const weekday = date.toLocaleDateString("en-US", { weekday: "short" });
        forecastDiv.innerHTML += `
          <div>
            <strong>${weekday}</strong><br>
            ${Math.round(day.temp.day)}°C<br>
            <img src="https://openweathermap.org/img/wn/${day.weather[0].icon}.png" alt="">
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
