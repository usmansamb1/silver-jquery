/**
 * Order Form Service Module
 * 
 * This module handles the service order form submission and validation.
 * It collects data from the form, sends it via Axios to the API,
 * and processes the response with SweetAlert2 notifications.
 * 
 * Updated to use HyperPay instead of Stripe for payment processing.
 */

// Constants
// BASE_PRICE will be fetched dynamically from the backend
let servicePrices = {};
let currentBasePrice = 150.00; // Default fallback price
const ALLOWED_FILE_TYPES = ['image/jpeg', 'image/png', 'application/pdf'];
const MAX_FILE_SIZE = 5 * 1024; // 5MB

/**
 * Initialize the form when the document is ready
 */
document.addEventListener('DOMContentLoaded', function() {
  console.log('📋 Order form DOM loaded, initializing...');
  initOrderForm();
  
  // Setup HyperPay form
  setupHyperPayForm();
  
  // Disable submit button initially if no services
  const submitBtn = document.getElementById('placeOrderBtn');
  const servicesList = document.getElementById('servicesList');
  if (submitBtn && servicesList) {
    submitBtn.disabled = servicesList.querySelectorAll('.service-item').length === 0;
  }
});

/**
 * Fetch service prices from the server
 */
function fetchServicePrices() {
  // Try to get service prices from a global variable or make an AJAX call
  if (typeof window.servicePrices !== 'undefined') {
    servicePrices = window.servicePrices;
  } else {
    // Fallback prices if not available
    servicePrices = {
      'rfid_80mm': 150.00,
      'rfid_120mm': 200.00,
      'oil_change': 120.00
    };
  }
  
  // Set the current base price to the first RFID service price
  currentBasePrice = servicePrices['rfid_80mm'] || 150.00;
}

/**
 * Setup HyperPay payment form
 */
function setupHyperPayForm() {
  const paymentMethodCreditCard = document.getElementById('payment_credit_card');
  const creditCardPaymentContainer = document.getElementById('credit-card-payment-container');
  const hyperpayWidget = document.getElementById('hyperpay-widget');
  
  // Function to check if widget should be loaded
  function shouldLoadWidget() {
    const services = collectServices();
    return paymentMethodCreditCard && paymentMethodCreditCard.checked && services.length > 0;
  }
  
  // Load widget immediately if conditions are met
  if (shouldLoadWidget()) {
    setTimeout(() => {
      loadHyperPayWidget();
    }, 200);
  }
  
  // Listen for payment method changes
  const paymentMethodWallet = document.getElementById('payment_wallet');
  if (paymentMethodCreditCard) {
    paymentMethodCreditCard.addEventListener('change', function() {
      if (this.checked) {
        // Only load if we have services
        if (shouldLoadWidget()) {
          clearTimeout(window.paymentMethodChangeTimeout);
          window.paymentMethodChangeTimeout = setTimeout(() => {
            loadHyperPayWidget();
          }, 200);
        } else {
          // Show message that services are needed
          if (hyperpayWidget) {
            hyperpayWidget.innerHTML = `
              <div class="text-center py-4">
                <i class="fa fa-info-circle fa-2x text-muted"></i>
                <p class="mt-2 text-muted">${window.translations?.please_add_service || 'Please add at least one service'}</p>
              </div>
            `;
          }
        }
      }
    });
  }
  
  if (paymentMethodWallet) {
    paymentMethodWallet.addEventListener('change', function() {
      if (this.checked && hyperpayWidget) {
        // Clear HyperPay widget when switching to wallet
        hyperpayWidget.innerHTML = `
          <div class="text-center py-4">
            <i class="fa fa-wallet fa-2x text-muted"></i>
            <p class="mt-2 text-muted">${window.translations?.select_credit_card_payment || 'Select credit card payment to load payment form'}</p>
          </div>
        `;
      }
    });
  }
  
  // Listen for card brand changes
  const cardBrandInputs = document.querySelectorAll('input[name="card_brand"]');
  cardBrandInputs.forEach(input => {
    input.addEventListener('change', function() {
      if (shouldLoadWidget()) {
        // Small delay to prevent rapid-fire reloads
        clearTimeout(window.cardBrandChangeTimeout);
        window.cardBrandChangeTimeout = setTimeout(() => {
          loadHyperPayWidget();
        }, 300);
      }
    });
  });
}

/**
 * Load HyperPay payment widget
 */
let isLoadingHyperPay = false; // Prevent multiple simultaneous loads
let currentCheckoutId = null;  // Track current checkout

async function loadHyperPayWidget() {
  const hyperpayWidget = document.getElementById('hyperpay-widget');
  if (!hyperpayWidget) return;
  
  // Prevent multiple simultaneous loads
  if (isLoadingHyperPay) {
    console.log('🔄 HyperPay widget is already loading, skipping...');
    return;
  }
  
  // Validate form first
  if (!validateForm()) {
    return;
  }
  
  // Collect services
  const services = collectServices();
  if (services.length === 0) {
    showError(window.translations?.please_add_service || 'Please add at least one service');
    return;
  }
  
  // Calculate total amount using unified calculation
  const calculation = calculateTotalAmount();
  const totalAmount = calculation.grandTotal;
  
  // Get selected brand
  const brandInputs = document.querySelectorAll('input[name="card_brand"]:checked');
  const selectedBrand = brandInputs.length > 0 ? brandInputs[0].value : 'VISA MASTER';
  
  console.log('🔄 Loading HyperPay widget...', {
    amount: totalAmount,
    brand: selectedBrand,
    services: services.length,
    calculation: {
      servicesTotal: calculation.servicesTotal,
      totalRefueling: calculation.totalRefueling,
      vatAmount: calculation.vatAmount,
      grandTotal: calculation.grandTotal
    },
    breakdown: services.map(s => ({
      service_id: s.service_id,
      service_price: window.servicePrices?.[s.service_id] || 0,
      refule_amount: s.refule_amount
    }))
  });
  
  // Set loading state
  isLoadingHyperPay = true;
  
  // Show loading state
  hyperpayWidget.innerHTML = `
    <div class="text-center py-4">
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">${window.translations?.loading || 'Loading...'}</span>
      </div>
      <p class="mt-2 text-muted">${window.translations?.loading_secure_payment || 'Loading secure payment form...'}</p>
    </div>
  `;
  
  try {
    // Prepare request data
    const requestData = {
      amount: totalAmount,
      brand: selectedBrand,
      services: services,
      pickup_location: document.getElementById('pickup_location').value
    };
    
    // Get HyperPay checkout ID
    const response = await fetch('/services/booking/hyperpay/get-form', {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        'Accept': 'application/json',
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(requestData)
    });
    
    const data = await response.json();
    
    if (data.status !== 'success' || !data.checkout_id) {
      throw new Error(data.message || window.translations?.failed_to_initialize_payment || 'Failed to initialize payment');
    }
    
    // Store current checkout ID
    currentCheckoutId = data.checkout_id;
    
    // Load HyperPay script
    const scriptUrl = data.script_url || 'https://eu-test.oppwa.com/v1/paymentWidgets.js?checkoutId=' + data.checkout_id;
    
    // Inject the form HTML first
    if (data.html) {
      hyperpayWidget.innerHTML = data.html;
    }
    
    // Remove any existing HyperPay script
    const existingScript = document.querySelector('script[src*="paymentWidgets.js"]');
    if (existingScript) {
      existingScript.remove();
    }
    
    // Create and load new script
    const script = document.createElement('script');
    script.src = scriptUrl;
    script.async = true;
    
    script.onload = function() {
      console.log('✅ HyperPay script loaded successfully');
      
      // Show test card info if in test mode
      const testCardInfo = document.getElementById('test-card-info');
      if (testCardInfo) {
        testCardInfo.style.display = 'block';
      }
      
      // Wait for form to be rendered
      setTimeout(() => {
        const form = document.querySelector('form.paymentWidgets');
        if (form) {
          console.log('✅ HyperPay form found and ready');
          
          // The form is now completely independent and will handle its own submission
          // No need to add any event listeners or modify its behavior
        } else {
          console.warn('⚠️ HyperPay form not found after script load');
        }
        
        // Reset loading state
        isLoadingHyperPay = false;
      }, 500);
    };
    
    script.onerror = function() {
      console.error('❌ Failed to load HyperPay script');
      hyperpayWidget.innerHTML = `
        <div class="alert alert-danger">
          <i class="fa fa-exclamation-triangle"></i> ${window.translations?.failed_to_load_payment || 'Failed to load payment form. Please try again.'}
        </div>
      `;
      // Reset loading state
      isLoadingHyperPay = false;
    };
    
    document.body.appendChild(script);
    
  } catch (error) {
    console.error('Error loading HyperPay:', error);
    hyperpayWidget.innerHTML = `
      <div class="alert alert-danger">
        <i class="fa fa-exclamation-triangle"></i> ${error.message || window.translations?.failed_to_load_payment || 'Failed to load payment form'}
      </div>
    `;
    // Reset loading state
    isLoadingHyperPay = false;
  }
}

/**
 * Initialize the order form with event handlers
 */
function initOrderForm() {
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
  const vehicleManufacturer = document.getElementById('vehicle_manufacturer');
  const vehicleModel = document.getElementById('vehicle_model');
  const vehicleYear = document.getElementById('vehicle_year');
  const refuleAmount = document.getElementById('refule_amount');
  const pickupLocation = document.getElementById('pickup_location');
  const paymentMethodWallet = document.getElementById('payment_wallet');
  const paymentMethodCreditCard = document.getElementById('payment_credit_card');
  const creditCardForm = document.getElementById('credit-card-form');
  const creditCardPaymentContainer = document.getElementById('credit-card-payment-container');
  
  // State
  let serviceIndex = 0;
  const currentYear = new Date().getFullYear();
  
  // Toggle payment method display
  if (paymentMethodWallet && paymentMethodCreditCard) {
    paymentMethodWallet.addEventListener('change', togglePaymentMethod);
    paymentMethodCreditCard.addEventListener('change', togglePaymentMethod);
  }
  
  // Handle vehicle selection toggle
  const useExistingVehicle = document.getElementById('use_existing_vehicle');
  const existingVehicleSection = document.getElementById('existing_vehicle_section');
  const newVehicleSection = document.getElementById('new_vehicle_section');
  const vehicleIdSelect = document.getElementById('vehicle_id');
  const saveVehicleCheckbox = document.getElementById('save_vehicle');
  
  if (useExistingVehicle && existingVehicleSection && newVehicleSection) {
    useExistingVehicle.addEventListener('change', function() {
      if (this.checked) {
        existingVehicleSection.classList.remove('d-none');
        newVehicleSection.classList.add('d-none');
        if (saveVehicleCheckbox) {
          saveVehicleCheckbox.parentElement.classList.add('d-none');
        }
        
        // If a vehicle is already selected, populate the hidden fields
        if (vehicleIdSelect.value) {
          const selectedOption = vehicleIdSelect.options[vehicleIdSelect.selectedIndex];
          populateVehicleFields(selectedOption);
        }
      } else {
        existingVehicleSection.classList.add('d-none');
        newVehicleSection.classList.remove('d-none');
        if (saveVehicleCheckbox) {
          saveVehicleCheckbox.parentElement.classList.remove('d-none');
        }
        
        // Clear the vehicle ID when switching to new vehicle mode
        vehicleIdSelect.value = '';
      }
    });
  }
  
  // Populate form fields when an existing vehicle is selected
  if (vehicleIdSelect) {
    vehicleIdSelect.addEventListener('change', function() {
      if (this.value) {
        const selectedOption = this.options[this.selectedIndex];
        populateVehicleFields(selectedOption);
      }
    });
  }
  
  /**
   * Helper function to populate vehicle fields from a selected option
   * @param {HTMLOptionElement} selectedOption - The selected option element
   */
  function populateVehicleFields(selectedOption) {
    if (!selectedOption) return;
    
    // Get vehicle data from option attributes
    const plateNumber = selectedOption.getAttribute('data-plate');
    const make = selectedOption.getAttribute('data-make');
    const manufacturer = selectedOption.getAttribute('data-manufacturer');
    const model = selectedOption.getAttribute('data-model');
    const year = selectedOption.getAttribute('data-year');
    
    // Store values in fields for form submission
    if (document.getElementById('plate_number')) {
      document.getElementById('plate_number').value = plateNumber || '';
    }
    if (document.getElementById('vehicle_make')) {
      document.getElementById('vehicle_make').value = make || '';
    }
    if (document.getElementById('vehicle_manufacturer')) {
      document.getElementById('vehicle_manufacturer').value = manufacturer || '';
    }
    if (document.getElementById('vehicle_model')) {
      document.getElementById('vehicle_model').value = model || '';
    }
    if (document.getElementById('vehicle_year')) {
      document.getElementById('vehicle_year').value = year || '';
    }
  }
  
  // Add service button click handler
  if (addServiceBtn) {
    addServiceBtn.addEventListener('click', function() {
      if (validateServiceForm()) {
        addServiceItem();
      } else {
        showToast(window.translations?.please_complete_fields || "Please complete all required fields correctly", window.translations?.error || "error");
      }
    });
  }
  
  // Form submission handler - UNIFIED APPROACH
  if (form) {
    form.addEventListener('submit', async function(e) {
      e.preventDefault();
      console.log('🔄 Form submitted - processing with unified handler');
      await handleFormSubmit();
    });
  }
  
  /**
   * Handle form submission for both wallet and credit card payments
   */
  async function handleFormSubmit() {
    // Validate form
    let isValid = true;
    const errors = [];
    
    // Check for services
    const serviceItems = servicesList.querySelectorAll('.service-item');
    console.log('🔍 Service validation check:', {
      servicesList_exists: !!servicesList,
      serviceItems_count: serviceItems.length,
      serviceItems_elements: Array.from(serviceItems).map(item => ({
        dataset: item.dataset,
        innerHTML: item.innerHTML.substring(0, 100) + '...'
      }))
    });
    
    if (serviceItems.length === 0) {
      isValid = false;
      errors.push(window.translations?.please_add_service || 'Please add at least one service to your order.');
    }
    
    // Validate pickup location
    if (!pickupLocation.value.trim()) {
      isValid = false;
      errors.push(window.translations?.pickup_location_required || 'Pickup location is required.');
      pickupLocation.classList.add('is-invalid');
      document.querySelector('.location-error')?.classList.remove('d-none');
    } else {
      pickupLocation.classList.remove('is-invalid');
      document.querySelector('.location-error')?.classList.add('d-none');
    }
    
    // Show validation errors if any
    if (!isValid) {
      showErrorMessages(errors);
      return;
    }
    
    // Show loading state
    const originalBtnText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>' + (window.translations?.processing || 'Processing...');
    
    try {
      // Collect form data
      const formData = collectFormData();
      
      // CRITICAL FIX: Check if form data collection failed
      if (!formData) {
        showErrorAlert(window.translations?.no_services_found || 'No services found. Please add at least one service to your order.');
        return;
      }
      
      // Get CSRF token from meta tag
      const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
      
      // Configure Axios
      axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken;
      axios.defaults.headers.common['Accept'] = 'application/json';
      
      // CRITICAL DEBUG: Log the exact data being sent
      console.log('🚀 Sending form data to backend:', {
        url: form.dataset.action,
        method: 'POST',
        data: formData,
        services_array_check: Array.isArray(formData.services),
        services_length: formData.services ? formData.services.length : 'NO_SERVICES'
      });
      
      // Send the request
      const response = await axios.post(form.dataset.action, formData);
      
      // Handle success
      if (response.data.status === 'success') {
        // Check if this is a credit card payment
        if (response.data.payment_method === 'credit_card') {
          const hyperpayWidget = document.getElementById('hyperpay-widget');
          
          // Check if we have a pre-loaded checkout
          if (hyperpayWidget && hyperpayWidget.dataset.checkoutId) {
            // We already have a HyperPay form loaded, just need to update the order association
            showToast(window.translations?.order_created || 'Order created! Please complete your payment below.', window.translations?.success || 'success');
            
            // Update the form action if needed to include order ID
            const paymentForm = hyperpayWidget.querySelector('form.paymentWidgets');
            if (paymentForm && response.data.data.order_id) {
              // Add order ID as hidden field
              const orderInput = document.createElement('input');
              orderInput.type = 'hidden';
              orderInput.name = 'merchantTransactionId';
              orderInput.value = 'ORDER-' + response.data.data.order_id;
              paymentForm.appendChild(orderInput);
            }
          } else if (response.data.data && response.data.data.payment_html) {
            // No pre-loaded form, use the one from response
            if (hyperpayWidget) {
              hyperpayWidget.innerHTML = response.data.data.payment_html;
              showToast(window.translations?.order_created || 'Order created! Please complete your payment.', window.translations?.success || 'success');
            }
          } else {
            // Fallback
            Swal.fire({
              title: window.translations?.success || 'Success!',
              text: response.data.message || window.translations?.order_created || 'Order created successfully! Please complete payment.',
              icon: 'success',
              confirmButtonText: window.translations?.ok || 'OK'
            });
          }
        } else {
          // For wallet payments or other success responses
          Swal.fire({
            title: window.translations?.success || 'Success!',
            text: response.data.message || window.translations?.service_order_success || 'Service order created successfully!',
            icon: 'success',
            confirmButtonText: window.translations?.ok || 'OK'
          }).then((result) => {
            if (result.isConfirmed && response.data.redirect) {
              window.location.href = response.data.redirect;
            } else if (!response.data.redirect) {
              // Refresh the page if no redirect
              window.location.reload();
            }
          });
        }
      } else {
        // Handle unexpected success format
        showErrorAlert(window.translations?.unexpected_response || 'Unexpected response format. Please try again.');
      }
    } catch (error) {
      console.error('Error submitting form:', error);
      
      if (error.response && error.response.data) {
        const response = error.response.data;
        
        if (response.status === 'error' && response.errors) {
          // Format validation errors
          const errorMessages = [];
          
          for (const field in response.errors) {
            if (response.errors[field] && Array.isArray(response.errors[field])) {
              response.errors[field].forEach(msg => errorMessages.push(msg));
            }
          }
          
          if (errorMessages.length > 0) {
            showErrorMessages(errorMessages);
          } else {
            showErrorAlert(response.message || window.translations?.processing_error || 'An error occurred while processing your request.');
          }
        } else {
          showErrorAlert(response.message || window.translations?.processing_error || 'An error occurred while processing your request.');
        }
      } else {
        showErrorAlert(window.translations?.processing_error_later || 'An error occurred while processing your request. Please try again later.');
      }
    } finally {
      // Reset button state
      submitBtn.disabled = false;
      submitBtn.innerHTML = originalBtnText;
    }
  }
  
  /**
   * Collect form data into a structured object for submission
   * @returns {Object} - Form data object
   */
  function collectFormData() {
    const services = [];
    const useExistingVehicleChecked = document.getElementById('use_existing_vehicle')?.checked;
    const vehicleId = document.getElementById('vehicle_id')?.value;
    
    // Collect service items
    servicesList.querySelectorAll('.service-item').forEach(item => {
      const serviceData = {
        service_type: item.dataset.serviceType,
        service_id: item.dataset.serviceId,
        refule_amount: parseFloat(item.dataset.refuleAmount),
        vehicle_make: item.dataset.vehicleMake,
        vehicle_manufacturer: item.dataset.vehicleManufacturer,
        vehicle_model: item.dataset.vehicleModel,
        vehicle_year: item.dataset.vehicleYear,
        plate_number: item.dataset.plateNumber
      };
      
      // Add vehicle selection data if using existing vehicle
      if (useExistingVehicleChecked && vehicleId) {
        serviceData.use_existing_vehicle = true;
        serviceData.vehicle_id = vehicleId;
      }
      
      services.push(serviceData);
    });
    
    // CRITICAL FIX: Ensure services array is not empty
    if (services.length === 0) {
      console.error('❌ No services found in form data collection');
      return null;
    }
    
    // Create form data object
    const formData = {
      pickup_location: pickupLocation.value,
      payment_method: paymentMethodWallet.checked ? 'wallet' : 'credit_card',
      services: services,
      use_existing_vehicle: useExistingVehicleChecked || false
    };
    
    // Add vehicle_id if using an existing vehicle - CRITICAL FIX
    if (useExistingVehicleChecked && vehicleId) {
      formData.vehicle_id = vehicleId;
    }
    
    // Add save_vehicle if creating a new vehicle
    if (!useExistingVehicleChecked && document.getElementById('save_vehicle')?.checked) {
      formData.save_vehicle = true;
    }
    
    // Add HyperPay card brand for credit card payments
    if (formData.payment_method === 'credit_card') {
      const selectedCardBrand = document.querySelector('input[name="card_brand"]:checked')?.value;
      if (selectedCardBrand) {
        formData.card_brand = selectedCardBrand;
      }
    }
    
    console.log('📋 Form data collected:', {
      pickup_location: formData.pickup_location,
      payment_method: formData.payment_method,
      services_count: formData.services.length,
      use_existing_vehicle: formData.use_existing_vehicle,
      vehicle_id: formData.vehicle_id,
      save_vehicle: formData.save_vehicle,
      services_preview: formData.services.map(s => ({
        service_type: s.service_type,
        service_id: s.service_id,
        refule_amount: s.refule_amount,
        plate_number: s.plate_number,
        use_existing_vehicle: s.use_existing_vehicle,
        vehicle_id: s.vehicle_id
      }))
    });
    
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
      title: window.translations?.error || 'Error',
      html: errorHtml,
      icon: 'error',
      confirmButtonText: window.translations?.ok || 'OK'
    });
  }
  
  /**
   * Show an error alert
   * @param {string} message - Error message
   */
  function showErrorAlert(message) {
    Swal.fire({
      title: window.translations?.error || 'Error',
      text: message,
      icon: 'error',
      confirmButtonText: window.translations?.ok || 'OK'
    });
  }
  
  // Field validations
  setupFieldValidations();
  
  // Initialize summary
  updateSummary();
  
  // Setup HyperPay form
  setupHyperPayForm();
  
  // Disable submit button initially if no services
  if (submitBtn && servicesList) {
    submitBtn.disabled = servicesList.querySelectorAll('.service-item').length === 0;
  }
  
  /**
   * Toggle between payment methods
   */
  function togglePaymentMethod() {
    if (paymentMethodCreditCard && paymentMethodCreditCard.checked) {
      if (creditCardPaymentContainer) {
        creditCardPaymentContainer.classList.remove('d-none');
      }
      // Small delay to ensure DOM is updated
      setTimeout(() => {
        setupHyperPayForm();
      }, 100);
    } else {
      if (creditCardPaymentContainer) {
        creditCardPaymentContainer.classList.add('d-none');
      }
    }
  }
  
  /**
   * Set up validation for all form fields
   */
  function setupFieldValidations() {
    // Service type validation
    if (serviceType) {
      serviceType.addEventListener('change', function() {
        validateField(
          serviceType,
          'service-type-error',
          !serviceType.value,
          window.translations?.please_select_service_type || 'Please select a service type'
        );
      });
    }
    
    // Fuel type validation
    if (serviceId) {
      serviceId.addEventListener('change', function() {
        validateField(
          serviceId,
          'service-error',
          !serviceId.value,
          window.translations?.please_select_fuel_type || 'Please select a fuel type'
        );
        
        // Update base price when service changes
        if (servicePrices[serviceId.value]) {
          currentBasePrice = servicePrices[serviceId.value];
          updateSummary();
        }
      });
    }
    
    // Plate number validation
    if (plateNumber) {
      plateNumber.addEventListener('blur', function() {
        validateField(
          plateNumber,
          'plate-error',
          !plateNumber.value.trim(),
          window.translations?.please_enter_valid_plate_number || 'Please enter a valid plate number'
        );
      });
    }
    
    // Name on RFID validation
    if (vehicleMake) {
      vehicleMake.addEventListener('blur', function() {
        validateField(
          vehicleMake,
          'make-error',
          !vehicleMake.value.trim(),
          window.translations?.please_enter_name_on_card_rfid || 'Please enter the name on card/RFID'
        );
      });
    }
    
    // Vehicle make validation
    if (vehicleManufacturer) {
      vehicleManufacturer.addEventListener('blur', function() {
        validateField(
          vehicleManufacturer,
          'manufacturer-error',
          !vehicleManufacturer.value.trim(),
          window.translations?.please_enter_vehicle_make || 'Please enter the vehicle make'
        );
      });
    }
    
    // Vehicle model validation
    if (vehicleModel) {
      vehicleModel.addEventListener('blur', function() {
        validateField(
          vehicleModel,
          'model-error',
          !vehicleModel.value.trim(),
          window.translations?.please_enter_vehicle_model || 'Please enter the vehicle model'
        );
      });
    }
    
    // Vehicle year validation
    if (vehicleYear) {
      vehicleYear.addEventListener('blur', function() {
        const value = vehicleYear.value.trim();
        const yearNum = parseInt(value);
        const isValid = value && !isNaN(yearNum) && yearNum >= 1900 && yearNum <= (currentYear + 1);
        
        validateField(
          vehicleYear,
          'year-error',
          !isValid,
          window.translations?.please_enter_valid_year || `Please enter a valid year between 1900 and ${currentYear + 1}`
        );
      });
    }
    
    // Refueling amount validation
    if (refuleAmount) {
      refuleAmount.addEventListener('blur', function() {
        const value = refuleAmount.value.trim();
        const amount = parseFloat(value);
        const isValid = value && !isNaN(amount) && amount > 0;
        
        validateField(
          refuleAmount,
          'refule-error',
          !isValid,
          window.translations?.please_enter_valid_refueling_amount || 'Please enter a valid refueling amount greater than 0'
        );
      });
    }
  }
  
  /**
   * Validate a form field and show/hide error messages
   * @param {HTMLElement} field - The form field to validate
   * @param {string} errorClass - CSS class for the error message
   * @param {boolean} hasError - Whether the field has an error
   * @param {string} errorMsg - Error message to display
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
   * Validate all fields in the service form
   * @returns {boolean} True if all validations pass
   */
  function validateServiceForm() {
    const isUsingExistingVehicle = document.getElementById('use_existing_vehicle')?.checked;
    let isValid = true;
    
    // Service Type validation
    if (!serviceType.value) {
      validateField(
        serviceType,
        'service-type-error',
        true,
        window.translations?.please_select_service_type || 'Please select a service type'
      );
      isValid = false;
    }
    
    // Service ID validation
    if (!serviceId.value) {
      validateField(
        serviceId,
        'service-error',
        true,
        window.translations?.please_select_fuel_type || 'Please select a fuel type'
      );
      isValid = false;
    }
    
    // If using existing vehicle, validate vehicle selection
    if (isUsingExistingVehicle) {
      const vehicleId = document.getElementById('vehicle_id');
      if (!vehicleId.value) {
        // Add validation styling to vehicle dropdown
        vehicleId.classList.add('is-invalid');
        // Display an error message
        showToast(window.translations?.please_select_vehicle || "Please select a vehicle from the dropdown", window.translations?.error || "error");
        isValid = false;
      } else {
        vehicleId.classList.remove('is-invalid');
        vehicleId.classList.add('is-valid');
      }
    } else {
      // Otherwise validate vehicle fields
      
      // Plate Number validation
      if (!plateNumber.value.trim()) {
        validateField(
          plateNumber,
          'plate-error',
          true,
          window.translations?.please_enter_plate_number || 'Please enter a plate number'
        );
        isValid = false;
      }
      
      // Vehicle Make (Name on Card) validation
      if (!vehicleMake.value.trim()) {
        validateField(
          vehicleMake,
          'make-error',
          true,
          window.translations?.please_enter_name_on_card_rfid || 'Please enter name on card/RFID'
        );
        isValid = false;
      }
      
      // Vehicle Manufacturer validation
      if (!vehicleManufacturer.value.trim()) {
        validateField(
          vehicleManufacturer,
          'manufacturer-error',
          true,
          window.translations?.please_enter_vehicle_make || 'Please enter vehicle make'
        );
        isValid = false;
      }
      
      // Vehicle Model validation
      if (!vehicleModel.value.trim()) {
        validateField(
          vehicleModel,
          'model-error',
          true,
          window.translations?.please_enter_vehicle_model || 'Please enter vehicle model'
        );
        isValid = false;
      }
      
      // Vehicle Year validation
      const yearValue = vehicleYear.value.trim();
      const yearNum = parseInt(yearValue);
      if (!yearValue || isNaN(yearNum) || yearNum < 1900 || yearNum > (currentYear + 1)) {
        validateField(
          vehicleYear,
          'year-error',
          true,
          window.translations?.please_enter_valid_year || `Please enter a valid year (1900-${currentYear + 1})`
        );
        isValid = false;
      }
    }
    
    // Refuel Amount validation
    const refuleValue = refuleAmount.value.trim();
    const refuleNum = parseFloat(refuleValue);
    if (!refuleValue || isNaN(refuleNum) || refuleNum <= 0) {
      validateField(
        refuleAmount,
        'refule-error',
        true,
        window.translations?.please_enter_valid_amount || 'Please enter a valid amount'
      );
      isValid = false;
    }
    
    return isValid;
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
    const vehicleManufacturerVal = vehicleManufacturer.value;
    const vehicleModelVal = vehicleModel.value;
    const vehicleYearVal = vehicleYear.value;
    const plateNumberVal = plateNumber.value;
    
    // Get actual service price and ensure it's a number
    const servicePriceRaw = servicePrices[serviceIdVal] || currentBasePrice;
    const servicePrice = typeof servicePriceRaw === 'number' ? servicePriceRaw : parseFloat(servicePriceRaw) || 150.00;
    
    // Check if using existing vehicle
    const isUsingExistingVehicle = document.getElementById('use_existing_vehicle')?.checked;
    let vehicleIdVal = '';
    
    if (isUsingExistingVehicle) {
      const vehicleSelect = document.getElementById('vehicle_id');
      if (vehicleSelect && vehicleSelect.value) {
        vehicleIdVal = vehicleSelect.value;
      }
    }
    
    // Create the service row element
    const row = document.createElement('tr');
    row.className = 'service-item';
    row.dataset.index = serviceIndex;
    row.dataset.serviceType = serviceTypeVal;
    row.dataset.serviceId = serviceIdVal;
    row.dataset.refuleAmount = refuleAmountVal;
    row.dataset.vehicleMake = vehicleMakeVal;
    row.dataset.vehicleManufacturer = vehicleManufacturerVal;
    row.dataset.vehicleModel = vehicleModelVal;
    row.dataset.vehicleYear = vehicleYearVal;
    row.dataset.plateNumber = plateNumberVal;
    row.dataset.servicePrice = servicePrice;
    
    // Store vehicle ID if using existing vehicle
    if (isUsingExistingVehicle && vehicleIdVal) {
      row.dataset.useExistingVehicle = 'true';
      row.dataset.vehicleId = vehicleIdVal;
    }
    
    // Prepare vehicle display text
    let vehicleDisplayText = `${vehicleManufacturerVal} ${vehicleModelVal}<br>${vehicleYearVal}<br>Plate: ${plateNumberVal}`;
    if (isUsingExistingVehicle && vehicleIdVal) {
      vehicleDisplayText += '<br><span class="badge bg-info">Saved Vehicle</span>';
    }
    
    // Row HTML content
    row.innerHTML = `
      <td>${serviceTypeName} - ${serviceName}<br>(<span class="icon-saudi_riyal"></span> ${servicePrice.toFixed(2)})<br>${window.translations?.prepaid || 'Prepaid'}</td>
      <td>${vehicleDisplayText}</td>
      <td>${refuleAmountVal}</td>
      <td>
        <button type="button" class="btn btn-sm btn-info edit-service">
          <i class="fa fa-edit"></i>
        </button>
        <button type="button" class="btn btn-sm btn-danger delete-service">
          <i class="fa fa-trash"></i>
        </button>
        <input type="hidden" class="refueling-amount" value="${refuleAmountVal}">
        <input type="hidden" class="service-price" value="${servicePrice}">
      </td>
    `;
    
    // Add to the list
    servicesList.appendChild(row);
    
    // Attach event handlers
    const editBtn = row.querySelector('.edit-service');
    if (editBtn) {
      editBtn.addEventListener('click', function() {
        // Pull data from the row's data attributes
        serviceType.value = row.dataset.serviceType;
        serviceId.value = row.dataset.serviceId;
        refuleAmount.value = row.dataset.refuleAmount;
        vehicleMake.value = row.dataset.vehicleMake;
        vehicleManufacturer.value = row.dataset.vehicleManufacturer;
        vehicleModel.value = row.dataset.vehicleModel;
        vehicleYear.value = row.dataset.vehicleYear;
        plateNumber.value = row.dataset.plateNumber;
        
        // Handle existing vehicle data
        const useExistingVehicleCheckbox = document.getElementById('use_existing_vehicle');
        const vehicleIdSelect = document.getElementById('vehicle_id');
        
        if (row.dataset.useExistingVehicle === 'true' && row.dataset.vehicleId) {
          // Set the use existing vehicle toggle
          if (useExistingVehicleCheckbox) {
            useExistingVehicleCheckbox.checked = true;
            // Trigger the change event to show/hide appropriate sections
            const event = new Event('change');
            useExistingVehicleCheckbox.dispatchEvent(event);
          }
          
          // Set the vehicle dropdown
          if (vehicleIdSelect) {
            vehicleIdSelect.value = row.dataset.vehicleId;
          }
        } else {
          // Make sure the toggle is off for new vehicles
          if (useExistingVehicleCheckbox) {
            useExistingVehicleCheckbox.checked = false;
            // Trigger the change event to show/hide appropriate sections
            const event = new Event('change');
            useExistingVehicleCheckbox.dispatchEvent(event);
          }
        }
        
        // Remove the row
        row.remove();
        
        // Update the summary
        updateSummary();
        
        // Show edit notification
        showToast(window.translations?.service_ready_for_editing || "Service ready for editing", window.translations?.info || "info");
      });
    }
    
    const deleteBtn = row.querySelector('.delete-service');
    if (deleteBtn) {
      deleteBtn.addEventListener('click', function() {
        // Remove the row with confirmation
        Swal.fire({
          title: window.translations?.remove_service || 'Remove service?',
          text: window.translations?.are_you_sure_remove_service || 'Are you sure you want to remove this service?',
          icon: 'warning',
          showCancelButton: true,
          confirmButtonText: window.translations?.yes_remove_it || 'Yes, remove it',
          cancelButtonText: window.translations?.cancel || 'Cancel'
        }).then((result) => {
          if (result.isConfirmed) {
            // Remove the row
            row.remove();
            
            // Update the summary
            updateSummary();
            
            // Show delete notification
            showToast(window.translations?.service_removed || "Service removed from list", window.translations?.info || "info");
          }
        });
      });
    }
    
    // Increment the service index
    serviceIndex++;
    
    // Clear form fields
    clearFormFields();
    
    // Update the summary
    updateSummary();
    
    // If credit card payment is selected, reload the HyperPay widget with updated amount
    const paymentMethodCreditCard = document.getElementById('payment_credit_card');
    if (paymentMethodCreditCard && paymentMethodCreditCard.checked) {
      setTimeout(() => {
        loadHyperPayWidget();
      }, 100);
    }
    
    // Show success notification
    showToast(window.translations?.service_added || "Service added to List of purchase Services");
  }
  
  /**
   * Clear all form fields and reset validation state
   */
  function clearFormFields() {
    const fields = [
      serviceType,
      serviceId,
      plateNumber,
      vehicleMake,
      vehicleManufacturer,
      vehicleModel,
      vehicleYear,
      refuleAmount
    ];
    
    // Clear values and reset validation
    fields.forEach(field => {
      if (field) {
        field.value = '';
        field.classList.remove('is-valid', 'is-invalid');
      }
    });
    
    // Hide all error messages
    document.querySelectorAll('.service-type-error, .service-error, .plate-error, .make-error, .manufacturer-error, .model-error, .year-error, .refule-error')
      .forEach(el => {
        el.classList.add('d-none');
      });
  }
  
  /**
   * Update the summary section with calculations
   */
  function updateSummary() {
    const services = servicesList.querySelectorAll('.service-item');
    const serviceCount = services.length;
    
    // Show/hide no services message
    if (serviceCount > 0) {
      noServicesMessage.classList.add('d-none');
    } else {
      noServicesMessage.classList.remove('d-none');
    }
    
    // Use unified calculation
    const calculation = calculateTotalAmount();
    
    // Update display - ensure currentBasePrice is a number for display
    const displayPrice = typeof currentBasePrice === 'number' ? currentBasePrice : parseFloat(currentBasePrice) || 150.00;
    animateValue('#unitPrice', `<span class="icon-saudi_riyal"></span> ${displayPrice.toFixed(2)}`);
    animateValue('#quantity', calculation.serviceCount.toString());
    animateValue('#topupAmount', `<span class="icon-saudi_riyal"></span> ${calculation.totalRefueling.toFixed(2)}`);
    animateValue('#subtotalAmount', `<span class="icon-saudi_riyal"></span> ${(calculation.servicesTotal + calculation.totalRefueling).toFixed(2)}`);
    animateValue('#vatAmount', `<span class="icon-saudi_riyal"></span> ${calculation.vatAmount.toFixed(2)}`);
    animateValue('#totalAmount', `<span class="icon-saudi_riyal"></span> ${calculation.grandTotal.toFixed(2)}`);
    
    // Enable/disable submit button
    if (submitBtn) {
      submitBtn.disabled = serviceCount === 0;
    }
  }
  
  /**
   * Animate value change for better UX
   * @param {string} selector - CSS selector for the element
   * @param {string} newValue - New value to display
   */
  function animateValue(selector, newValue) {
    const element = document.querySelector(selector);
    if (element) {
      element.style.transition = 'opacity 0.2s';
      element.style.opacity = '0';
      
      setTimeout(() => {
        // Create temporary div to parse HTML content
        const temp = document.createElement('div');
        temp.innerHTML = newValue;
        // Replace content while preserving HTML structure
        element.innerHTML = '';
        while (temp.firstChild) {
          element.appendChild(temp.firstChild);
        }
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
}

/**
 * Collect services from the list
 */
function collectServices() {
  const services = [];
  const servicesList = document.getElementById('servicesList');
  
  if (!servicesList) {
    console.warn('Services list element not found');
    return services;
  }
  
  const serviceItems = servicesList.querySelectorAll('.service-item');
  
  serviceItems.forEach(item => {
    const serviceData = {
      service_type: item.dataset.serviceType,
      service_id: item.dataset.serviceId,
      refule_amount: parseFloat(item.dataset.refuleAmount),
      vehicle_make: item.dataset.vehicleMake,
      vehicle_manufacturer: item.dataset.vehicleManufacturer,
      vehicle_model: item.dataset.vehicleModel,
      vehicle_year: item.dataset.vehicleYear,
      plate_number: item.dataset.plateNumber
    };
    
    services.push(serviceData);
  });
  
  return services;
}

/**
 * Validate the form before submission
 */
function validateForm() {
  let isValid = true;
  
  // Clear previous errors
  document.querySelectorAll('.service-error, .plate-error, .make-error, .manufacturer-error, .model-error, .year-error, .refule-error, .location-error').forEach(error => {
    error.classList.add('d-none');
  });
  
  // Check if services are added
  const services = collectServices();
  if (services.length === 0) {
    showError(window.translations?.please_add_service || 'Please add at least one service');
    return false;
  }
  
  // Check pickup location
  const pickupLocation = document.getElementById('pickup_location');
  if (!pickupLocation || !pickupLocation.value) {
    const locationError = document.querySelector('.location-error');
    if (locationError) {
      locationError.classList.remove('d-none');
    }
    isValid = false;
  }
  
  return isValid;
}

/**
 * Show error message using SweetAlert
 */
function showError(message) {
  Swal.fire({
    title: window.translations?.error || 'Error!',
    text: message,
    icon: 'error',
    confirmButtonText: window.translations?.ok || 'OK'
  });
}

/**
 * Show success message using SweetAlert
 */
function showSuccess(message) {
  Swal.fire({
    title: window.translations?.success || 'Success!',
    text: message,
    icon: 'success',
    confirmButtonText: window.translations?.ok || 'OK'
  });
}

/**
 * Calculate total amount with proper VAT handling
 * VAT should only be applied to service prices, NOT refueling amounts
 */
function calculateTotalAmount() {
  const VAT_RATE = 0.15; // 15% VAT
  const services = collectServices();
  
  let servicesTotal = 0;      // Total of all service prices
  let totalRefueling = 0;     // Total of all refueling amounts
  let vatAmount = 0;          // VAT only on service prices
  
  services.forEach(service => {
    const servicePrice = parseFloat(window.servicePrices?.[service.service_id] || 0);
    const refuleAmount = parseFloat(service.refule_amount || 0);
    
    servicesTotal += servicePrice;
    totalRefueling += refuleAmount;
  });
  
  // VAT only applies to service prices, NOT refueling amounts
  vatAmount = servicesTotal * VAT_RATE;
  
  // Grand total = service prices + VAT on services + refueling (no VAT on refueling)
  const grandTotal = servicesTotal + vatAmount + totalRefueling;
  
  return {
    servicesTotal,
    totalRefueling,
    vatAmount,
    grandTotal,
    serviceCount: services.length
  };
}

// Make functions available globally
window.loadHyperPayWidget = loadHyperPayWidget;
window.collectServices = collectServices;
window.calculateTotalAmount = calculateTotalAmount;