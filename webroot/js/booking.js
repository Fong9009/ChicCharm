/* Customer Booking Functionality */
document.addEventListener('DOMContentLoaded', function() {
    // --- Configuration ---
    const GET_STYLISTS_URL_BASE = '/bookings/get-stylists-for-service/'; 
    const GET_TIMESLOTS_URL = '/bookings/get-available-time-slots';
    const CSRF_TOKEN = document.querySelector('input[name="_csrfToken"]')?.value; 

    // --- DOM Elements ---
    const serviceCheckboxes = document.querySelectorAll('.service-checkbox');
    const totalCostInput = document.getElementById('total-cost'); 
    const bookingDateInput = document.getElementById('booking-date');
    const startTimeInput = document.getElementById('start-time'); 
    const endTimeInput = document.getElementById('end-time'); 
    const serviceStylistSelectionsContainer = document.getElementById('service-stylist-selections'); 
    const timeRangeDisplay = document.getElementById('time-range-display'); 
    const startTimeDisplay = document.getElementById('start-time-display'); 
    const endTimeDisplay = document.getElementById('end-time-display'); 
    const serviceCountDisplay = document.getElementById('service-count');
    const serviceTotalDisplay = document.getElementById('service-total');
    const selectedServicesListDisplay = document.getElementById('selected-services-list');
    const bookingForm = document.getElementById('booking-form');
    const closingTimeWarningContainer = document.getElementById('closing-time-warning-container');

    // --- State ---
    let serviceSelections = []; 

    // --- Initialization ---
    initializeBookingState();
    configureDateInput();

    // --- Functions ---

    function getCsrfToken() {
        return CSRF_TOKEN || document.querySelector('input[name="_csrfToken"]')?.value;
    }

    // Set min/max dates
    function configureDateInput() {
        const today = new Date();
        const todayStr = today.toISOString().split('T')[0];
        bookingDateInput.min = todayStr;
        bookingDateInput.max = new Date(today.getFullYear() + 1, today.getMonth(), today.getDate()).toISOString().split('T')[0];
        if (!bookingDateInput.value && !bookingDateInput.disabled) {
             bookingDateInput.value = todayStr;
        }
    }

    // Get service details from checkbox data attributes
    function getServiceDetails(serviceId) {
        const checkbox = document.querySelector(`#service-${serviceId}`);
        if (!checkbox) return null;
        return {
            id: serviceId,
            name: document.querySelector(`label[for="service-${serviceId}"]`)?.textContent.trim() || 'Unknown Service',
            duration: parseInt(checkbox.dataset.duration, 10) || 0,
            cost: parseFloat(checkbox.dataset.cost) || 0,
            initialStylistId: checkbox.dataset.selectedStylistId || null 
        };
    }

    // Initialize state from potentially pre-checked boxes (edit mode)
    function initializeBookingState() {
        serviceSelections = [];
        serviceCheckboxes.forEach(checkbox => {
            if (checkbox.checked) {
                const details = getServiceDetails(checkbox.value);
                if (details) {
                    serviceSelections.push({
                        serviceId: details.id,
                        name: details.name,
                        duration: details.duration,
                        cost: details.cost,
                        selectedStylistId: details.initialStylistId 
                    });
                }
            }
        });
        // Initial UI setup based on loaded state
        updateInputStates();
        renderServiceStylistSelections(); 
        calculateAndUpdateSummary();
        if (bookingDateInput.value && startTimeInput.value && serviceSelections.length > 0) {
             updateEndTimeDisplay(); 
        }
         if (bookingDateInput.value && serviceSelections.length > 0) {
             updateAvailableTimeSlots(); 
         }

    }

    // Update enable/disable state of inputs
    function updateInputStates() {
        const allStylistsSelected = serviceSelections.length > 0 && serviceSelections.every(s => s.selectedStylistId && s.selectedStylistId !== 'any');

        // --- Date Input State ---
        if (allStylistsSelected) {
            bookingDateInput.disabled = false;
            // Ensure date input has a value if enabled and empty
            if (!bookingDateInput.value) {
                bookingDateInput.value = new Date().toISOString().split('T')[0];
            }
        } else {
            bookingDateInput.disabled = true;
            bookingDateInput.value = ''; 
        }

        // --- Time Input State ---
        if (!bookingDateInput.disabled && bookingDateInput.value) {
            // Enable time input ONLY if date is enabled AND has a value
            startTimeInput.disabled = false;
             // Check if it's currently showing a placeholder, if so, trigger slot update
            if (startTimeInput.options.length <= 1 && startTimeInput.value === '') {
                 // Ensure the placeholder is correct before potentially fetching
                 if(startTimeInput.options[0]) {
                     startTimeInput.options[0].textContent = 'Loading times...'; 
                 }
                updateAvailableTimeSlots(); 
            }
        } else {
            // Disable time input if date is disabled or empty
            startTimeInput.disabled = true;
            startTimeInput.innerHTML = '<option value="">Select Date</option>'; 
            updateEndTimeDisplay(); 
        }

        // If no services are selected at all, reset both
        if (serviceSelections.length === 0) {
            bookingDateInput.disabled = true;
            bookingDateInput.value = '';
            startTimeInput.disabled = true;
            startTimeInput.innerHTML = '<option value="">Select Service(s) and Stylist(s)</option>'; 
            startTimeInput.value = '';
            endTimeInput.value = '';
            timeRangeDisplay.style.display = 'none';
            clearClosingTimeWarning();
        }
    }

     // Check if a date string is in the past
    function isDateInPast(dateStr) {
        const selectedDate = new Date(dateStr);
        const todayStart = new Date();
        todayStart.setHours(0, 0, 0, 0);
        return selectedDate < todayStart;
    }


    // Calculate total cost and duration, update summary display
    function calculateAndUpdateSummary() {
        let totalCost = 0;
        let totalDuration = 0;

        selectedServicesListDisplay.innerHTML = ''; 

        serviceSelections.forEach(selection => {
            totalCost += selection.cost;
            totalDuration += selection.duration;

            // Update the list display
            const listItem = document.createElement('div');
            listItem.className = 'selected-service-item';
            listItem.textContent = `• ${selection.name}`;
            selectedServicesListDisplay.appendChild(listItem);
        });

        totalCostInput.value = totalCost.toFixed(2); 
        serviceCountDisplay.textContent = serviceSelections.length;
        serviceTotalDisplay.textContent = totalCost.toFixed(2);

        // Update end time display if start time is selected
        updateEndTimeDisplay();
    }

    // Update the display showing the calculated end time
    function updateEndTimeDisplay() {
        const startTimeValue = startTimeInput.value;

        if (!startTimeValue || serviceSelections.length === 0 || !bookingDateInput.value) {
            timeRangeDisplay.style.display = 'none';
            endTimeInput.value = ''; 
            return;
        }

        let totalDuration = 0;
        serviceSelections.forEach(s => { totalDuration += s.duration; });

        const [startHours, startMinutes] = startTimeValue.split(':').map(Number);
        const startDateTime = new Date(bookingDateInput.value + 'T' + startTimeValue + ':00');

        if (isNaN(startDateTime)) { 
             timeRangeDisplay.style.display = 'none';
             endTimeInput.value = '';
             return;
         }

        const endDateTime = new Date(startDateTime.getTime() + totalDuration * 60000);
        const endHours = endDateTime.getHours();
        const endMinutes = endDateTime.getMinutes();

        // Format times for display (12-hour clock)
        const format12Hour = (date) => {
            let hours = date.getHours();
            let minutes = date.getMinutes();
            const ampm = hours >= 12 ? 'PM' : 'AM';
            hours = hours % 12;
            hours = hours ? hours : 12; 
            minutes = minutes < 10 ? '0' + minutes : minutes;
            return `${hours}:${minutes} ${ampm}`;
        };

        const displayStartTime = format12Hour(startDateTime);
        const displayEndTime = format12Hour(endDateTime);

        // Update display elements
        startTimeDisplay.textContent = displayStartTime;
        endTimeDisplay.textContent = displayEndTime;
        timeRangeDisplay.style.display = 'block';

        // Store 24-hour end time in hidden input
        endTimeInput.value = `${endHours.toString().padStart(2, '0')}:${endMinutes.toString().padStart(2, '0')}`;

        // Check against closing time (5 PM / 17:00)
        if (endHours > 17 || (endHours === 17 && endMinutes > 0)) {
            endTimeDisplay.textContent += ' (Ends After Closing)';
            timeRangeDisplay.style.color = '#856404'; 
            timeRangeDisplay.style.backgroundColor = '#fff3cd';
            timeRangeDisplay.style.border = '1px solid #ffeeba';
            timeRangeDisplay.style.padding = '0.375rem 0.75rem';
        } else {
            timeRangeDisplay.style.color = '';
            timeRangeDisplay.style.backgroundColor = '';
            timeRangeDisplay.style.border = '';
            timeRangeDisplay.style.padding = '';
        }
    }

     function displayClosingTimeWarning(message) {
         if (closingTimeWarningContainer) {
             closingTimeWarningContainer.innerHTML = `<div class="alert alert-warning mt-3" role="alert">${message}</div>`;
             closingTimeWarningContainer.style.display = 'block';
         }
     }

     function clearClosingTimeWarning() {
         if (closingTimeWarningContainer) {
             closingTimeWarningContainer.innerHTML = '';
             closingTimeWarningContainer.style.display = 'none';
         }
     }

    // Fetch available stylists for ONE service
    async function getStylistsForService(serviceId) {
        const url = `${GET_STYLISTS_URL_BASE}${serviceId}`;
        console.log("Fetching stylists for service:", serviceId, "URL:", url); 
        try {
            const response = await fetch(url, {
                method: 'GET', 
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-Token': getCsrfToken() 
                }
            });
            if (!response.ok) {
                 const errorData = await response.json().catch(() => ({})); 
                 console.error(`HTTP error fetching stylists for ${serviceId}: ${response.status}`, errorData);
                 throw new Error(`HTTP error! Status: ${response.status}`);
             }
            return await response.json();
        } catch (error) {
            console.error('Error fetching stylists for service:', serviceId, error);
            return []; 
        }
    }

    // Render the service/stylist selection rows
    async function renderServiceStylistSelections() {
        serviceStylistSelectionsContainer.innerHTML = ''; // Clear previous

        if (serviceSelections.length === 0) return;

        // Use Promise.all to fetch stylists concurrently
        const stylistPromises = serviceSelections.map(sel => getStylistsForService(sel.serviceId));
        const allStylistsResults = await Promise.all(stylistPromises);

        serviceSelections.forEach((selection, index) => {
            const availableStylists = allStylistsResults[index] || [];

            const container = document.createElement('div');
            container.className = 'service-stylist-selection mb-3 p-3 border rounded'; // Add some styling
            container.dataset.serviceId = selection.serviceId;

            // Service Name Label
            const label = document.createElement('h6');
            label.textContent = selection.name;
            label.className = 'mb-2';
            container.appendChild(label);

            // Stylist Select Dropdown
            const select = document.createElement('select');
            select.className = 'form-control stylist-select';

            select.name = `bookings_services[${selection.serviceId}][stylist_id]`;
            select.required = true;
            select.dataset.serviceId = selection.serviceId; 
            // Update validation message
            select.setAttribute('oninvalid', "this.setCustomValidity('Please Select a Stylist')");
            select.setAttribute('oninput', "this.setCustomValidity('')");

            // Options
            select.innerHTML = '<option value="">Select Stylist...</option>'; 

            if (Array.isArray(availableStylists)) {
                availableStylists.forEach(stylist => {
                    const option = document.createElement('option');
                    option.value = stylist.id;
                    option.textContent = stylist.name;
                    select.appendChild(option);
                });
            } else {
                 console.warn("Stylist data is not an array for service:", selection.serviceId, availableStylists);
             }


            // Pre-select value based on current state 
            // Ensure selectedStylistId is not 'any' before setting
            select.value = (selection.selectedStylistId && selection.selectedStylistId !== 'any') ? selection.selectedStylistId : '';

            container.appendChild(select);

             // Add listener to update state and fetch time slots on change
             select.addEventListener('change', handleStylistSelectionChange);


            // Hidden Inputs (generated here for simplicity)
            const serviceInput = document.createElement('input');
            serviceInput.type = 'hidden';
            serviceInput.name = `bookings_services[${selection.serviceId}][service_id]`;
            serviceInput.value = selection.serviceId;

            const costInput = document.createElement('input');
            costInput.type = 'hidden';
            costInput.name = `bookings_services[${selection.serviceId}][service_cost]`;
            costInput.value = selection.cost.toFixed(2);


            container.appendChild(serviceInput);
            container.appendChild(costInput);

            serviceStylistSelectionsContainer.appendChild(container);
        });
         // After rendering, ensure time slots are updated if needed
         // Check if all selections have a specific stylist ID (not null/empty)
         if (bookingDateInput.value && serviceSelections.length > 0 && serviceSelections.every(s => s.selectedStylistId && s.selectedStylistId !== 'any')) {
            updateAvailableTimeSlots();
         }
    }


    // Fetch available time slots based on current selections
    async function updateAvailableTimeSlots() {
        clearClosingTimeWarning();

        // Conditions required to fetch time slots - ensure specific stylist ID is selected and date is set
        const allStylistsSelected = serviceSelections.every(s => s.selectedStylistId && s.selectedStylistId !== 'any');
        if (!bookingDateInput.value || serviceSelections.length === 0 || !allStylistsSelected) {
            startTimeInput.disabled = true;
            // Determine correct placeholder text for the time select option
            if (!bookingDateInput.value && serviceSelections.length > 0) { 
                 startTimeInput.innerHTML = '<option value="">Select Date</option>';
            } else { 
                 startTimeInput.innerHTML = '<option value="">Select Service(s) and Stylist(s)</option>';
            }
            updateEndTimeDisplay();
            return;
        }
        
        startTimeInput.disabled = true;
        startTimeInput.innerHTML = '<option value="">Loading available times...</option>';

        // Data sent must contain only integer stylist IDs
        const dataToSend = {
            date: bookingDateInput.value,
            selected_services: serviceSelections.map(s => ({
                service_id: parseInt(s.serviceId, 10),
                stylist_id: parseInt(s.selectedStylistId, 10) // Ensure it's an integer
            }))
        };
        
        // Validate before sending - ensure all stylist IDs are valid integers
        if (dataToSend.selected_services.some(s => isNaN(s.stylist_id))) {
             console.error("Attempted to fetch time slots with invalid stylist ID.");
             startTimeInput.innerHTML = '<option value="">Error: Invalid stylist selection</option>';
             return;
         }

        console.log("Fetching time slots with data:", JSON.stringify(dataToSend)); 

        try {
            const response = await fetch(GET_TIMESLOTS_URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-Token': getCsrfToken()
                },
                body: JSON.stringify(dataToSend)
            });

            if (!response.ok) {
                const errorData = await response.json().catch(() => ({ error: `HTTP error! Status: ${response.status}`}));
                console.error('Error response fetching time slots:', errorData);
                 // Handle specific backend errors if possible
                 if (response.status === 400 && errorData.error) {
                     startTimeInput.innerHTML = `<option value="">Error: ${errorData.error}</option>`;
                 } else if (response.status === 404 && errorData.error) {
                      startTimeInput.innerHTML = `<option value="">Error: ${errorData.error}</option>`;
                 } else if (response.status === 500 && errorData.error){
                      startTimeInput.innerHTML = '<option value="">Server error loading slots</option>';
                      // Maybe display a generic error message to the user
                 } else {
                      startTimeInput.innerHTML = '<option value="">Error loading time slots</option>';
                 }
                 throw new Error(`HTTP error! Status: ${response.status}`);
             }


            const slots = await response.json();
            console.log("Received slots:", slots);

             // Check for closing time message from backend (though unlikely with new logic)
             if (slots.message) {
                 displayClosingTimeWarning(slots.message);
             }


            startTimeInput.innerHTML = '<option value="">Select a start time</option>'; 
            startTimeInput.disabled = false;

             // Filter slots based on whether they are in the past for *today*
             const todayStr = new Date().toISOString().split('T')[0];
             const now = new Date();
             const currentHour = now.getHours();
             const currentMinute = now.getMinutes();

             const validSlots = Array.isArray(slots) ? slots.filter(slot => {
                 if (bookingDateInput.value === todayStr) {
                     const [slotHour, slotMinute] = slot.value.split(':').map(Number);
                     if (slotHour < currentHour) return false;
                     if (slotHour === currentHour && slotMinute < currentMinute) return false;
                 }
                 return true;
             }) : [];


            if (validSlots.length === 0) {
                startTimeInput.innerHTML = '<option value="">No available time slots found</option>';
                startTimeInput.disabled = true;
            } else {
                validSlots.forEach(slot => {
                    const option = document.createElement('option');
                    option.value = slot.value; 
                    option.textContent = slot.text; 
                    startTimeInput.appendChild(option);
                });
            }
             // Try to re-select previous value if it exists in new list
             const previousStartTime = startTimeInput.dataset.previousValue;
             if (previousStartTime && startTimeInput.querySelector(`option[value="${previousStartTime}"]`)) {
                 startTimeInput.value = previousStartTime;
             } else {
                 startTimeInput.value = "";
             }
             delete startTimeInput.dataset.previousValue; 

        } catch (error) {
            console.error('Error updating available time slots:', error);
            startTimeInput.innerHTML = '<option value="">Error loading times</option>';
            startTimeInput.disabled = true;
        } finally {
             updateEndTimeDisplay();
         }
    }

    // --- Event Handlers ---

    // Service checkbox change
    function handleServiceCheckboxChange(event) {
        const checkbox = event.target;
        const serviceId = checkbox.value;
        const details = getServiceDetails(serviceId);

        if (!details) return;

        if (checkbox.checked) {
            // Add to state
            serviceSelections.push({
                serviceId: details.id,
                name: details.name,
                duration: details.duration,
                cost: details.cost,
                selectedStylistId: null // Needs selection
            });
        } else {
            // Remove from state
            serviceSelections = serviceSelections.filter(s => s.serviceId !== serviceId);
            // Also remove the corresponding stylist selection UI row if it exists
             const rowToRemove = serviceStylistSelectionsContainer.querySelector(`.service-stylist-selection[data-service-id="${serviceId}"]`);
             if (rowToRemove) rowToRemove.remove();
        }

        // Update UI
        updateInputStates(); 
        renderServiceStylistSelections(); 
        calculateAndUpdateSummary();
    }

    // Stylist dropdown change
    function handleStylistSelectionChange(event) {
        const select = event.target;
        const serviceId = select.dataset.serviceId;
        const selectedStylistId = select.value; 

        const index = serviceSelections.findIndex(s => s.serviceId === serviceId);
        if (index !== -1) {
            serviceSelections[index].selectedStylistId = selectedStylistId ? parseInt(selectedStylistId, 10) : null;
        }

        startTimeInput.dataset.previousValue = startTimeInput.value;

        updateInputStates(); 
    }

    // Date input change
    function handleDateInputChange() {
         const selectedDate = bookingDateInput.value;

        if (!selectedDate) {
            updateInputStates(); 
            return;
        }

        if (isDateInPast(selectedDate)) {
            alert('Cannot select past dates.');
            bookingDateInput.value = new Date().toISOString().split('T')[0];
            handleDateInputChange(); 
            return;
        }

         startTimeInput.dataset.previousValue = startTimeInput.value;
         startTimeInput.value = "";

        updateAvailableTimeSlots(); 
        updateInputStates(); 
    }

    // Start time selection change
    function handleStartTimeChange() {
         updateEndTimeDisplay();
    }


    // Form validation before submission
    function validateForm() {
        if (serviceSelections.length === 0) {
            alert('Please select at least one service.');
            return false;
        }
        if (!bookingDateInput.value) {
            alert('Please select a booking date.');
            bookingDateInput.focus();
            return false;
        }
         if (isDateInPast(bookingDateInput.value)) {
             alert('Cannot select a past date.');
             bookingDateInput.focus();
             return false;
         }

        if (!startTimeInput.value) {
            alert('Please select an available start time.');
             startTimeInput.focus();
            return false;
        }

        // Check if every service has a specific stylist selected (not null or empty string)
        if (!serviceSelections.every(s => s.selectedStylistId && s.selectedStylistId !== 'any')) { 
             alert('Please select a stylist for each service.');
             // Find the first offending select and focus it
             const firstInvalidSelect = serviceStylistSelectionsContainer.querySelector('.stylist-select:invalid, .stylist-select option[value=""]:checked');
              if(firstInvalidSelect) firstInvalidSelect.focus();
             return false;
         }

        return true; 
    }


    // Form submission handler
    function handleFormSubmit(event) {
        if (!validateForm()) {
            event.preventDefault(); 
            console.log("Form validation failed.");
        } else {
            console.log("Form validation passed. Submitting...");
        }
    }


    // --- Attach Event Listeners ---
    serviceCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', handleServiceCheckboxChange);
    });

    bookingDateInput.addEventListener('change', handleDateInputChange);
    bookingDateInput.addEventListener('input', handleDateInputChange);


    startTimeInput.addEventListener('change', handleStartTimeChange);

    if (bookingForm) {
        bookingForm.addEventListener('submit', handleFormSubmit);
    }


});
