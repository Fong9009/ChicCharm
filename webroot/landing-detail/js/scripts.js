/*!
* Start Bootstrap - Creative v7.0.7 (https://startbootstrap.com/theme/creative)
* Copyright 2013-2023 Start Bootstrap
* Licensed under MIT (https://github.com/StartBootstrap/startbootstrap-creative/blob/master/LICENSE)
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

    // Activate SimpleLightbox plugin for portfolio items
    new SimpleLightbox({
        elements: '#portfolio a.portfolio-box'
    });

    // Add this to your JavaScript file or inline script
    document.addEventListener('DOMContentLoaded', function() {
        const dropdowns = document.querySelectorAll('#mainNav .dropdown');
        
        dropdowns.forEach(dropdown => {
            const toggle = dropdown.querySelector('.dropdown-toggle');
            
            toggle.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                // Close other dropdowns
                dropdowns.forEach(d => {
                    if (d !== dropdown) {
                        d.classList.remove('show');
                        d.querySelector('.dropdown-menu').classList.remove('show');
                    }
                });
                
                // Toggle current dropdown
                dropdown.classList.toggle('show');
                dropdown.querySelector('.dropdown-menu').classList.toggle('show');
            });
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.dropdown')) {
                dropdowns.forEach(d => {
                    d.classList.remove('show');
                    d.querySelector('.dropdown-menu').classList.remove('show');
                });
            }
        });
    });
    
    // JavaScript for dropdown functionality
    document.addEventListener("DOMContentLoaded", function() {
        // Close dropdowns when clicking outside
        window.onclick = function(event) {
            if (!event.target.matches(".dropdown-toggle")) {
                var dropdowns = document.getElementsByClassName("dropdown-content");
                for (var i = 0; i < dropdowns.length; i++) {
                    var openDropdown = dropdowns[i];
                    if (openDropdown.classList.contains("show")) {
                        openDropdown.classList.remove("show");
                    }
                }
            }
        }
        
        // Toggle dropdown
        var toggles = document.getElementsByClassName("dropdown-toggle");
        for (var i = 0; i < toggles.length; i++) {
            toggles[i].addEventListener("click", function(event) {
                event.stopPropagation();
                var content = this.nextElementSibling;
                var allDropdowns = document.getElementsByClassName("dropdown-content");
                
                // Close all other dropdowns
                for (var j = 0; j < allDropdowns.length; j++) {
                    if (allDropdowns[j] !== content) {
                        allDropdowns[j].classList.remove("show");
                    }
                }
                
                content.classList.toggle("show");
            });
        }
    });
});
