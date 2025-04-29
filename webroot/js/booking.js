/* Customer Booking Functionality */
document.addEventListener('DOMContentLoaded', function() {
    // --- Configuration ---
    const GET_STYLISTS_URL_BASE = apiUrl;
    const GET_TIMESLOTS_URL = apiUrl2;
    const GET_AVAILABILITY_URL = apiUrl3;
    const CSRF_TOKEN = document.querySelector('input[name="_csrfToken"]')?.value;

    // --- DOM Elements ---
    const serviceCheckboxes = document.querySelectorAll('.service-checkbox');
    const totalCostInput = document.getElementById('total-cost');
    const bookingDateInput = document.getElementById('booking-date');
    const serviceStylistSelectionsContainer = document.getElementById('service-stylist-selections');
    const serviceCountDisplay = document.getElementById('service-count');
    const serviceTotalDisplay = document.getElementById('service-total');
    const selectedServicesListDisplay = document.getElementById('selected-services-list');
    const bookingForm = document.getElementById('booking-form');
    const closingTimeWarningContainer = document.getElementById('closing-time-warning-container');

    // --- State ---
    let serviceSelections = [];

    // --- Initialization ---

    // Helper to parse H:i string to minutes since midnight
    const timeToMinutes = (timeStr) => {
        if (!timeStr) return null;
        const [hours, minutes] = timeStr.split(':').map(Number);
        if (isNaN(hours) || isNaN(minutes)) return null;
        return hours * 60 + minutes;
    };

    // Helper to format HH:MM to H:MM AM/PM
    const formatTimeForDisplay = (timeStr) => {
        if (!timeStr) return "Invalid Time";
        const [hours, minutes] = timeStr.split(':').map(Number);
        if (isNaN(hours) || isNaN(minutes)) return "Invalid Time";
        const ampm = hours >= 12 ? 'PM' : 'AM';
        const hours12 = hours % 12 || 12;
        return `${hours12}:${minutes.toString().padStart(2, '0')} ${ampm}`;
    };

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
            initialStylistId: checkbox.dataset.selectedStylistId || null,
            initialStartTime: checkbox.dataset.selectedStartTime || null
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
                        selectedStylistId: details.initialStylistId ? parseInt(details.initialStylistId, 10) : null,
                        initialStylistId: details.initialStylistId ? parseInt(details.initialStylistId, 10) : null,
                        initialStartTime: details.initialStartTime || null,
                        selectedStartTime: null,
                        availableSlots: []
                    });
                }
            }
        });
        // Initial UI setup based on loaded state
        updateInputStates();
        renderServiceStylistSelections().then(() => {
            if (bookingDateInput.value) {
                handleDateInputChange();
            }
        });
        calculateAndUpdateSummary();
        // Apply initial conflict disabling after everything is loaded
        applyInitialConflictDisabling();
    }

    // Update enable/disable state of inputs
    function updateInputStates() {
        // CHANGE: Enable date picker if AT LEAST ONE service is selected
        const anyServiceSelected = serviceSelections.length > 0;

        // --- Date Input State ---
        if (anyServiceSelected) {
            bookingDateInput.disabled = false;
        } else {
            bookingDateInput.disabled = true;
            bookingDateInput.value = '';
            // Also clear stylist dropdowns and time slots if no services are selected
            serviceStylistSelectionsContainer.innerHTML = '';
        }

        // If no services are selected at all, reset date input (redundant now, but safe)
        if (serviceSelections.length === 0) {
            bookingDateInput.disabled = true;
            bookingDateInput.value = '';
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
        serviceStylistSelectionsContainer.innerHTML = '';

        if (serviceSelections.length === 0) return;

        serviceSelections.forEach((selection, index) => {
            const container = document.createElement('div');
            container.className = 'service-stylist-selection mb-3 p-3 border rounded';
            container.dataset.serviceId = selection.serviceId;

            const label = document.createElement('h6');
            label.textContent = selection.name;
            label.className = 'mb-2';
            container.appendChild(label);

            // Stylist Select Dropdown (initially empty)
            const select = document.createElement('select');
            select.className = 'form-control stylist-select';
            select.name = `bookings_services[${selection.serviceId}][stylist_id]`;
            select.required = true;
            select.dataset.serviceId = selection.serviceId;
            select.setAttribute('oninvalid', "this.setCustomValidity('Please Select a Stylist')");
            select.setAttribute('oninput', "this.setCustomValidity('')");
            select.disabled = true;

            // Options - Placeholder only
            select.innerHTML = '<option value="">Select Date First...</option>';

            container.appendChild(select);

             // Add listener to update state and fetch time slots on change
             select.addEventListener('change', handleStylistSelectionChange);

            // Hidden Inputs
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
    }


    // Fetch available time slots based on current selections
    async function updateAvailableTimeSlots() {
        clearClosingTimeWarning();

        // Conditions required to fetch time slots - ensure specific stylist ID is selected and date is set
        const allStylistsSelected = serviceSelections.every(s => s.selectedStylistId && s.selectedStylistId !== 'any');
        if (!bookingDateInput.value || serviceSelections.length === 0 || !allStylistsSelected) {
            return;
        }

        startTimeInput.disabled = true;
        startTimeInput.innerHTML = '<option value="">Loading available times...</option>';

        // Data sent must contain only integer stylist IDs
        const dataToSend = {
            date: bookingDateInput.value,
            selected_services: serviceSelections.map(s => ({
                service_id: parseInt(s.serviceId, 10),
                stylist_id: parseInt(s.selectedStylistId, 10)
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
        }
    }

    // Fetch and render time slots for a specific service
    async function updateAvailableTimeSlotsForService(serviceId, stylistId, date) {
        const container = serviceStylistSelectionsContainer.querySelector(`.service-stylist-selection[data-service-id="${serviceId}"]`);
        let timeSelect = container.querySelector('.service-time-select');

        // Find the corresponding service selection in our state array
        const selectionIndex = serviceSelections.findIndex(s => s.serviceId === serviceId);
        if (selectionIndex === -1) {
            console.error("Cannot find service selection in state for ID:", serviceId);
            return;
        }

        // Create the time select dropdown if it doesn't exist
        if (!timeSelect) {
            const label = document.createElement('label');
            label.textContent = 'Start Time';
            label.htmlFor = `service-time-${serviceId}`;
            label.className = 'mt-2';
            container.appendChild(label);

            timeSelect = document.createElement('select');
            timeSelect.id = `service-time-${serviceId}`;
            timeSelect.className = 'form-control service-time-select';
            timeSelect.name = `bookings_services[${serviceId}][start_time]`;
            timeSelect.required = true;
            timeSelect.dataset.serviceId = serviceId;
            timeSelect.setAttribute('oninvalid', "this.setCustomValidity('Please Select a Time Slot')");
            timeSelect.setAttribute('oninput', "this.setCustomValidity('')");
            container.appendChild(timeSelect);

            // Add the availability info placeholder *after* the time select
            const availabilityDiv = document.createElement('div');
            availabilityDiv.className = 'availability-info mt-2 small text-muted'; // Start muted
            availabilityDiv.id = `availability-info-${serviceId}`; // Unique ID for this service
            container.appendChild(availabilityDiv);

            // Add the change listener *once* when creating the element
            timeSelect.addEventListener('change', handleTimeSelectionChange);
        }

        // Clear existing availability message before loading new slots
        updateAvailabilityDisplay(serviceId, null);

        timeSelect.disabled = true;
        timeSelect.innerHTML = '<option value="">Loading times...</option>';
        serviceSelections[selectionIndex].availableSlots = [];
        // REMOVE: Don't reset selectedStartTime here
        // serviceSelections[selectionIndex].selectedStartTime = null;

        const dataToSend = {
            date: date,
            selected_services: [{ service_id: parseInt(serviceId, 10), stylist_id: parseInt(stylistId, 10) }]
        };

        try {
            const resp = await fetch(GET_TIMESLOTS_URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-Token': getCsrfToken()
                },
                body: JSON.stringify(dataToSend)
            });
            const slots = await resp.json();

            // STORE the full list of slots in the state
            serviceSelections[selectionIndex].availableSlots = slots;

            // Render the initial (unfiltered) slots
            renderTimeSlotOptions(serviceId, slots);

        } catch (e) {
            console.error('Error loading slots for service', serviceId, e);
            timeSelect.innerHTML = '<option value="">Error loading times</option>';
            serviceSelections[selectionIndex].availableSlots = [];
        }
    }

    // Helper function to render options into a time select dropdown
    function renderTimeSlotOptions(serviceId, slotsToRender) {
        const timeSelect = serviceStylistSelectionsContainer.querySelector(`#service-time-${serviceId}`);
        if (!timeSelect) return;

        console.log(`Rendering time slots for Service ${serviceId}. Available slots from backend:`, JSON.stringify(slotsToRender)); // Log available slots

        const currentVal = timeSelect.value;

        timeSelect.innerHTML = '<option value="">Select Time</option>';
        if (!Array.isArray(slotsToRender) || slotsToRender.length === 0) {
            timeSelect.innerHTML = '<option value="">No Available Times</option>';
            timeSelect.disabled = true;
        } else {
            slotsToRender.forEach(slot => {
                const opt = document.createElement('option');
                opt.value = slot.value;
                opt.textContent = slot.text;
                timeSelect.appendChild(opt);
            });
            timeSelect.disabled = false;

            // --- EDIT MODE PRE-SELECTION --- //
            const selectionState = serviceSelections.find(s => s.serviceId === serviceId);
            const preSelectedTime = selectionState ? selectionState.initialStartTime : null;
            let preselectionSuccessful = false;

            console.log(`Service ${serviceId} - Checking pre-selection. InitialStartTime from state:`, preSelectedTime);

            if (preSelectedTime) {
                const existsInAvailable = slotsToRender.some(slot => slot.value === preSelectedTime);
                console.log(`Service ${serviceId} - Does initialStartTime (${preSelectedTime}) exist in available slots?`, existsInAvailable);

                if (existsInAvailable) {
                    // If we have a pre-selected time from the state/HTML and it exists in the list, select it
                    timeSelect.value = preSelectedTime;
                    preselectionSuccessful = true;
                } else {
                    // Saved time is NOT in the available list, create a special option for it
                    const savedOption = document.createElement('option');
                    savedOption.value = preSelectedTime;
                    savedOption.textContent = `${formatTimeForDisplay(preSelectedTime)}`;
                    // Insert it after the "Select Time" placeholder
                    if (timeSelect.options[0]) {
                         timeSelect.insertBefore(savedOption, timeSelect.options[1]);
                    } else { // Should not happen if placeholder exists
                         timeSelect.appendChild(savedOption);
                    }
                    // Select this newly added option
                    timeSelect.value = preSelectedTime;
                    preselectionSuccessful = true;
                }
            }

            // Update state based on pre-selection outcome
            if (selectionState) {
                if (preselectionSuccessful) {
                    selectionState.selectedStartTime = preSelectedTime;
                } else {
                    timeSelect.value = ''; // Reset dropdown if no pre-selection happened
                    selectionState.selectedStartTime = null;
                }
                // Clear the initial time now that we've used it for pre-selection attempt
                selectionState.initialStartTime = null;
            }
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
            serviceSelections.push({
                serviceId: details.id,
                name: details.name,
                duration: details.duration,
                cost: details.cost,
                selectedStylistId: null,
                selectedStartTime: null,
                availableSlots: [],
                initialStylistId: details.initialStylistId ? parseInt(details.initialStylistId, 10) : null,
                initialStartTime: details.initialStartTime || null
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

        // Reset Date and Stylist/Time selections if services change and at least one service remains selected
        if (serviceSelections.length > 0) {
            if (bookingDateInput.value) {
                bookingDateInput.value = '';
                 console.log("Services changed, resetting date and stylist/time selections.");
                 // Clear stylist/time slots visually
                serviceStylistSelectionsContainer.innerHTML = '';
                 // Need to re-render the containers for stylists
                 renderServiceStylistSelections();
            }
        } else {
             serviceStylistSelectionsContainer.innerHTML = '';
        }
    }

    // Stylist dropdown change
    function handleStylistSelectionChange(event) {
        const stylistSelect = event.target;
        const serviceId = stylistSelect.dataset.serviceId;
        const stylistId = stylistSelect.value; // This will be the selected stylist ID or ""
        const date = bookingDateInput.value;

        console.log(`Stylist changed for service ${serviceId}: ${stylistId}`);

        const selectionIndex = serviceSelections.findIndex(s => s.serviceId == serviceId);
        if (selectionIndex !== -1) {
            serviceSelections[selectionIndex].selectedStylistId = stylistId ? parseInt(stylistId, 10) : null;
            serviceSelections[selectionIndex].selectedStartTime = null; // Reset time when stylist changes

            // Render time slots for the *newly selected* stylist
            updateAvailableTimeSlotsForService(serviceId, stylistId, date);

            // Fetch availability count for this service/stylist/date
            fetchAndDisplayAvailabilityCount(serviceId, stylistId, date);

        } else {
            console.error("Could not find service selection state for ID:", serviceId);
             // Clear availability if state is inconsistent
            updateAvailabilityDisplay(serviceId, null);
        }
    }

    // Date input change
    async function handleDateInputChange() {
        const selectedDate = bookingDateInput.value;
        console.log("Date changed:", selectedDate);

        if (!selectedDate || isDateInPast(selectedDate)) {
            // Clear all stylist dropdowns and time slots if date is invalid or past
            serviceStylistSelectionsContainer.querySelectorAll('.stylist-select').forEach(select => {
                select.innerHTML = '<option value="">Select Date First...</option>';
                select.disabled = true;
            });
            serviceStylistSelectionsContainer.querySelectorAll('.time-slot-container').forEach(container => container.innerHTML = '');
             // **NEW**: Clear all availability displays
            serviceSelections.forEach(selection => {
                updateAvailabilityDisplay(selection.serviceId, null);
            });
            return;
        }

        clearClosingTimeWarning(); // Clear any previous warnings

        // Enable all stylist selects now that a valid date is chosen
        serviceStylistSelectionsContainer.querySelectorAll('.stylist-select').forEach(select => {
            select.disabled = false;
            // Keep existing selection if possible, otherwise show placeholder
            if (!select.value) {
                 select.innerHTML = '<option value="">Select Stylist...</option>';
            }
        });

        // Fetch stylists AND time slots for ALL selected services concurrently
        const updatePromises = serviceSelections.map(async (selection) => {
            const select = serviceStylistSelectionsContainer.querySelector(`.stylist-select[data-service-id="${selection.serviceId}"]`);
            if (!select) return; // Skip if element not found

            // 1. Fetch and populate stylists
            const stylists = await getStylistsForService(selection.serviceId);
            select.innerHTML = '<option value="">Select Stylist...</option>'; // Reset options
            stylists.forEach(stylist => {
                const option = document.createElement('option');
                option.value = stylist.id;
                option.textContent = stylist.name;
                // Re-select previous stylist if still valid
                if (selection.selectedStylistId && stylist.id === selection.selectedStylistId) {
                    option.selected = true;
                }
                select.appendChild(option);
            });
             // Ensure state matches the potentially re-selected stylist in the dropdown
            selection.selectedStylistId = select.value ? parseInt(select.value, 10) : null;


            // 2. Fetch Time Slots (if stylist is selected)
            if (selection.selectedStylistId) {
                await updateAvailableTimeSlotsForService(selection.serviceId, selection.selectedStylistId, selectedDate);
                 // **NEW**: Fetch availability count after potentially getting stylists/slots
                await fetchAndDisplayAvailabilityCount(selection.serviceId, selection.selectedStylistId, selectedDate);
            } else {
                 // **NEW**: Clear availability if no stylist selected
                 updateAvailabilityDisplay(selection.serviceId, null);
                 // Clear time slots if no stylist is selected after fetching
                 const timeSlotsContainer = document.getElementById(`time-slots-${selection.serviceId}`);
                 if (timeSlotsContainer) timeSlotsContainer.innerHTML = '';
            }
        });

        await Promise.all(updatePromises);
        console.log("Finished updating stylists and time slots after date change.");

        // Apply conflicts after all updates are done
        // recalculateAndApplyConflicts();
    }

    // Time selection change for a specific service
    function handleTimeSelectionChange(event) {
        const timeSelect = event.target;
        const serviceId = timeSelect.dataset.serviceId;
        const selectedTime = timeSelect.value || null;

        const currentIndex = serviceSelections.findIndex(s => s.serviceId === serviceId);
        if (currentIndex === -1) return;

        // Update the state for the service that changed
        serviceSelections[currentIndex].selectedStartTime = selectedTime;
        const currentStylistId = serviceSelections[currentIndex].selectedStylistId;

        if (!currentStylistId) return;

        // Helper to parse H:i string to minutes since midnight
        const timeToMinutes = (timeStr) => {
            if (!timeStr) return null;
            const [hours, minutes] = timeStr.split(':').map(Number);
            if (isNaN(hours) || isNaN(minutes)) return null;
            return hours * 60 + minutes;
        };

        // --- Recalculate and apply disabled state based on ALL current selections for this stylist ---

        // 1. Find all currently selected time windows for this stylist
        const occupiedWindows = [];
        serviceSelections.forEach((sel, index) => {
            if (sel.selectedStylistId === currentStylistId && sel.selectedStartTime) {
                const startMin = timeToMinutes(sel.selectedStartTime);
                if (startMin !== null && sel.duration > 0) {
                    occupiedWindows.push({
                        serviceId: sel.serviceId, // Keep track of which service occupies the window
                        start: startMin,
                        end: startMin + sel.duration
                    });
                }
            }
        });

        // 2. Iterate through ALL services assigned to this stylist
        serviceSelections.forEach((targetSelection) => {
            if (targetSelection.selectedStylistId !== currentStylistId) {
                return; // Skip services with different stylists
            }

            const targetServiceId = targetSelection.serviceId;
            const targetDuration = targetSelection.duration;
            const targetTimeSelect = serviceStylistSelectionsContainer.querySelector(`#service-time-${targetServiceId}`);

            if (!targetTimeSelect || targetDuration <= 0) {
                return; // Skip if dropdown doesn't exist or service has no duration
            }

            // 3. Iterate through each OPTION in the target service's dropdown
            Array.from(targetTimeSelect.options).forEach(option => {
                if (!option.value) { // Skip the placeholder "Select Time"
                    option.disabled = false;
                    option.style.color = '';
                    option.textContent = option.textContent.replace(' (Unavailable)', '');
                    return;
                }

                const slotStartMin = timeToMinutes(option.value);
                if (slotStartMin === null) { // Skip invalid option values
                    option.disabled = true;
                    option.style.color = 'lightgrey';
                    option.textContent = option.textContent.replace(' (Unavailable)', '') + ' (Unavailable)';
                    return;
                }
                const slotEndMin = slotStartMin + targetDuration;

                // 4. Check if this slot conflicts with ANY window occupied by OTHER services
                let conflictsWithOther = false;
                for (const window of occupiedWindows) {
                    // IMPORTANT: Check conflict ONLY if the window belongs to a DIFFERENT service
                    if (window.serviceId !== targetServiceId) {
                        // Check for overlap: [slotStartMin, slotEndMin) vs [window.start, window.end)
                        if (slotStartMin < window.end && slotEndMin > window.start) {
                            conflictsWithOther = true;
                            break; // One conflict is enough
                        }
                    }
                }

                // 5. Disable the option if it conflicts with another service's selection
                option.disabled = conflictsWithOther;
                option.style.color = conflictsWithOther ? 'lightgrey' : '';
                 // Add/Remove visual cue for disabled options
                 if(conflictsWithOther) {
                     // Ensure we don't add the text multiple times
                     if (!option.textContent.includes(' (Unavailable)')) {
                         option.textContent += ' (Unavailable)';
                     }
                 } else {
                      option.textContent = option.textContent.replace(' (Unavailable)', '');
                 }
            });
        });
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

        // Check if every service has a specific stylist selected (not null or empty string)
        if (!serviceSelections.every(s => s.selectedStylistId && s.selectedStylistId !== 'any')) {
             alert('Please select a stylist for each service.');
             // Find the first offending select and focus it
             const firstInvalidStylistSelect = serviceStylistSelectionsContainer.querySelector('.stylist-select:invalid, .stylist-select option[value=""]:checked');
              if(firstInvalidStylistSelect) firstInvalidStylistSelect.focus();
             return false;
         }

        // Check if every service has a start time selected
        let allTimesSelected = true;
        serviceStylistSelectionsContainer.querySelectorAll('.service-time-select').forEach(timeSelect => {
            if (!timeSelect.value) {
                allTimesSelected = false;
                timeSelect.focus();
            }
        });
        if (!allTimesSelected) {
            alert('Please select a start time for each service.');
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

    if (bookingForm) {
        bookingForm.addEventListener('submit', handleFormSubmit);
    }

    // --- Helper Functions ---

    // Function to apply disabling logic based on current state
    function applyInitialConflictDisabling() {
        console.log("Applying initial conflict disabling...");
        const stylistsInBooking = new Set();
        serviceSelections.forEach(sel => {
            if (sel.selectedStylistId) {
                stylistsInBooking.add(sel.selectedStylistId);
            }
        });

        stylistsInBooking.forEach(stylistId => {
            // 1. Find all currently selected time windows for this stylist
            const occupiedWindows = [];
            serviceSelections.forEach((sel) => {
                if (sel.selectedStylistId === stylistId && sel.selectedStartTime) {
                    const startMin = timeToMinutes(sel.selectedStartTime);
                    if (startMin !== null && sel.duration > 0) {
                        occupiedWindows.push({
                            serviceId: sel.serviceId,
                            start: startMin,
                            end: startMin + sel.duration
                        });
                    }
                }
            });

            if (occupiedWindows.length === 0) return; // No conflicts if nothing selected for this stylist

            // 2. Iterate through ALL services assigned to this stylist
            serviceSelections.forEach((targetSelection) => {
                if (targetSelection.selectedStylistId !== stylistId) return;

                const targetServiceId = targetSelection.serviceId;
                const targetDuration = targetSelection.duration;
                const targetTimeSelect = serviceStylistSelectionsContainer.querySelector(`#service-time-${targetServiceId}`);

                if (!targetTimeSelect || targetDuration <= 0) return;

                // 3. Iterate through each OPTION
                Array.from(targetTimeSelect.options).forEach(option => {
                    if (!option.value) { option.disabled = false; option.style.color = ''; option.textContent = option.textContent.replace(' (Unavailable)', ''); return; }
                    const slotStartMin = timeToMinutes(option.value);
                    if (slotStartMin === null) { option.disabled = true; option.style.color = 'lightgrey'; option.textContent = option.textContent.replace(' (Unavailable)', '') + ' (Unavailable)'; return; }
                    const slotEndMin = slotStartMin + targetDuration;
                    let conflictsWithOther = false;
                    for (const window of occupiedWindows) {
                        if (window.serviceId !== targetServiceId) {
                            if (slotStartMin < window.end && slotEndMin > window.start) {
                                conflictsWithOther = true;
                                break;
                            }
                        }
                    }
                    option.disabled = conflictsWithOther;
                    option.style.color = conflictsWithOther ? 'lightgrey' : '';
                    if(conflictsWithOther) {
                        if (!option.textContent.includes(' (Unavailable)')) { option.textContent += ' (Unavailable)'; }
                    } else {
                         option.textContent = option.textContent.replace(' (Unavailable)', '');
                    }
                }); // End option loop
            }); // End targetSelection loop
        }); // End stylist loop
    }

    // --- NEW FUNCTION: Update the display for availability count ---
    function updateAvailabilityDisplay(serviceId, count) {
        const availabilityDiv = document.getElementById(`availability-info-${serviceId}`);
        if (!availabilityDiv) return; // Exit if placeholder not found

        if (count === null || count === undefined) {
            // Clear message if count is invalid or not yet fetched
            availabilityDiv.innerHTML = '';
            availabilityDiv.className = 'availability-info mt-2 small text-muted'; // Reset class
        } else if (count === 0) {
            availabilityDiv.innerHTML = `
                <i class="fas fa-exclamation-circle text-warning"></i> All time slots booked.
            `;
            availabilityDiv.className = 'availability-info mt-2 alert alert-warning p-1'; // Warning style
        } else {
            availabilityDiv.innerHTML = `
                <i class="fas fa-check-circle text-success"></i> ${count} time slot${count === 1 ? '' : 's'} available.
            `;
            availabilityDiv.className = 'availability-info mt-2 text-success small'; // Success style
        }
    }
    // --- END NEW FUNCTION ---

    // --- NEW FUNCTION: Fetch and display availability count ---
    async function fetchAndDisplayAvailabilityCount(serviceId, stylistId, date) {
        const availabilityDiv = document.getElementById(`availability-info-${serviceId}`);
        if (!availabilityDiv) return;

        if (!serviceId || !stylistId || !date || isDateInPast(date)) {
            updateAvailabilityDisplay(serviceId, null); // Clear if data is incomplete or date is past
            return;
        }

        availabilityDiv.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Checking...'; // Loading indicator
        availabilityDiv.className = 'availability-info mt-2 small text-muted';

        const url = `${GET_AVAILABILITY_URL}?service_id=${serviceId}&stylist_id=${stylistId}&date=${date}`;

        try {
            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest', // Important for CakePHP AJAX detection
                    'X-CSRF-Token': getCsrfToken()
                }
            });

            if (!response.ok) {
                const errorText = await response.text();
                console.error(`HTTP error fetching availability count for service ${serviceId}: ${response.status}`, errorText);
                throw new Error(`HTTP error! Status: ${response.status}`);
            }

            const data = await response.json();
            if (data.availableSlotsCount !== undefined) {
                updateAvailabilityDisplay(serviceId, data.availableSlotsCount);
            } else {
                console.error('Invalid response format from getAvailabilityCount:', data);
                updateAvailabilityDisplay(serviceId, null); // Clear on bad response
            }
        } catch (error) {
            console.error('Error fetching availability count for service:', serviceId, error);
            if (availabilityDiv) {
                availabilityDiv.innerHTML = '<i class="fas fa-exclamation-triangle text-danger"></i> Error checking availability';
                availabilityDiv.className = 'availability-info mt-2 small text-danger';
            }
             // updateAvailabilityDisplay(serviceId, null); // Optionally clear on error
        }
    }
    // --- END NEW FUNCTION ---

});
