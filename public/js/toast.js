const toast = document.getElementById("notification");
const toastTitle = toast.querySelector(".notification-title");
const toastMessage = toast.querySelector(".notification-message");

function showToast(title, message, alert = false) {
  toast.classList.remove("hidden", "opacity-0", "-translate-y-3/2");

  if (alert) {
    toast.classList.remove("text-green-600");
    toast.querySelector(".icon-success").classList.add("hidden");
    toastTitle.classList.add("text-red-500");
    toast.querySelector(".icon-alert").classList.remove("hidden");
  } else {
    toast.classList.remove("text-red-500");
    toast.querySelector(".icon-alert").classList.add("hidden");
    toast.classList.add("text-green-600");
    toast.querySelector(".icon-success").classList.remove("hidden");
  }

  toastTitle.textContent = title;
  toastMessage.textContent = message;

  setTimeout(() => {
    toast.classList.add("opacity-0", "-translate-y-3/2");
    setTimeout(() => {
      toast.classList.add("hidden");
    }, 300);
  }, 10000);
}
