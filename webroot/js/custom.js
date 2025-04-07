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
    const searchInput = document.querySelector('.search-box input[name="search"]'); // Use correct selector
    if (!searchInput) return;

    let timeoutId;

    searchInput.addEventListener('input', function(e) {
        // Clear the previous timeout
        clearTimeout(timeoutId);

        // Set a new timeout to delay the search
        timeoutId = setTimeout(() => {
            const searchTerm = e.target.value.toLowerCase();
            const tableRows = document.querySelectorAll('table tbody tr'); // Target rows in the current table body

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
        if (!tbody) return; // Exit if tbody doesn't exist
        let visibleRows = 0;
        rows.forEach(row => {
            // Only count non-message rows that are visible
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
            // Adjust colspan based on the actual number of columns in your table
            const colCount = document.querySelectorAll('table thead th').length;
            messageRow.innerHTML = `
                <td colspan="${colCount}" style="text-align: center; padding: 20px;">
                    No results found for your filter on this page.
                </td>
            `;
            tbody.appendChild(messageRow);
        }
    }

    // Ensure sort dropdown still works (it submits the form, which is fine)
    const sortSelect = document.querySelector('.filter-box select');
    if (sortSelect) {
        sortSelect.addEventListener('change', function() {
            this.closest('form').submit();
        });
    }
});