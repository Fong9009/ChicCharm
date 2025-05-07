/* Booking Functionality */
document.addEventListener('DOMContentLoaded', function() {

    // Configuration 
    const GET_STYLISTS_URL_BASE = apiUrl;
    const GET_TIMESLOTS_URL = apiUrl2;
    const GET_AVAILABILITY_URL = apiUrl3;
    const CSRF_TOKEN = document.querySelector('input[name="_csrfToken"]')?.value; 

    // DOM Elements 
    const serviceCheckboxes = document.querySelectorAll('.service-checkbox');
    const totalCostInput = document.getElementById('total-cost'); 
    const bookingDateInput = document.getElementById('booking-date');
    const serviceStylistSelectionsContainer = document.getElementById('service-stylist-selections'); 
    const serviceCountDisplay = document.getElementById('service-count');
    const serviceTotalDisplay = document.getElementById('service-total');
    const selectedServicesListDisplay = document.getElementById('selected-services-list');
    const bookingForm = document.getElementById('booking-form');
    const closingTimeWarningContainer = document.getElementById('closing-time-warning-container');

    // State 
    let serviceSelections = []; 

    // Initialization 

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

    // Functions

    function getCsrfToken() {
        return CSRF_TOKEN || document.querySelector('input[name="_csrfToken"]')?.value;
    }

    // Set min/max dates and default for new bookings
    function configureDateInput() {
        const today = new Date();
        const todayStr = today.toISOString().split('T')[0];
        if (bookingDateInput) { 
            bookingDateInput.min = todayStr;
            bookingDateInput.max = new Date(today.getFullYear() + 1, today.getMonth(), today.getDate()).toISOString().split('T')[0];
            // If it's a new booking (input is empty and not disabled), set to today
            if (!bookingDateInput.value && !bookingDateInput.disabled) {
                bookingDateInput.value = todayStr;
            }
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
                const serviceIdStr = checkbox.value;
                const details = getServiceDetails(serviceIdStr);
                if (details) {
                    const serviceIdNum = parseInt(details.id, 10);
                    if (isNaN(serviceIdNum)) {
                        console.error("[Init] Invalid service ID encountered:", details.id);
                        return;
                    }
                    serviceSelections.push({
                        serviceId: serviceIdNum,
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
            // Instead, directly populate stylists if date is present
            if (bookingDateInput.value && !bookingDateInput.disabled) {
                populateAllStylistDropdowns(bookingDateInput.value);
            }
        });
        calculateAndUpdateSummary();
    }

    // Update enable/disable state of inputs
    function updateInputStates() {
        const anyServiceCheckboxSelected = serviceSelections.length > 0;
        const isEditModeWithDate = bookingDateInput && bookingDateInput.value !== ''; // Check if date is pre-filled

        // --- Date Input State ---
        // Enable date input if in edit mode with a date OR if a service checkbox is selected
        if (bookingDateInput) { // Ensure bookingDateInput exists
            if (isEditModeWithDate || anyServiceCheckboxSelected) {
                bookingDateInput.disabled = false;
            } else {
                bookingDateInput.disabled = true;
                // bookingDateInput.value = ''; // Don't clear if just no checkbox selected yet but could be edit mode without active services
                // Clear stylist and time selections only if truly no services are selected AND not in edit mode with a date
                if (!anyServiceCheckboxSelected && !isEditModeWithDate && serviceStylistSelectionsContainer) {
                    serviceStylistSelectionsContainer.innerHTML = '';
                }
            }
    
            // If no services are *checked* AND no initial date was set (i.e., not edit mode or edit mode with no date), then clear date & warning
            if (!anyServiceCheckboxSelected && !isEditModeWithDate) {
                bookingDateInput.value = ''; // Safe to clear now
                clearClosingTimeWarning();
            }
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

        // Debugging logs
        console.log(`[calculateAndUpdateSummary] Count: ${serviceSelections.length}, Total Cost: ${totalCost.toFixed(2)}`);
        console.log("[calculateAndUpdateSummary] Elements:", serviceCountDisplay, serviceTotalDisplay, totalCostInput);

        // Update DOM elements
        if (totalCostInput) totalCostInput.value = totalCost.toFixed(2); 
        if (serviceCountDisplay) serviceCountDisplay.textContent = serviceSelections.length;
        if (serviceTotalDisplay) serviceTotalDisplay.textContent = totalCost.toFixed(2);
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
            const container = createServiceRowElements(selection);
            serviceStylistSelectionsContainer.appendChild(container);
        });
    }

    // --- NEW HELPER: Creates DOM elements for a single service row --- 
    function createServiceRowElements(selection) {
        const container = document.createElement('div');
        container.className = 'service-stylist-selection mb-3 p-3 border rounded';
        container.dataset.serviceId = selection.serviceId;

        const label = document.createElement('h6');
        label.textContent = selection.name;
        label.className = 'mb-2 fw-bold';
        container.appendChild(label);

        // --- Stylist Selection ---
        const stylistLabel = document.createElement('label');
        stylistLabel.textContent = 'Stylist';
        stylistLabel.htmlFor = `stylist-select-${selection.serviceId}`;
        stylistLabel.className = 'form-label mt-2';
        container.appendChild(stylistLabel);

        const select = document.createElement('select');
        select.id = `stylist-select-${selection.serviceId}`;
        select.className = 'form-control stylist-select';
        select.name = `bookings_services[${selection.serviceId}][stylist_id]`;
        select.required = true;
        select.dataset.serviceId = selection.serviceId;
        select.setAttribute('oninvalid', "this.setCustomValidity('Please Select a Stylist')");
        select.setAttribute('oninput', "this.setCustomValidity('')");
        select.disabled = true; // Initially disabled until date is selected
        select.innerHTML = '<option value="">Select Date First...</option>';
        container.appendChild(select);
        select.addEventListener('change', handleStylistSelectionChange);

        // Time Selection
        const timeLabel = document.createElement('label');
        timeLabel.textContent = 'Time';
        timeLabel.htmlFor = `time-select-${selection.serviceId}`;
        timeLabel.className = 'form-label mt-2';
        container.appendChild(timeLabel);

        const timeSelect = document.createElement('select');
        timeSelect.id = `time-select-${selection.serviceId}`;
        timeSelect.className = 'form-control time-select mt-1';
        timeSelect.name = `bookings_services[${selection.serviceId}][start_time]`;
        timeSelect.required = true;
        timeSelect.disabled = true; 
        timeSelect.dataset.serviceId = selection.serviceId;
        timeSelect.innerHTML = '<option value="">Select Stylist & Date...</option>';
        timeSelect.setAttribute('oninvalid', "this.setCustomValidity('Please Select a Time Slot')");
        timeSelect.setAttribute('oninput', "this.setCustomValidity('')");
        timeSelect.addEventListener('change', handleTimeSelectionChange);
        container.appendChild(timeSelect);

        // Availability Info 
        const availabilityInfo = document.createElement('div');
        availabilityInfo.className = 'availability-info mt-2 small text-muted';
        availabilityInfo.id = `availability-info-${selection.serviceId}`;
        container.appendChild(availabilityInfo);

        // --- Hidden Inputs ---
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

        return container; 
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
                      // display a generic error message to the user
                 } else {
                      startTimeInput.innerHTML = '<option value="">Error loading time slots</option>';
                 }
                 throw new Error(`HTTP error! Status: ${response.status}`);
             }


            const slots = await response.json();

             // Check for closing time message from backend 
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
            availabilityDiv.className = 'availability-info mt-2 small text-muted';  
            availabilityDiv.id = `availability-info-${serviceId}`;  
            container.appendChild(availabilityDiv);

            // Add the change listener *once* when creating the element
            timeSelect.addEventListener('change', handleTimeSelectionChange);
        }

        // Clear existing availability message before loading new slots
        updateAvailabilityDisplay(serviceId, null);

        timeSelect.disabled = true;
        timeSelect.innerHTML = '<option value="">Loading times...</option>';
        serviceSelections[selectionIndex].availableSlots = [];

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
        const timeSelect = serviceStylistSelectionsContainer.querySelector(`#time-select-${serviceId}`);
        if (!timeSelect) return;

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

            if (preSelectedTime) {
                const existsInAvailable = slotsToRender.some(slot => slot.value === preSelectedTime);

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
        const serviceIdStr = checkbox.value;
        const serviceId = parseInt(serviceIdStr, 10);
        if(isNaN(serviceId)) {
            console.error("Invalid service ID from checkbox:", serviceIdStr);
            return;
        }

        const details = getServiceDetails(serviceId);
        if (!details) return;

        if (checkbox.checked) {
            // 1. Add to state
             const newSelection = {
                 serviceId: serviceId,
                 name: details.name,
                 duration: details.duration,
                 cost: details.cost,
                 selectedStylistId: null,
                 initialStylistId: null, 
                 initialStartTime: null,
                 selectedStartTime: null,
                 availableSlots: []
             };
             serviceSelections.push(newSelection);

            // 2. Create and append the new DOM row
            const newRowElement = createServiceRowElements(newSelection);
            serviceStylistSelectionsContainer.appendChild(newRowElement);

            // 3. If date is already selected, populate stylists for this new row
            if (bookingDateInput.value && !bookingDateInput.disabled) {
                populateAllStylistDropdowns(bookingDateInput.value);
            }

        } else {
            // --- Service Removed ---
            // 1. Remove from state
            serviceSelections = serviceSelections.filter(s => s.serviceId !== serviceId);

            // 2. Remove the corresponding DOM row
            const rowToRemove = serviceStylistSelectionsContainer.querySelector(`.service-stylist-selection[data-service-id="${serviceId}"]`);
            if (rowToRemove) {
            rowToRemove.remove();
            } else {
            console.warn(`[ServiceChange] Could not find DOM row to remove for service ${serviceId}`);
            }

            // 3. Recalculate conflicts
            disableConflictingTimeSlots();
        }
        updateInputStates();
        calculateAndUpdateSummary();
    }

    // Stylist dropdown change
    async function handleStylistSelectionChange(event) {
        const stylistSelect = event.target;
        const serviceId = parseInt(stylistSelect.dataset.serviceId, 10);
        const stylistId = stylistSelect.value ? parseInt(stylistSelect.value, 10) : null;
        const bookingDate = bookingDateInput.value;
        const serviceRow = stylistSelect.closest('.service-stylist-selection');
        const timeSelect = serviceRow.querySelector('.time-select');

        // Update state
        const selection = serviceSelections.find(s => s.serviceId === serviceId);
        if (selection) {
            selection.selectedStylistId = stylistId;
            selection.selectedStartTime = null;  
            selection.availableSlots = []; 
        }

        // Clear and disable the time slot dropdown immediately
        timeSelect.innerHTML = '<option value="">Loading Times...</option>';
        timeSelect.disabled = true;
        timeSelect.value = ''; 

        // Clear availability count
        updateAvailabilityDisplay(serviceId, null); 

        if (stylistId && bookingDate && !isDateInPast(bookingDate)) {
             // Fetch availability count (optional, but good UX)
             fetchAndDisplayAvailabilityCount(serviceId, stylistId, bookingDate);

            // Fetch new time slots for this specific service/stylist/date
            const availableSlots = await fetchAvailableTimeSlotsForService(serviceId, stylistId, bookingDate);

            // Populate the time slot dropdown for this row
            populateTimeSlotDropdown(timeSelect, availableSlots);
        } else {
            // If no stylist selected or date missing/past, keep time disabled
            timeSelect.innerHTML = '<option value="">Select Stylist & Date...</option>';
            timeSelect.disabled = true;
        }
         // Re-run conflict check after potential time slot update
         disableConflictingTimeSlots();
    }

    // Date input change
    async function handleDateInputChange() {
        const selectedDate = bookingDateInput.value;

        // Clear any previous closing time warnings
        clearClosingTimeWarning();

        if (!selectedDate || isDateInPast(selectedDate)) {
            // Disable all stylist and time selects if date is invalid/past
             serviceStylistSelectionsContainer.querySelectorAll('.stylist-select, .time-select').forEach(select => {
                 select.disabled = true;
                 select.innerHTML = `<option value="">${isDateInPast(selectedDate) ? 'Date is in the past' : 'Select Date First...'}</option>`;
                 if (select.classList.contains('time-select')) select.value = ''; 
             });
            // Clear availability counts
             serviceSelections.forEach(sel => updateAvailabilityDisplay(sel.serviceId, null));
            return; 
        }

        // Date is valid and not in the past, proceed to populate stylists
        await populateAllStylistDropdowns(selectedDate);

         // After populating stylists, potentially fetch times ONLY IF a stylist is already selected (e.g. edit mode)
         for (const selection of serviceSelections) {
            const serviceRow = serviceStylistSelectionsContainer.querySelector(`.service-stylist-selection[data-service-id="${selection.serviceId}"]`);
            if (!serviceRow) continue;

            const stylistSelect = serviceRow.querySelector('.stylist-select');
            const timeSelect = serviceRow.querySelector('.time-select');
            const currentStylistId = stylistSelect.value ? parseInt(stylistSelect.value, 10) : null;

            if (currentStylistId && timeSelect) {
                 // Fetch availability count
                 fetchAndDisplayAvailabilityCount(selection.serviceId, currentStylistId, selectedDate);
                 // Fetch time slots
                 const availableSlots = await fetchAvailableTimeSlotsForService(selection.serviceId, currentStylistId, selectedDate);
                 populateTimeSlotDropdown(timeSelect, availableSlots);
                 // Try to restore initial time if applicable
                 if (selection.initialStartTime && selection.selectedStylistId === selection.initialStylistId) {
                     const initialTimeValue = selection.initialStartTime.substring(0, 5); 
                      if (Array.isArray(availableSlots) && availableSlots.some(slot => slot.value === initialTimeValue)) {
                         timeSelect.value = initialTimeValue;
                         selection.selectedStartTime = initialTimeValue; 
                         // Manually trigger change to run conflict checks
                         timeSelect.dispatchEvent(new Event('change'));
                      } else {
                          selection.selectedStartTime = null; 
                      }
                 }

             } else if (timeSelect) {
                 // If no stylist is selected, ensure time is disabled
                 timeSelect.innerHTML = '<option value="">Select Stylist First</option>';
                 timeSelect.disabled = true;
                 timeSelect.value = '';
                 updateAvailabilityDisplay(selection.serviceId, null); 
             }
         }
          // Re-run conflict check after potential time slot updates
          disableConflictingTimeSlots();
    }

    // Populate ALL stylist dropdowns for the currently selected services and date
    async function populateAllStylistDropdowns(date) {
        if (!date || isDateInPast(date)) {
            console.warn("[PopulateStylists] Skipping stylist population: Invalid or past date.");
        return;
    }

        const stylistPromises = serviceSelections.map(selection => getStylistsForService(selection.serviceId));
        const results = await Promise.all(stylistPromises);

        let needsTimeFetching = false; 

        serviceSelections.forEach((selection, index) => {
            const serviceRow = serviceStylistSelectionsContainer.querySelector(`.service-stylist-selection[data-service-id="${selection.serviceId}"]`);
            if (!serviceRow) {
                console.warn(`[PopulateStylists] Row not found for service ${selection.serviceId}, skipping.`);
                return; 
            }

            const select = serviceRow.querySelector('.stylist-select');
            const timeSelect = serviceRow.querySelector('.time-select');

            if (select.value && select.value !== '') {
                // Ensure state matches dropdown, though it should already
                selection.selectedStylistId = parseInt(select.value, 10);
                // Still might need to trigger time fetching if times aren't loaded yet
                if (timeSelect && timeSelect.options.length <= 1 && selection.selectedStylistId) { 
                    needsTimeFetching = true;
                }
                return; 
            }

            const stylists = results[index]; 

            // Preserve the currently selected value OR use initial value
            const previouslySelectedStylist = select.value;
            const initialStylistToSelect = selection.initialStylistId;
            let valueToRestore = null;


            select.innerHTML = ''; 

            if (!stylists || stylists.length === 0) {
                select.innerHTML = '<option value="">No Stylists Available</option>';
                select.disabled = true;
                timeSelect.innerHTML = '<option value="">No Stylists Available</option>';
                timeSelect.disabled = true;
                timeSelect.value = ''; 
                updateAvailabilityDisplay(selection.serviceId, null); 
            } else {
                select.innerHTML = '<option value="">Select Stylist...</option>'; 
                let foundInitialStylist = false;
                stylists.forEach(stylist => {
                    const option = document.createElement('option');
                    option.value = stylist.id;
                    option.textContent = stylist.name;
                    select.appendChild(option);
                    // Check if this stylist matches the previously selected OR the initial one
                    if (stylist.id == previouslySelectedStylist) {
                        valueToRestore = stylist.id;
                    }
                    if (stylist.id == initialStylistToSelect) { 
                        foundInitialStylist = true;
                    }
                });
                select.disabled = false;

                // Prioritize initial value if it exists and is valid
                if (initialStylistToSelect && foundInitialStylist) {
                    valueToRestore = initialStylistToSelect;
                } else if (initialStylistToSelect && !foundInitialStylist) {
                    console.warn(`[PopulateStylists] Service ${selection.serviceId}: Initial stylist ${initialStylistToSelect} is no longer available.`);
                }

                // Restore the selection (either previous or initial)
                if (valueToRestore) {
                    select.value = valueToRestore;
                    selection.selectedStylistId = parseInt(valueToRestore, 10); 
                    needsTimeFetching = true; 
                } else {
                    // If previous/initial selection invalid, reset time slot too
                    timeSelect.innerHTML = '<option value="">Select Stylist First...</option>';
                    timeSelect.disabled = true;
                    timeSelect.value = '';
                    selection.selectedStylistId = null;
                    updateAvailabilityDisplay(selection.serviceId, null);
                }
            }
            // Clear the initial stylist ID now that we've used it
            selection.initialStylistId = null;
        });

        // Now, after all stylists are potentially selected, fetch times if needed
        if (needsTimeFetching) {
            await fetchInitialTimesForSelectedStylists(date);
        }
    }

    // Fetches initial time slots only for services that have a selected stylist
    async function fetchInitialTimesForSelectedStylists(date) {
        for (const selection of serviceSelections) {
            if (selection.selectedStylistId && !selection.selectedStartTime) { 
                // Fetch availability count first
                fetchAndDisplayAvailabilityCount(selection.serviceId, selection.selectedStylistId, date);
                // Fetch the actual time slots
                const availableSlots = await fetchAvailableTimeSlotsForService(selection.serviceId, selection.selectedStylistId, date);
                // Find the dropdown
                const serviceRow = serviceStylistSelectionsContainer.querySelector(`.service-stylist-selection[data-service-id="${selection.serviceId}"]`);
                const timeSelect = serviceRow?.querySelector('.time-select');
                if (timeSelect) {
                populateTimeSlotDropdown(timeSelect, availableSlots, selection.initialStartTime);
                selection.initialStartTime = null;
                }
            }
        }
        // After all times are fetched and potentially pre-selected, apply conflict disabling
        applyInitialConflictDisabling();
    }

    // Populate a specific time slot dropdown
    function populateTimeSlotDropdown(timeSelectElement, slots, initialTime = null) {
         // Preserve the currently selected value if it exists in the new slots
         const previouslySelectedTime = timeSelectElement.value;
         let valueToRestore = null;
         let preselectionSuccessful = false;

         timeSelectElement.innerHTML = ''; 

         console.log(`[populateTimeSlotDropdown for ${timeSelectElement.id}] Final slots to add:`, JSON.stringify(slots));
        if (!slots || slots.length === 0) {
            timeSelectElement.innerHTML = '<option value="">No Available Slots</option>';
            timeSelectElement.disabled = true;
        } else {
            timeSelectElement.innerHTML = '<option value="">Select Time Slot...</option>'; 
            slots.forEach(slot => {
                const option = document.createElement('option');
                option.value = slot.value; 
                option.textContent = slot.text; 
                timeSelectElement.appendChild(option);
                // Check if this slot matches the previously selected one
                if (slot.value === previouslySelectedTime) {
                valueToRestore = slot.value;
                }
            });
            timeSelectElement.disabled = false;


             if (initialTime) {
                const existsInAvailable = slots.some(slot => slot.value === initialTime);

                if (existsInAvailable) {
                    // If the initial time exists in the list, select it
                    timeSelectElement.value = initialTime;
                    valueToRestore = initialTime; 
                    preselectionSuccessful = true;
                } else {
                    // Saved time is NOT in the available list, create a special option for it
                    const savedOption = document.createElement('option');
                    savedOption.value = initialTime;
                    savedOption.textContent = `${formatTimeForDisplay(initialTime)}`;
                    savedOption.style.fontStyle = 'italic';
                    savedOption.style.color = '#6c757d';
                    // Insert it after the "Select Time" placeholder
                    if (timeSelectElement.options[0]) {
                        timeSelectElement.insertBefore(savedOption, timeSelectElement.options[1]);
                    } else {
                        timeSelectElement.appendChild(savedOption);
                    }
                    // Select this newly added option
                    timeSelectElement.value = initialTime;
                    valueToRestore = initialTime; 
                    preselectionSuccessful = true;
                }
            }

            // Restore the previous selection if it's still valid and no initial pre-selection happened
            if (!preselectionSuccessful && valueToRestore) {
            timeSelectElement.value = valueToRestore;
            } else if (!valueToRestore) {
                timeSelectElement.value = ''; 
            }
        }

         // Update state based on the final selected value
         const finalSelectedTime = timeSelectElement.value || null;
         const serviceId = parseInt(timeSelectElement.dataset.serviceId, 10);
         const selectionState = serviceSelections.find(s => s.serviceId === serviceId);
         if(selectionState) {
             selectionState.selectedStartTime = finalSelectedTime;
         }

         // Trigger change event manually if a value was selected/restored, so conflict check runs
         if (timeSelectElement.value) {
             timeSelectElement.dispatchEvent(new Event('change', { bubbles: true }));
         }
    }

    // Handle Time Selection Change (Focus on updating state and conflict checks)
    function handleTimeSelectionChange(event) {

        const timeSelect = event.target;
        const serviceIdStr = timeSelect.dataset.serviceId;
        const selectedTime = timeSelect.value || null;

        const serviceId = parseInt(serviceIdStr, 10);
        if (isNaN(serviceId)) {
            console.error("[handleTimeSelectionChange] Invalid or missing serviceId in dataset:", serviceIdStr);
            return;
        }

        const currentIndex = serviceSelections.findIndex(s => s.serviceId === serviceId);
        if (currentIndex === -1) {
            console.error("[handleTimeSelectionChange] Could not find service in state for ID:", serviceId);
            return;
        }

        serviceSelections[currentIndex].selectedStartTime = selectedTime;
        const currentStylistId = serviceSelections[currentIndex].selectedStylistId;


        if (!currentStylistId) {
            return;
        }

        disableConflictingTimeSlots();
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

            if (occupiedWindows.length === 0) return; 

            // 2. Iterate through ALL services assigned to this stylist
            serviceSelections.forEach((targetSelection) => {
                if (targetSelection.selectedStylistId !== stylistId) return;

                const targetServiceId = targetSelection.serviceId;
                const targetDuration = targetSelection.duration;
                const targetTimeSelect = serviceStylistSelectionsContainer.querySelector(`#time-select-${targetServiceId}`);

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
                }); 
            }); 
        }); 
    }

    // --- NEW FUNCTION: Update the display for availability count ---
    function updateAvailabilityDisplay(serviceId, count) {
        const availabilityDiv = document.getElementById(`availability-info-${serviceId}`);
        if (!availabilityDiv) return; // Exit if placeholder not found

        if (count === null || count === undefined) {
            // Clear message if count is invalid or not yet fetched
            availabilityDiv.innerHTML = '';
            availabilityDiv.className = 'availability-info mt-2 small text-muted'; 
        } else if (count === 0) {
            availabilityDiv.innerHTML = `
                <i class="fas fa-exclamation-circle text-warning"></i> All time slots booked.
            `;
            availabilityDiv.className = 'availability-info mt-2 alert alert-warning p-1'; 
        } else {
            availabilityDiv.innerHTML = `
                <i class="fas fa-check-circle text-success"></i> ${count} time slot${count === 1 ? '' : 's'} available.
            `;
            availabilityDiv.className = 'availability-info mt-2 text-success small'; 
        }
    }
    // --- END NEW FUNCTION ---

    // --- NEW FUNCTION: Fetch and display availability count ---
    async function fetchAndDisplayAvailabilityCount(serviceId, stylistId, date) {
        const availabilityDiv = document.getElementById(`availability-info-${serviceId}`);
        if (!availabilityDiv) return;

        if (!serviceId || !stylistId || !date || isDateInPast(date)) {
            updateAvailabilityDisplay(serviceId, null); 
            return;
        }

        availabilityDiv.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Checking...'; 
        availabilityDiv.className = 'availability-info mt-2 small text-muted';

        const url = `${GET_AVAILABILITY_URL}?service_id=${serviceId}&stylist_id=${stylistId}&date=${date}`;

        try {
            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest', 
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
                updateAvailabilityDisplay(serviceId, null); 
            }
        } catch (error) {
            console.error('Error fetching availability count for service:', serviceId, error);
            if (availabilityDiv) {
                availabilityDiv.innerHTML = '<i class="fas fa-exclamation-triangle text-danger"></i> Error checking availability';
                availabilityDiv.className = 'availability-info mt-2 small text-danger';
            }
        }
    }
    // --- END NEW FUNCTION ---

    // Fetch available time slots for ONE service/stylist/date combination
    async function fetchAvailableTimeSlotsForService(serviceId, stylistId, date) {
        if (!serviceId || !stylistId || !date) {
            console.warn("Skipping time slot fetch: Missing serviceId, stylistId, or date.");
            return []; 
        }

        try {
            const response = await fetch(GET_TIMESLOTS_URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-Token': getCsrfToken()
                },
                body: JSON.stringify({
                    date: date,
                    selected_services: [{ service_id: serviceId, stylist_id: stylistId }]
                })
            });

            if (!response.ok) {
                const errorText = await response.text();
                console.error(`HTTP error fetching time slots for Svc ${serviceId}, Stylist ${stylistId}: ${response.status}`, errorText);
                return []; 
            }

            const slots = await response.json();

            // Update the specific service selection state
            const selection = serviceSelections.find(s => s.serviceId == serviceId);
            if (selection) {
                selection.availableSlots = slots; 
            }

            return slots; 

        } catch (error) {
            console.error('Error fetching time slots for service:', serviceId, 'stylist:', stylistId, error);
            return []; 
        }
    }

    // Disable conflicting time slots
    function disableConflictingTimeSlots() {
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

            // 2. Iterate through ALL services assigned to this stylist
            serviceSelections.forEach((targetSelection) => {
                if (targetSelection.selectedStylistId !== stylistId) return;

                const targetServiceId = targetSelection.serviceId;
                const targetDuration = targetSelection.duration;
                const targetTimeSelect = serviceStylistSelectionsContainer.querySelector(`#time-select-${targetServiceId}`);

                if (!targetTimeSelect || targetDuration <= 0) return;

                // 3. Iterate through each OPTION in the target service's dropdown
                Array.from(targetTimeSelect.options).forEach(option => {
                    if (!option.value) { 
                        option.disabled = false;
                        option.style.color = '';
                        option.textContent = option.textContent.replace(' (Unavailable)', '');
                        return;
                    }
                    const slotStartMin = timeToMinutes(option.value);
                    if (slotStartMin === null) { 
                        option.disabled = true;
                        option.style.color = 'lightgrey';
                        option.textContent = option.textContent.replace(' (Unavailable)', '') + ' (Unavailable)';
                        return;
                    }
                    const slotEndMin = slotStartMin + targetDuration;
                    let conflictsWithOther = false;
                    for (const window of occupiedWindows) {
                        // Check conflict ONLY if the window belongs to a DIFFERENT service
                        if (window.serviceId !== targetServiceId) {
                            // Check for overlap: [slotStartMin, slotEndMin) vs [window.start, window.end)
                            if (slotStartMin < window.end && slotEndMin > window.start) {
                                conflictsWithOther = true;
                                break; 
                            }
                        }
                    }

                    // 5. Disable/Enable the option based on conflict status
                    option.disabled = conflictsWithOther;
                    option.style.color = conflictsWithOther ? 'lightgrey' : '';
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
        }); 
    }
});
