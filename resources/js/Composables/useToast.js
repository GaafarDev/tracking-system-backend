import { useToast as useVueToastification } from 'vue-toastification'

export function useToast() {
  const toast = useVueToastification()

  const success = (message, options = {}) => {
    toast.success(message, {
      timeout: 5000,
      closeOnClick: true,
      pauseOnFocusLoss: true,
      pauseOnHover: true,
      draggable: true,
      draggablePercent: 0.6,
      showCloseButtonOnHover: false,
      hideProgressBar: false,
      closeButton: "button",
      icon: true,
      rtl: false,
      ...options
    })
  }

  const error = (message, options = {}) => {
    toast.error(message, {
      timeout: 7000,
      closeOnClick: true,
      pauseOnFocusLoss: true,
      pauseOnHover: true,
      draggable: true,
      draggablePercent: 0.6,
      showCloseButtonOnHover: false,
      hideProgressBar: false,
      closeButton: "button",
      icon: true,
      rtl: false,
      ...options
    })
  }

  const warning = (message, options = {}) => {
    toast.warning(message, {
      timeout: 6000,
      closeOnClick: true,
      pauseOnFocusLoss: true,
      pauseOnHover: true,
      draggable: true,
      draggablePercent: 0.6,
      showCloseButtonOnHover: false,
      hideProgressBar: false,
      closeButton: "button",
      icon: true,
      rtl: false,
      ...options
    })
  }

  const info = (message, options = {}) => {
    toast.info(message, {
      timeout: 5000,
      closeOnClick: true,
      pauseOnFocusLoss: true,
      pauseOnHover: true,
      draggable: true,
      draggablePercent: 0.6,
      showCloseButtonOnHover: false,
      hideProgressBar: false,
      closeButton: "button",
      icon: true,
      rtl: false,
      ...options
    })
  }

  const sosAlert = (message, driverName) => {
    toast.error(`🚨 SOS ALERT: ${message}`, {
      timeout: 0, // Don't auto-dismiss
      closeOnClick: false,
      pauseOnFocusLoss: false,
      pauseOnHover: false,
      draggable: false,
      showCloseButtonOnHover: true,
      hideProgressBar: true,
      closeButton: "button",
      icon: false,
      className: 'sos-alert-toast',
      bodyClassName: 'sos-alert-body',
      toastClassName: 'sos-alert-container'
    })
  }

  return {
    success,
    error,
    warning,
    info,
    sosAlert
  }
}