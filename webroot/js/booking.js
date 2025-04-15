/* Customer Booking Functionality */
document.addEventListener('DOMContentLoaded', function() {
    // Get all service checkboxes
    const serviceCheckboxes = document.querySelectorAll('.service-checkbox');
    const totalCostInput = document.getElementById('total-cost');
    const bookingDateInput = document.getElementById('booking-date');
    const startTimeInput = document.getElementById('start-time');
    const endTimeDisplay = document.getElementById('end-time-display');
    const endTimeInput = document.getElementById('end-time');
    const serviceStylistSelections = document.getElementById('service-stylist-selections');

    // Set minimum date to today
    const today = new Date().toISOString().split('T')[0];
    bookingDateInput.min = today;

    // Function to get service duration from data attribute
    function getServiceDuration(serviceId) {
        const checkbox = document.querySelector(`#service-${serviceId}`);
        return parseInt(checkbox.dataset.duration) || 60;
    }

    // Function to get service cost from data attribute
    function getServiceCost(serviceId) {
        const checkbox = document.querySelector(`#service-${serviceId}`);
        return parseFloat(checkbox.dataset.cost) || 0;
    }

    // Function to update end time based on start time and duration
    function updateEndTime(startTime, durationMinutes) {
        if (!startTime) return;

        const [hours, minutes] = startTime.split(':').map(Number);
        const startDate = new Date();
        startDate.setHours(hours, minutes, 0);

        const endDate = new Date(startDate.getTime() + durationMinutes * 60000);
        const endHours = endDate.getHours();
        const endMinutes = endDate.getMinutes();

        // Format the end time in 24-hour format for the hidden input
        const formattedEndTime = `${endHours.toString().padStart(2, '0')}:${endMinutes.toString().padStart(2, '0')}`;
        endTimeInput.value = formattedEndTime;

        // Check if end time exceeds 5 PM (17:00)
        if (endHours > 17 || (endHours === 17 && endMinutes > 0)) {
            endTimeDisplay.value = 'The shop closes at 5 PM';
            endTimeDisplay.style.color = '#856404';
            endTimeDisplay.style.backgroundColor = '#fff3cd';
            endTimeDisplay.style.padding = '0.375rem 0.75rem';
            endTimeDisplay.style.border = '1px solid #ffeeba';
        } else {
            // Format the end time in 12-hour format with AM/PM for display
            let displayHours = endHours % 12;
            displayHours = displayHours === 0 ? 12 : displayHours;
            const ampm = endHours >= 12 ? 'PM' : 'AM';
            const displayEndTime = `${displayHours}:${endMinutes.toString().padStart(2, '0')} ${ampm}`;
            endTimeDisplay.value = displayEndTime;
            endTimeDisplay.style.color = '';
            endTimeDisplay.style.backgroundColor = '';
            endTimeDisplay.style.padding = '';
            endTimeDisplay.style.border = '';
        }
    }

    // Function to get all selected service IDs
    function getSelectedServiceIds() {
        return Array.from(serviceCheckboxes)
            .filter(checkbox => checkbox.checked)
            .map(checkbox => checkbox.value);
    }

    // Function to calculate total cost from selected services
    function calculateTotalCost() {
        let totalCost = 0;
        const selectedServices = getSelectedServiceIds();

        selectedServices.forEach(serviceId => {
            totalCost += getServiceCost(serviceId);
        });

        totalCostInput.value = totalCost.toFixed(2);
        updateEndTime(startTimeInput.value, getServiceDuration(selectedServices[0])); // Update end time when services change

        // Also update remaining cost if it exists
        const remainingCostInput = document.getElementById('remaining-cost');
        if (remainingCostInput) {
            remainingCostInput.value = totalCost.toFixed(2);
        }

        // Update the summary display
        document.getElementById('service-count').textContent = selectedServices.length;
        document.getElementById('service-total').textContent = totalCost.toFixed(2);

        // Update the list of selected services
        const selectedList = document.getElementById('selected-services-list');
        selectedList.innerHTML = '';

        selectedServices.forEach(serviceId => {
            const label = document.querySelector(`label[for="service-${serviceId}"]`).textContent;
            const listItem = document.createElement('div');
            listItem.className = 'selected-service-item';
            listItem.textContent = '• ' + label;
            selectedList.appendChild(listItem);
        });

        // Update input states
        updateInputStates();
    }

    // Function to fetch stylists for a specific service
    async function fetchStylistsForService(serviceId) {
        if (!bookingDateInput.value || !startTimeInput.value) {
            return null;
        }

        const data = {
            service_ids: [serviceId],
            booking_date: bookingDateInput.value,
            start_time: startTimeInput.value,
            end_time: endTimeInput.value
        };

        try {
            const csrfToken = document.querySelector('input[name="_csrfToken"]').value;
            const response = await fetch(apiUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': csrfToken
                },
                body: JSON.stringify(data)
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const result = await response.json();

            // Check if there's a message about shop closing time
            if (result.message) {
                // Create or update the warning message
                let warningDiv = document.getElementById('closing-time-warning');
                if (!warningDiv) {
                    warningDiv = document.createElement('div');
                    warningDiv.id = 'closing-time-warning';
                    warningDiv.className = 'alert alert-warning mt-3';
                    warningDiv.role = 'alert';
                    serviceStylistSelections.parentNode.insertBefore(warningDiv, serviceStylistSelections);
                }
                warningDiv.textContent = result.message;
                return null;
            } else {
                // Remove the warning message if it exists
                const warningDiv = document.getElementById('closing-time-warning');
                if (warningDiv) {
                    warningDiv.remove();
                }
            }

            return result;
        } catch (error) {
            console.error('Error fetching stylists:', error);
            return null;
        }
    }

    // Function to update stylist selections
    async function updateStylistSelections() {
        serviceStylistSelections.innerHTML = '';
        const selectedServices = getSelectedServiceIds();

        if (selectedServices.length === 0 || !bookingDateInput.value || !startTimeInput.value || !endTimeInput.value) {
            return;
        }

        for (const serviceId of selectedServices) {
            const serviceLabel = document.querySelector(`label[for="service-${serviceId}"]`).textContent;
            const container = document.createElement('div');
            container.className = 'service-stylist-selection mb-3';

            const label = document.createElement('h6');
            label.textContent = serviceLabel;
            container.appendChild(label);

            const select = document.createElement('select');
            select.className = 'form-control';
            select.name = `bookings_services[${serviceId}][stylist_id]`;
            select.required = true;

            // Add hidden inputs
            const serviceInput = document.createElement('input');
            serviceInput.type = 'hidden';
            serviceInput.name = `bookings_services[${serviceId}][service_id]`;
            serviceInput.value = serviceId;

            const costInput = document.createElement('input');
            costInput.type = 'hidden';
            costInput.name = `bookings_services[${serviceId}][service_cost]`;
            costInput.value = getServiceCost(serviceId);

            select.innerHTML = '<option value="">Loading available stylists...</option>';
            container.appendChild(select);
            container.appendChild(serviceInput);
            container.appendChild(costInput);
            serviceStylistSelections.appendChild(container);

            // Fetch and populate stylists
            const stylists = await fetchStylistsForService(serviceId);
            if (stylists && Array.isArray(stylists)) {
                select.innerHTML = '<option value="">Select a stylist...</option>';
                stylists.forEach(stylist => {
                    const option = document.createElement('option');
                    option.value = stylist.id;
                    option.textContent = stylist.name;
                    select.appendChild(option);
                });
            } else {
                select.innerHTML = '<option value="">No available services for this stylist at this time</option>';
                select.disabled = true;
            }
        }
    }

    // Function to update stylist IDs container
    function updateStylistIdsContainer() {
        const container = document.getElementById('stylist-ids-container');
        container.innerHTML = '';

        const selects = document.querySelectorAll('.stylist-select');
        selects.forEach((select, index) => {
            const serviceId = select.closest('.service-stylist-selection').dataset.serviceId;
            const stylistId = select.value;

            if (stylistId) {
                // Create hidden input for service_id
                const serviceInput = document.createElement('input');
                serviceInput.type = 'hidden';
                serviceInput.name = `bookings_services[${serviceId}][service_id]`;
                serviceInput.value = serviceId;
                container.appendChild(serviceInput);

                // Create hidden input for stylist_id
                const stylistInput = document.createElement('input');
                stylistInput.type = 'hidden';
                stylistInput.name = `bookings_services[${serviceId}][stylist_id]`;
                stylistInput.value = stylistId;
                container.appendChild(stylistInput);
            }
        });
    }

    // Function to update input states based on service selection
    function updateInputStates() {
        const hasSelectedServices = getSelectedServiceIds().length > 0;
        bookingDateInput.disabled = !hasSelectedServices;
        startTimeInput.disabled = !hasSelectedServices;

        if (!hasSelectedServices) {
            bookingDateInput.value = '';
            startTimeInput.value = '';
            endTimeDisplay.value = '';
            endTimeInput.value = '';
            serviceStylistSelections.innerHTML = '';
            document.getElementById('stylist-ids-container').innerHTML = '';
        }
    }

    // Function to update available time slots
    async function updateAvailableTimeSlots() {
        const selectedServices = getSelectedServiceIds();
        const selectedDate = bookingDateInput.value;

        if (!selectedDate || selectedServices.length === 0) {
            startTimeInput.disabled = true;
            startTimeInput.innerHTML = '<option value="">Please select date and services first</option>';
            return;
        }

        startTimeInput.disabled = true;
        startTimeInput.innerHTML = '<option value="">Loading available times...</option>';

        try {
            const response = await fetch(apiUrl2, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': document.querySelector('input[name="_csrfToken"]').value
                },
                body: JSON.stringify({
                    date: selectedDate,
                    service_ids: selectedServices
                })
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const availableSlots = await response.json();

            startTimeInput.innerHTML = '<option value="">Select a time slot</option>';
            if (availableSlots.length > 0) {
                availableSlots.forEach(slot => {
                    const option = document.createElement('option');
                    option.value = slot.value;
                    option.textContent = slot.display;
                    startTimeInput.appendChild(option);
                });
                startTimeInput.disabled = false;
            } else {
                startTimeInput.innerHTML = '<option value="">No available time slots</option>';
                startTimeInput.disabled = true;
            }
        } catch (error) {
            console.error('Error fetching available time slots:', error);
            startTimeInput.innerHTML = '<option value="">Error loading time slots</option>';
            startTimeInput.disabled = true;
        }
    }

    // Event listeners
    serviceCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', () => {
            const selectedServices = Array.from(serviceCheckboxes)
                .filter(cb => cb.checked);

            // Enable/disable date input
            bookingDateInput.disabled = selectedServices.length === 0;

            // Calculate total duration and cost
            let totalDuration = 0;
            let totalCost = 0;
            selectedServices.forEach(service => {
                totalDuration += getServiceDuration(service.value);
                totalCost += getServiceCost(service.value);
            });

            // Update service count and total
            document.getElementById('service-count').textContent = selectedServices.length;
            document.getElementById('service-total').textContent = totalCost.toFixed(2);

            // Update available time slots
            updateAvailableTimeSlots();

            // Update stylist selections
            updateStylistSelections();
        });
    });

    bookingDateInput.addEventListener('change', () => {
        updateAvailableTimeSlots();
        updateStylistSelections();
    });

    startTimeInput.addEventListener('change', () => {
        if (!startTimeInput.value) {
            endTimeInput.value = '';
            endTimeDisplay.value = '';
            return;
        }

        const selectedServices = Array.from(serviceCheckboxes)
            .filter(cb => cb.checked);
        let totalDuration = 0;
        selectedServices.forEach(service => {
            totalDuration += getServiceDuration(service.value);
        });

        updateEndTime(startTimeInput.value, totalDuration);
        updateStylistSelections();
    });

    // Add event listener for stylist selection changes
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('stylist-select')) {
            updateStylistIdsContainer();
        }
    });

    // Function to validate the form before submission
    function validateForm() {
        const selectedServices = getSelectedServiceIds();
        if (selectedServices.length === 0) {
            alert('Please select at least one service');
            return false;
        }

        if (!bookingDateInput.value) {
            alert('Please select a booking date');
            return false;
        }

        if (!startTimeInput.value) {
            alert('Please select a start time');
            return false;
        }

        // Check if all selected services have a stylist assigned
        const stylistSelects = document.querySelectorAll('.stylist-select');
        for (const select of stylistSelects) {
            if (!select.value) {
                alert('Please select a stylist for all services');
                return false;
            }
        }

        return true;
    }

    // Add form submission handler
    document.querySelector('form').addEventListener('submit', function(e) {
        if (!validateForm()) {
            e.preventDefault();
        }
    });

    // Initialize input states
    updateInputStates();
});
