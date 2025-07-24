// LocalLoop JavaScript
document.addEventListener("DOMContentLoaded", function() {
  console.log("LocalLoop Loaded");

  // Fade out alerts smoothly
  const alerts = document.querySelectorAll('.alert');
  alerts.forEach(alert => {
    setTimeout(() => {
      alert.style.transition = "opacity 0.7s";
      alert.style.opacity = 0;
      setTimeout(() => alert.style.display = 'none', 700);
    }, 3000);
  });

  // Loader animation for page transitions
  const links = document.querySelectorAll('a.button-link');
  links.forEach(link => {
    link.addEventListener('click', function(e) {
      const loader = document.createElement('div');
      loader.className = 'loader';
      document.body.appendChild(loader);
    });
  });
});

function confirmBooking() {
  return confirm("Are you sure you want to confirm this booking?");
}
