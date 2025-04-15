document.addEventListener("DOMContentLoaded", function() {
    // Track input method
    let isKeyboardUser = false;
    
    // Detect keyboard usage
    document.addEventListener('keydown', function() {
        isKeyboardUser = true;
    });
    
    // Detect mouse usage
    document.addEventListener('mousedown', function() {
        isKeyboardUser = false;
    });

    // Smooth scrolling 
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const targetId = this.getAttribute('href');
            if (targetId === '#') return;
            
            const targetElement = document.querySelector(targetId);
            if (targetElement) {
                targetElement.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
            }
        });
    });

    // Dropdown functionality
    const dropdowns = document.querySelectorAll('.dropdown');
    
    dropdowns.forEach(dropdown => {
        const toggle = dropdown.querySelector('.dropdown-toggle');
        const menu = dropdown.querySelector('.dropdown-menu');
        
        if (toggle && menu) {
            // Click handler
            toggle.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                toggleDropdown(dropdown);
            });

            // Hover support
            dropdown.addEventListener('mouseenter', function() {
                if (window.innerWidth > 768 && !isKeyboardUser) { 
                    toggleDropdown(dropdown, true);
                }
            });

            dropdown.addEventListener('mouseleave', function() {
                if (window.innerWidth > 768 && !isKeyboardUser) { 
                    toggleDropdown(dropdown, false);
                }
            });

            // Keyboard navigation
            toggle.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    toggleDropdown(dropdown);
                } else if (e.key === 'Escape') {
                    closeAllDropdowns();
                }
            });

            // Handle keyboard navigation within dropdown menu
            menu.addEventListener('keydown', function(e) {
                if (!isKeyboardUser) return; // Only handle keyboard navigation for keyboard users
                
                const items = menu.querySelectorAll('a, button');
                const currentIndex = Array.from(items).indexOf(document.activeElement);
                
                if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    const nextIndex = (currentIndex + 1) % items.length;
                    items[nextIndex].focus();
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    const prevIndex = (currentIndex - 1 + items.length) % items.length;
                    items[prevIndex].focus();
                } else if (e.key === 'Escape') {
                    closeAllDropdowns();
                    toggle.focus();
                }
            });
        }
    });
    
    // Close dropdowns when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.dropdown')) {
            closeAllDropdowns();
        }
    });

    // Handle window resize
    window.addEventListener('resize', function() {
        if (window.innerWidth <= 768) {
            closeAllDropdowns();
        }
    });

    // Helper functions
    function toggleDropdown(dropdown, forceOpen = null) {
        const isOpen = dropdown.classList.contains('show');
        const shouldOpen = forceOpen !== null ? forceOpen : !isOpen;

        if (shouldOpen) {
            // Close other dropdowns first
            closeAllDropdowns();
            
            // Open this dropdown
            dropdown.classList.add('show');
            const menu = dropdown.querySelector('.dropdown-menu');
            if (menu) {
                menu.classList.add('show');
                // Focus first item when opening with keyboard
                if (isKeyboardUser) {
                    const firstItem = menu.querySelector('a, button');
                    if (firstItem) {
                        firstItem.focus();
                    }
                }
            }
        } else {
            dropdown.classList.remove('show');
            const menu = dropdown.querySelector('.dropdown-menu');
            if (menu) {
                menu.classList.remove('show');
            }
        }
    }

    function closeAllDropdowns() {
        dropdowns.forEach(dropdown => {
            dropdown.classList.remove('show');
            const menu = dropdown.querySelector('.dropdown-menu');
            if (menu) {
                menu.classList.remove('show');
            }
        });
    }

    // Handle filter auto-submit
    const filterSelect = document.querySelector('.filter-box select[name="filter"]');
    if (filterSelect) {
        filterSelect.addEventListener('change', function() {
            this.closest('form').submit();
        });
    }

    // Handle search form validation (optional, keep if needed)
    /*
    const searchFormValidation = document.querySelector(".search-form");
    if (searchFormValidation) {
        searchFormValidation.addEventListener("submit", function(event) {
            const searchInputValidation = this.querySelector('input[name="search"]');
            if (!searchInputValidation.value.trim()) {
                event.preventDefault();
            }
        });
    }
    */

    // Client-side Dynamic Filtering
    const searchInput = document.querySelector('.search-box input[name="search"]');
    if (!searchInput) return;

    let timeoutId;

    searchInput.addEventListener('input', function(e) {
        // Clear the previous timeout
        clearTimeout(timeoutId);

        // Set a new timeout to delay the search
        timeoutId = setTimeout(() => {
            const searchTerm = e.target.value.toLowerCase();
            const tableRows = document.querySelectorAll('table tbody tr');

            tableRows.forEach(row => {
                // Ignore the 'no results' message row if it exists
                if (row.classList.contains('no-results-message')) return;

                let text = '';
                // Get text from all cells except the last one (actions column)
                row.querySelectorAll('td:not(:last-child)').forEach(cell => {
                    text += cell.textContent + ' ';
                });
                text = text.toLowerCase();

                // Show/hide row based on search term
                if (text.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });

            // Update "no results" message
            updateNoResults(tableRows);
        }, 300); // 300ms delay
    });

    // Function to update "no results" message
    function updateNoResults(rows) {
        const tbody = document.querySelector('table tbody');
        if (!tbody) return;
        let visibleRows = 0;
        rows.forEach(row => {
            if (!row.classList.contains('no-results-message') && row.style.display !== 'none') {
                visibleRows++;
            }
        });

        // Remove existing message if it exists
        const existingMessage = tbody.querySelector('.no-results-message');
        if (existingMessage) existingMessage.remove();

        // Add message if no results
        if (visibleRows === 0) {
            const messageRow = document.createElement('tr');
            messageRow.className = 'no-results-message';
            const colCount = document.querySelectorAll('table thead th').length;
            messageRow.innerHTML = `
                <td colspan="${colCount}" style="text-align: center; padding: 20px;">
                    No results found for your filter on this page.
                </td>
            `;
            tbody.appendChild(messageRow);
        }
    }

    // Contacts Index Functionality
    const searchInputContacts = document.getElementById('searchInput');
    if (searchInputContacts) {
        const tableRows = document.querySelectorAll('tbody tr');

        searchInputContacts.addEventListener('input', function(e) {
            const searchText = e.target.value.toLowerCase();
            
            tableRows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchText) ? '' : 'none';
            });
        });
    }

    // Mobile Navigation Menu Animation
    const hamburgerButton = document.querySelector('.navbar-toggler');
    const navbarCollapse = document.querySelector('.navbar-collapse');

    function closeMobileMenu() {
        if (navbarCollapse) {
            navbarCollapse.style.transform = 'translateX(-100%)';
            setTimeout(() => {
                navbarCollapse.classList.remove('show');
                document.body.classList.remove('nav-open');
            }, 300);
        }
    }

    function openMobileMenu() {
        if (navbarCollapse) {
            navbarCollapse.classList.add('show');
            document.body.classList.add('nav-open');
            setTimeout(() => {
                navbarCollapse.style.transform = 'translateX(0)';
            }, 10);
        }
    }

    if (hamburgerButton && navbarCollapse) {
        // Toggle menu on hamburger click
        hamburgerButton.addEventListener('click', function(e) {
            e.stopPropagation();
            if (navbarCollapse.classList.contains('show')) {
                closeMobileMenu();
            } else {
                openMobileMenu();
            }
        });

        // Close menu when clicking on a menu item (except dropdown toggles)
        const menuItems = navbarCollapse.querySelectorAll('.nav-link:not(.dropdown-toggle)');
        menuItems.forEach(item => {
            item.addEventListener('click', () => {
                closeMobileMenu();
            });
        });

        // Handle dropdown toggles in mobile menu
        const dropdownToggles = navbarCollapse.querySelectorAll('.dropdown-toggle');
        dropdownToggles.forEach(toggle => {
            toggle.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                const dropdownMenu = toggle.nextElementSibling;
                if (dropdownMenu && dropdownMenu.classList.contains('dropdown-menu')) {
                    const isOpen = dropdownMenu.classList.contains('show');
                    closeAllDropdowns();
                    if (!isOpen) {
                        dropdownMenu.classList.add('show');
                        toggle.setAttribute('aria-expanded', 'true');
                    }
                }
            });
        });
    }

    // Close menu when clicking outside
    document.addEventListener('click', function(event) {
        const isClickInsideMenu = navbarCollapse.contains(event.target);
        const isClickOnHamburger = hamburgerButton.contains(event.target);
        const isClickOnDropdown = event.target.closest('.dropdown-menu');
        
        if (!isClickInsideMenu && !isClickOnHamburger && !isClickOnDropdown && navbarCollapse.classList.contains('show')) {
            closeMobileMenu();
        }
    });
}); 