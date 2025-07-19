/**
 * Order Form Service Module
 * 
 * This module handles the service order form submission and validation.
 * It collects data from the form, sends it via Axios to the API,
 * and processes the response with SweetAlert2 notifications.
 * 
 * NOTE FOR MOBILE APP INTEGRATION:
 * This module is designed to be reused in a React Native or Flutter app.
 * The same API endpoint will be used, with identical payload structure and
 * response format. The mobileApi.js will handle platform-specific
 * implementations while sharing the same validation and data processing logic.
 */

// Constants
const VAT_RATE = 0.15;
const BASE_PRICE = 150.00;

/**
 * Initialize the order form with event handlers and form functionality
 * @param {Object} config - Configuration options
 * @returns {Object} - Exposed methods for testing or external control
 */
export function initOrderForm(config = {}) {
  // Elements cache
  const form = document.getElementById('orderServiceForm');
  const submitBtn = document.getElementById('placeOrderBtn');
  const addServiceBtn = document.getElementById('addServiceBtn');
  const servicesList = document.getElementById('servicesList');
  const noServicesMessage = document.getElementById('noServicesMessage');
  
  // Form fields
  const serviceType = document.getElementById('service_type');
  const serviceId = document.getElementById('service_id');
  const plateNumber = document.getElementById('plate_number');
  const vehicleMake = document.getElementById('vehicle_make');
  const vehicleModel = document.getElementById('vehicle_model');
  const vehicleYear = document.getElementById('vehicle_year');
  const refuleAmount = document.getElementById('refule_amount');
  const pickupLocation = document.getElementById('pickup_location');
  const paymentMethodWallet = document.getElementById('payment_wallet');
  const paymentMethodCreditCard = document.getElementById('payment_credit_card');
  const creditCardForm = document.getElementById('credit-card-form');
  
  // State
  let serviceIndex = 0;
  const currentYear = new Date().getFullYear();
  
  // Initialize
  function init() {
    // Set up event listeners
    if (form) {
      form.addEventListener('submit', handleFormSubmit);
    }
    
    if (addServiceBtn) {
      addServiceBtn.addEventListener('click', validateAndAddService);
    }
    
    // Toggle payment method display
    if (paymentMethodWallet && paymentMethodCreditCard) {
      paymentMethodWallet.addEventListener('change', togglePaymentMethod);
      paymentMethodCreditCard.addEventListener('change', togglePaymentMethod);
    }
    
    // Field validations
    if (serviceType) serviceType.addEventListener('change', () => validateField(serviceType, 'service-type-error', !serviceType.value, 'Please select a service type'));
    if (serviceId) serviceId.addEventListener('change', () => validateField(serviceId, 'service-error', !serviceId.value, 'Please select a fuel type'));
    if (plateNumber) plateNumber.addEventListener('blur', () => validateField(plateNumber, 'plate-error', !plateNumber.value.trim(), 'Please enter a valid plate number'));
    if (vehicleMake) vehicleMake.addEventListener('blur', () => validateField(vehicleMake, 'make-error', !vehicleMake.value.trim(), 'Please enter a name'));
    if (vehicleModel) vehicleModel.addEventListener('blur', () => validateField(vehicleModel, 'model-error', !vehicleModel.value.trim(), 'Please enter the vehicle model'));
    if (vehicleYear) vehicleYear.addEventListener('blur', () => validateVehicleYear());
    if (refuleAmount) refuleAmount.addEventListener('blur', () => validateRefuleAmount());
    
    // Initialize summary
    updateSummary();
    
    // Disable submit button initially if no services
    if (submitBtn) {
      submitBtn.disabled = document.querySelectorAll('#servicesList .service-item').length === 0;
    }
  }
  
  // Validate vehicle year
  function validateVehicleYear() {
    const value = vehicleYear.value.trim();
    const yearNum = parseInt(value);
    const isValid = value && !isNaN(yearNum) && yearNum >= 1900 && yearNum <= (currentYear + 1);
    
    return validateField(
      vehicleYear, 
      'year-error', 
      !isValid,
      `Please enter a valid year between 1900 and ${currentYear + 1}`
    );
  }
  
  // Validate refueling amount
  function validateRefuleAmount() {
    const value = refuleAmount.value.trim();
    const amount = parseFloat(value);
    const isValid = value && !isNaN(amount) && amount > 0;
    
    return validateField(
      refuleAmount, 
      'refule-error', 
      !isValid,
      'Please enter a valid refueling amount greater than 0'
    );
  }
  
  /**
   * Toggle between payment methods
   */
  function togglePaymentMethod() {
    if (paymentMethodCreditCard && paymentMethodCreditCard.checked) {
      creditCardForm.classList.remove('d-none');
    } else {
      creditCardForm.classList.add('d-none');
    }
  }
  
  /**
   * Validate a field and show/hide error messages
   * @param {HTMLElement} field - The field to validate
   * @param {string} errorClass - The class of the error element
   * @param {boolean} hasError - Whether the field has an error
   * @param {string} errorMsg - The error message to display
   * @returns {boolean} - Whether the field is valid
   */
  function validateField(field, errorClass, hasError, errorMsg) {
    const errorElement = document.querySelector(`.${errorClass}`);
    
    if (hasError) {
      field.classList.add('is-invalid');
      field.classList.remove('is-valid');
      if (errorElement) {
        errorElement.textContent = errorMsg;
        errorElement.classList.remove('d-none');
      }
      return false;
    } else {
      field.classList.remove('is-invalid');
      field.classList.add('is-valid');
      if (errorElement) {
        errorElement.classList.add('d-none');
      }
      return true;
    }
  }
  
  /**
   * Validate all service fields before adding
   * @returns {boolean} - Whether all fields are valid
   */
  function validateServiceForm() {
    let isValid = true;
    
    // Validate each field
    isValid = validateField(
      serviceType, 
      'service-type-error', 
      !serviceType.value, 
      'Please select a service type'
    ) && isValid;
    
    isValid = validateField(
      serviceId, 
      'service-error', 
      !serviceId.value, 
      'Please select a fuel type'
    ) && isValid;
    
    isValid = validateField(
      plateNumber, 
      'plate-error', 
      !plateNumber.value.trim(), 
      'Please enter a valid plate number'
    ) && isValid;
    
    isValid = validateField(
      vehicleMake, 
      'make-error', 
      !vehicleMake.value.trim(), 
      'Please enter the name on card/RFID'
    ) && isValid;
    
    isValid = validateField(
      vehicleModel, 
      'model-error', 
      !vehicleModel.value.trim(), 
      'Please enter the vehicle model'
    ) && isValid;
    
    isValid = validateVehicleYear() && isValid;
    isValid = validateRefuleAmount() && isValid;
    
    return isValid;
  }
  
  /**
   * Validate and add a service to the list
   */
  function validateAndAddService() {
    if (validateServiceForm()) {
      addServiceItem();
      showToast("Service added to List of purchase Services");
    } else {
      showToast("Please complete all required fields correctly", "error");
    }
  }
  
  /**
   * Add a service item to the list
   */
  function addServiceItem() {
    const serviceTypeVal = serviceType.value;
    const serviceTypeName = serviceType.options[serviceType.selectedIndex].text;
    const serviceIdVal = serviceId.value;
    const serviceName = serviceId.options[serviceId.selectedIndex].text;
    const refuleAmountVal = refuleAmount.value;
    const vehicleMakeVal = vehicleMake.value;
    const vehicleModelVal = vehicleModel.value;
    const vehicleYearVal = vehicleYear.value;
    const plateNumberVal = plateNumber.value;
    
    // Create a new row
    const row = document.createElement('tr');
    row.className = 'service-item';
    row.dataset.index = serviceIndex;
    row.dataset.serviceType = serviceTypeVal;
    row.dataset.serviceId = serviceIdVal;
    row.dataset.refuleAmount = refuleAmountVal;
    row.dataset.vehicleMake = vehicleMakeVal;
    row.dataset.vehicleModel = vehicleModelVal;
    row.dataset.vehicleYear = vehicleYearVal;
    row.dataset.plateNumber = plateNumberVal;
    
    row.innerHTML = `
      <td>${serviceTypeName} - ${serviceName}<br>(SAR ﷼ ${BASE_PRICE.toFixed(2)})<br>Prepaid</td>
      <td>${vehicleMakeVal}<br>${vehicleModelVal}<br>${vehicleYearVal}<br>${plateNumberVal}</td>
      <td>${refuleAmountVal}</td>
      <td>
        <button type="button" class="btn btn-sm btn-info edit-service">
          <i class="fa fa-edit"></i>
        </button>
        <button type="button" class="btn btn-sm btn-danger delete-service">
          <i class="fa fa-trash"></i>
        </button>
        <input type="hidden" name="services[${serviceIndex}][service_type]" value="${serviceTypeVal}">
        <input type="hidden" name="services[${serviceIndex}][service_id]" value="${serviceIdVal}">
        <input type="hidden" name="services[${serviceIndex}][refule_amount]" value="${refuleAmountVal}" class="refueling-amount">
        <input type="hidden" name="services[${serviceIndex}][vehicle_make]" value="${vehicleMakeVal}">
        <input type="hidden" name="services[${serviceIndex}][vehicle_model]" value="${vehicleModelVal}">
        <input type="hidden" name="services[${serviceIndex}][vehicle_year]" value="${vehicleYearVal}">
        <input type="hidden" name="services[${serviceIndex}][plate_number]" value="${plateNumberVal}">
      </td>
    `;
    
    // Add to the DOM
    servicesList.appendChild(row);
    
    // Attach event handlers
    const deleteBtn = row.querySelector('.delete-service');
    if (deleteBtn) {
      deleteBtn.addEventListener('click', function() {
        row.remove();
        updateSummary();
        showToast("Service removed from list", "info");
      });
    }
    
    const editBtn = row.querySelector('.edit-service');
    if (editBtn) {
      editBtn.addEventListener('click', function() {
        // Load values back into form
        serviceType.value = row.dataset.serviceType;
        serviceId.value = row.dataset.serviceId;
        refuleAmount.value = row.dataset.refuleAmount;
        vehicleMake.value = row.dataset.vehicleMake;
        vehicleModel.value = row.dataset.vehicleModel;
        vehicleYear.value = row.dataset.vehicleYear;
        plateNumber.value = row.dataset.plateNumber;
        
        // Remove row
        row.remove();
        updateSummary();
        showToast("Service ready for editing", "info");
      });
    }
    
    // Increment service index
    serviceIndex++;
    
    // Clear form fields
    clearFormFields();
    
    // Update summary
    updateSummary();
  }
  
  /**
   * Clear all form fields
   */
  function clearFormFields() {
    serviceType.value = '';
    serviceId.value = '';
    plateNumber.value = '';
    vehicleMake.value = '';
    vehicleModel.value = '';
    vehicleYear.value = '';
    refuleAmount.value = '';
    
    // Reset validation state
    [serviceType, serviceId, plateNumber, vehicleMake, vehicleModel, vehicleYear, refuleAmount].forEach(field => {
      field.classList.remove('is-valid', 'is-invalid');
    });
    
    document.querySelectorAll('.service-type-error, .service-error, .plate-error, .make-error, .model-error, .year-error, .refule-error').forEach(el => {
      el.classList.add('d-none');
    });
  }
  
  /**
   * Update the summary section
   */
  function updateSummary() {
    const services = document.querySelectorAll('#servicesList .service-item');
    const serviceCount = services.length;
    
    // Show/hide no services message
    if (serviceCount > 0) {
      noServicesMessage.classList.add('d-none');
    } else {
      noServicesMessage.classList.remove('d-none');
    }
    
    // Calculate totals
    let totalRefueling = 0;
    document.querySelectorAll('.refueling-amount').forEach(input => {
      totalRefueling += parseFloat(input.value || 0);
    });
    
    const servicesTotal = serviceCount * BASE_PRICE;
    const totalBeforeVat = servicesTotal + totalRefueling;
    const vatAmount = totalBeforeVat * VAT_RATE;
    const grandTotal = totalBeforeVat + vatAmount;
    
    // Update display
    animateValue('#unitPrice', `SAR ﷼ ${BASE_PRICE.toFixed(2)}`);
    animateValue('#quantity', serviceCount.toString());
    animateValue('#topupAmount', `SAR ﷼ ${totalRefueling.toFixed(2)}`);
    animateValue('#totalAmount', `SAR ﷼ ${grandTotal.toFixed(2)}`);
    
    // Enable/disable submit button
    if (submitBtn) {
      submitBtn.disabled = serviceCount === 0;
    }
  }
  
  /**
   * Animate value change
   * @param {string} selector - CSS selector for the element
   * @param {string} newValue - New value to display
   */
  function animateValue(selector, newValue) {
    const element = document.querySelector(selector);
    if (element) {
      element.style.transition = 'opacity 0.2s';
      element.style.opacity = '0';
      
      setTimeout(() => {
        element.textContent = newValue;
        element.style.opacity = '1';
      }, 200);
    }
  }
  
  /**
   * Show a toast notification
   * @param {string} message - Message to display
   * @param {string} icon - SweetAlert2 icon type
   */
  function showToast(message, icon = 'success') {
    Swal.fire({
      toast: true,
      position: 'top-end',
      icon: icon,
      title: message,
      showConfirmButton: false,
      timer: 2000,
      timerProgressBar: true
    });
  }
  
  /**
   * Handle form submission
   * @param {Event} event - Form submit event
   */
  async function handleFormSubmit(event) {
    event.preventDefault();
    
    // Validate form
    const errors = [];
    
    // Check for services
    const services = document.querySelectorAll('#servicesList .service-item');
    if (services.length === 0) {
      errors.push('Please add at least one service to your order.');
    }
    
    // Validate pickup location
    if (!pickupLocation.value.trim()) {
      errors.push('Pickup location is required.');
      validateField(pickupLocation, 'location-error', true, 'Please select a pickup location');
    } else {
      validateField(pickupLocation, 'location-error', false);
    }
    
    // Check for credit card if selected
    if (paymentMethodCreditCard.checked) {
      const cardNumber = document.querySelector('#card-number-element');
      if (!cardNumber) {
        errors.push('Credit card information is required.');
      }
    }
    
    // If there are errors, show them and stop submission
    if (errors.length > 0) {
      showErrorMessages(errors);
      return;
    }
    
    // Show loading state
    const originalBtnText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Processing...';
    
    try {
      // Collect form data
      const formData = collectFormData();
      
      // Send the data to the server
      const response = await axios.post('/services/booking/order/form', formData);
      
      // Handle success
      if (response.data.status === 'success') {
        // Show success message
        Swal.fire({
          title: 'Success!',
          text: response.data.message || 'Service order created successfully!',
          icon: 'success',
          confirmButtonText: 'OK'
        }).then((result) => {
          if (result.isConfirmed && response.data.redirect) {
            window.location.href = response.data.redirect;
          } else if (!response.data.redirect) {
            // Refresh the page if no redirect
            window.location.reload();
          }
        });
      } else {
        // Handle unexpected success format
        showErrorAlert('Unexpected response format. Please try again.');
      }
    } catch (error) {
      // Handle error
      console.error('Error submitting form:', error);
      
      if (error.response && error.response.data) {
        const data = error.response.data;
        
        if (data.status === 'error' && data.errors) {
          // Format validation errors
          const errorMessages = [];
          for (const field in data.errors) {
            if (data.errors[field] && Array.isArray(data.errors[field])) {
              data.errors[field].forEach(msg => errorMessages.push(msg));
            }
          }
          
          if (errorMessages.length > 0) {
            showErrorMessages(errorMessages);
          } else {
            showErrorAlert(data.message || 'An error occurred while processing your request.');
          }
        } else {
          showErrorAlert(data.message || 'An error occurred while processing your request.');
        }
      } else {
        showErrorAlert('An error occurred while processing your request. Please try again later.');
      }
    } finally {
      // Reset button state
      submitBtn.disabled = false;
      submitBtn.innerHTML = originalBtnText;
    }
  }
  
  /**
   * Collect form data into a structured object
   * @returns {Object} - Form data object
   */
  function collectFormData() {
    const formData = {
      pickup_location: pickupLocation.value,
      payment_method: paymentMethodWallet.checked ? 'wallet' : 'credit_card',
      services: []
    };
    
    // Add services
    document.querySelectorAll('#servicesList .service-item').forEach(item => {
      formData.services.push({
        service_type: item.dataset.serviceType,
        service_id: item.dataset.serviceId,
        refule_amount: parseFloat(item.dataset.refuleAmount),
        vehicle_make: item.dataset.vehicleMake,
        vehicle_model: item.dataset.vehicleModel,
        vehicle_year: item.dataset.vehicleYear,
        plate_number: item.dataset.plateNumber
      });
    });
    
    // Add credit card token if using credit card
    if (formData.payment_method === 'credit_card') {
      // In a real implementation, this would come from Stripe.js
      formData.stripeToken = document.querySelector('input[name="stripeToken"]')?.value;
      formData.save_card = document.querySelector('#save_card')?.checked ? 1 : 0;
    }
    
    return formData;
  }
  
  /**
   * Show error messages in a SweetAlert2 modal
   * @param {Array} errors - Array of error messages
   */
  function showErrorMessages(errors) {
    let errorHtml = '';
    
    if (errors.length === 1) {
      errorHtml = errors[0];
    } else {
      errorHtml = '<ul class="text-start mb-0">';
      errors.forEach(error => {
        errorHtml += `<li>${error}</li>`;
      });
      errorHtml += '</ul>';
    }
    
    Swal.fire({
      title: 'Error',
      html: errorHtml,
      icon: 'error',
      confirmButtonText: 'OK'
    });
  }
  
  /**
   * Show an error alert
   * @param {string} message - Error message
   */
  function showErrorAlert(message) {
    Swal.fire({
      title: 'Error',
      text: message,
      icon: 'error',
      confirmButtonText: 'OK'
    });
  }
  
  // Initialize on load
  init();
  
  // Return public API
  return {
    updateSummary,
    validateField,
    showToast,
    collectFormData
  };
}

// Auto-initialize when included directly in a script tag
document.addEventListener('DOMContentLoaded', function() {
  initOrderForm();
}); 