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

    // Function to calculate end time based on start time and service durations
    function calculateEndTime() {
        const startTime = startTimeInput.value;
        if (!startTime) return;

        const [hours, minutes] = startTime.split(':').map(Number);
        let totalMinutes = 0;

        // Calculate total duration from selected services
        serviceCheckboxes.forEach(checkbox => {
            if (checkbox.checked) {
                totalMinutes += getServiceDuration(checkbox.value);
            }
        });

        // Calculate end time
        let endHours = hours + Math.floor(totalMinutes / 60);
        let endMinutes = minutes + (totalMinutes % 60);

        // Handle overflow of minutes
        if (endMinutes >= 60) {
            endHours += Math.floor(endMinutes / 60);
            endMinutes = endMinutes % 60;
        }

        // Format the end time in 24-hour format for the hidden input
        const formattedEndTime = `${endHours.toString().padStart(2, '0')}:${endMinutes.toString().padStart(2, '0')}`;
        endTimeInput.value = formattedEndTime;

        // Format the end time in 12-hour format with AM/PM for display
        let displayHours = endHours % 12;
        displayHours = displayHours === 0 ? 12 : displayHours; // Convert 0 to 12 for 12 AM
        const ampm = endHours >= 12 ? 'PM' : 'AM';
        const displayEndTime = `${displayHours}:${endMinutes.toString().padStart(2, '0')} ${ampm}`;
        endTimeDisplay.value = displayEndTime;
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
        calculateEndTime(); // Update end time when services change

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

    // Function to create stylist selection for a service
    async function createStylistSelection(serviceId, serviceName) {
        const container = document.createElement('div');
        container.className = 'service-stylist-selection mb-3';
        container.dataset.serviceId = serviceId;

        const label = document.createElement('label');
        label.className = 'form-label';
        label.textContent = `Select stylist for ${serviceName}`;

        const select = document.createElement('select');
        select.className = 'form-control stylist-select';
        select.name = `bookings_services[${serviceId}][stylist_id]`;
        select.required = true;

        // Add hidden input for service cost
        const costInput = document.createElement('input');
        costInput.type = 'hidden';
        costInput.name = `bookings_services[${serviceId}][service_cost]`;
        costInput.value = getServiceCost(serviceId);

        // Add hidden input for service_id
        const serviceInput = document.createElement('input');
        serviceInput.type = 'hidden';
        serviceInput.name = `bookings_services[${serviceId}][service_id]`;
        serviceInput.value = serviceId;

        const loadingSpinner = document.createElement('div');
        loadingSpinner.className = 'spinner-border spinner-border-sm ms-2 d-none';
        loadingSpinner.setAttribute('role', 'status');
        loadingSpinner.innerHTML = '<span class="visually-hidden">Loading...</span>';

        const errorMessage = document.createElement('div');
        errorMessage.className = 'invalid-feedback d-none';
        errorMessage.textContent = 'Error loading stylists. Please try again.';

        container.appendChild(label);
        container.appendChild(select);
        container.appendChild(costInput);
        container.appendChild(serviceInput);
        container.appendChild(loadingSpinner);
        container.appendChild(errorMessage);

        // Check if date and time are selected
        if (!bookingDateInput.value || !startTimeInput.value) {
            select.disabled = true;
            select.innerHTML = '<option value="">Please select date and time first</option>';
            return container;
        }

        await updateStylistOptions(select, serviceId, loadingSpinner, errorMessage);
        return container;
    }

    // Function to update stylist options
    async function updateStylistOptions(select, serviceId, loadingSpinner, errorMessage) {
        select.disabled = true;
        select.innerHTML = '<option value="">Loading available stylists...</option>';
        loadingSpinner.classList.remove('d-none');
        errorMessage.classList.add('d-none');

        try {
            const stylists = await fetchStylistsForService(serviceId);
            if (stylists && stylists.length > 0) {
                select.disabled = false;
                select.innerHTML = '<option value="">Select a stylist...</option>';
                stylists.forEach(stylist => {
                    const option = document.createElement('option');
                    option.value = stylist.id;
                    option.textContent = stylist.name;
                    select.appendChild(option);
                });
            } else {
                select.disabled = true;
                select.innerHTML = '<option value="">No available stylists for this service</option>';
            }
        } catch (error) {
            console.error('Error fetching stylists:', error);
            select.disabled = true;
            select.innerHTML = '<option value="">Error loading stylists</option>';
            errorMessage.classList.remove('d-none');
        } finally {
            loadingSpinner.classList.add('d-none');
        }
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
            const url = apiUrl;
            const response = await fetch(url, {
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
            return result.error ? null : result;
        } catch (error) {
            console.error('Error fetching stylists:', error);
            return null;
        }
    }

    // Function to update stylist selections when services change
    async function updateStylistSelections(onlyUpdateOptions = false) {
        if (!onlyUpdateOptions) {
            serviceStylistSelections.innerHTML = '';
            const selectedServices = getSelectedServiceIds();

            for (const serviceId of selectedServices) {
                const serviceLabel = document.querySelector(`label[for="service-${serviceId}"]`).textContent;
                const selection = await createStylistSelection(serviceId, serviceLabel);
                serviceStylistSelections.appendChild(selection);
            }
        } else {
            const selections = document.querySelectorAll('.service-stylist-selection');
            for (const selection of selections) {
                const serviceId = selection.dataset.serviceId;
                const select = selection.querySelector('select');
                const loadingSpinner = selection.querySelector('.spinner-border');
                const errorMessage = selection.querySelector('.invalid-feedback');

                if (!bookingDateInput.value || !startTimeInput.value) {
                    select.disabled = true;
                    select.innerHTML = '<option value="">Please select date and time first</option>';
                    continue;
                }

                await updateStylistOptions(select, serviceId, loadingSpinner, errorMessage);
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

    // Event listeners
    serviceCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', () => {
            calculateTotalCost();
            updateInputStates();
            updateStylistSelections(false);
        });
    });

    startTimeInput.addEventListener('change', () => {
        calculateEndTime();
        updateStylistSelections(true);
    });

    bookingDateInput.addEventListener('change', () => {
        updateStylistSelections(true);
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
