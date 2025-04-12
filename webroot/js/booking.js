/* Customer Booking Functionality */
document.addEventListener('DOMContentLoaded', function() {
    // Get all service checkboxes
    const serviceCheckboxes = document.querySelectorAll('.service-checkbox');
    const totalCostInput = document.getElementById('total-cost');
    const bookingDateInput = document.getElementById('booking-date');
    const startTimeInput = document.getElementById('start-time');
    const endTimeInput = document.getElementById('end-time');
    const serviceStylistSelections = document.getElementById('service-stylist-selections');

    // Set minimum date to today
    const today = new Date().toISOString().split('T')[0];
    bookingDateInput.min = today;

    // Function to get all selected service IDs
    function getSelectedServiceIds() {
        return Array.from(serviceCheckboxes)
            .filter(checkbox => checkbox.checked)
            .map(checkbox => checkbox.value);
    }

    // Function to calculate total cost from selected services
    function calculateTotalCost() {
        let totalCost = 0;
        
        // Loop through all checked service checkboxes
        serviceCheckboxes.forEach(checkbox => {
            if (checkbox.checked) {
                const label = document.querySelector(`label[for="${checkbox.id}"]`);
                const costMatch = label.textContent.match(/\$([\d.]+)/);
                if (costMatch) {
                    totalCost += parseFloat(costMatch[1]);
                }
            }
        });
        
        totalCostInput.value = totalCost.toFixed(2);
        
        // Also update remaining cost if it exists
        const remainingCostInput = document.getElementById('remaining-cost');
        if (remainingCostInput) {
            remainingCostInput.value = totalCost.toFixed(2);
        }

        // Update the summary display
        const selectedCount = getSelectedServiceIds().length;
        document.getElementById('service-count').textContent = selectedCount;
        document.getElementById('service-total').textContent = totalCost.toFixed(2);
        
        // Update the list of selected services
        const selectedList = document.getElementById('selected-services-list');
        selectedList.innerHTML = '';
        
        serviceCheckboxes.forEach(checkbox => {
            if (checkbox.checked) {
                const label = document.querySelector(`label[for="${checkbox.id}"]`).textContent;
                const listItem = document.createElement('div');
                listItem.className = 'selected-service-item';
                listItem.textContent = '• ' + label;
                selectedList.appendChild(listItem);
            }
        });
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
        select.name = `stylist_ids[${serviceId}]`;
        select.required = true;

        container.appendChild(label);
        container.appendChild(select);

        // Check if date and time are selected
        if (!bookingDateInput.value || !startTimeInput.value || !endTimeInput.value) {
            select.disabled = true;
            select.innerHTML = '<option value="">Please select date and time first</option>';
            return container;
        }

        select.disabled = true;
        select.innerHTML = '<option value="">Loading available stylists...</option>';

        // Fetch available stylists for this service
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

        return container;
    }

    // Function to fetch stylists for a specific service
    async function fetchStylistsForService(serviceId) {
        if (!bookingDateInput.value || !startTimeInput.value || !endTimeInput.value) {
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
            
            const response = await fetch('/bookings/get-stylists', {
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
            // Only clear and rebuild everything if we're adding/removing services
            serviceStylistSelections.innerHTML = '';
            const selectedServices = getSelectedServiceIds();

            for (const serviceId of selectedServices) {
                const serviceLabel = document.querySelector(`label[for="service-${serviceId}"]`).textContent;
                const selection = await createStylistSelection(serviceId, serviceLabel);
                serviceStylistSelections.appendChild(selection);
            }
        } else {
            // Just update the options in existing select elements
            const selections = document.querySelectorAll('.service-stylist-selection');
            for (const selection of selections) {
                const serviceId = selection.dataset.serviceId;
                const select = selection.querySelector('select');
                
                if (!bookingDateInput.value || !startTimeInput.value || !endTimeInput.value) {
                    select.disabled = true;
                    select.innerHTML = '<option value="">Please select date and time first</option>';
                    continue;
                }

                select.disabled = true;
                select.innerHTML = '<option value="">Loading available stylists...</option>';

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
                serviceInput.name = `bookings_services[${index}][service_id]`;
                serviceInput.value = serviceId;
                container.appendChild(serviceInput);

                // Create hidden input for stylist_id
                const stylistInput = document.createElement('input');
                stylistInput.type = 'hidden';
                stylistInput.name = `bookings_services[${index}][stylist_id]`;
                stylistInput.value = stylistId;
                container.appendChild(stylistInput);

                // Get service cost from the label
                const serviceLabel = document.querySelector(`label[for="service-${serviceId}"]`);
                const costMatch = serviceLabel.textContent.match(/\$([\d.]+)/);
                if (costMatch) {
                    const costInput = document.createElement('input');
                    costInput.type = 'hidden';
                    costInput.name = `bookings_services[${index}][service_cost]`;
                    costInput.value = costMatch[1];
                    container.appendChild(costInput);
                }
            }
        });
    }

    // Add event listener for stylist selection changes
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('stylist-select')) {
            updateStylistIdsContainer();
        }
    });

    // Event listeners
    serviceCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', () => {
            calculateTotalCost();
            updateStylistSelections(false); 
        });
    });

    [bookingDateInput, startTimeInput, endTimeInput].forEach(input => {
        input.addEventListener('change', () => updateStylistSelections(true)); 
    });
});