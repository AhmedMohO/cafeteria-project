document.addEventListener('DOMContentLoaded', () => {
  const alertNode = document.getElementById('my-orders-alert');
  const cancelForms = document.querySelectorAll('.cancel-order-form');
  const filterForm = document.getElementById('my-orders-filter-form');
  const dateFromInput = document.getElementById('date_from');
  const dateToInput = document.getElementById('date_to');

  const clearAlertParamsFromUrl = () => {
    if (typeof window.history.replaceState === 'undefined') {
      return;
    }

    const currentUrl = new URL(window.location.href);
    currentUrl.searchParams.delete('alert_type');
    currentUrl.searchParams.delete('alert_message');
    window.history.replaceState({}, document.title, currentUrl.toString());
  };

  const alertIconMap = {
    success: 'success',
    danger: 'error',
    error: 'error',
    warning: 'warning',
    info: 'info',
  };

  if (alertNode) {
    const alertType = alertNode.dataset.alertType || '';
    const alertMessage = alertNode.dataset.alertMessage || '';

    if (alertMessage.trim() !== '') {
      clearAlertParamsFromUrl();

      if (typeof Swal !== 'undefined') {
        Swal.fire({
          icon: alertIconMap[alertType] || 'info',
          title: alertType === 'success' ? 'Success' : 'Order Update',
          text: alertMessage,
          confirmButtonColor: '#f0ad00',
        });
      }
    }
  }

  cancelForms.forEach((form) => {
    form.addEventListener('submit', (event) => {
      if (typeof Swal === 'undefined') {
        return;
      }

      event.preventDefault();

      const orderLabel = form.dataset.orderLabel || 'this order';

      Swal.fire({
        title: 'Cancel order?',
        text: `Order ${orderLabel} will be deleted permanently.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it',
        cancelButtonText: 'Keep it',
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        focusCancel: true,
      }).then((result) => {
        if (result.isConfirmed) {
          form.submit();
        }
      });
    });
  });

  if (dateFromInput && dateToInput) {
    dateFromInput.addEventListener('change', () => {
      dateToInput.min = dateFromInput.value || '';
    });

    dateToInput.addEventListener('change', () => {
      dateFromInput.max = dateToInput.value || '';
    });
  }

  if (filterForm && dateFromInput && dateToInput) {
    filterForm.addEventListener('submit', (event) => {
      const dateFrom = dateFromInput.value;
      const dateTo = dateToInput.value;

      if (dateFrom && dateTo && dateFrom > dateTo) {
        event.preventDefault();

        if (typeof Swal !== 'undefined') {
          Swal.fire({
            icon: 'error',
            title: 'Invalid Date Range',
            text: 'Date From cannot be after Date To.',
            confirmButtonColor: '#f0ad00',
          });
        } else {
          alert('Date From cannot be after Date To.');
        }
      }
    });
  }
});
