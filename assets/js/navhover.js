// // This script changes the icon image on hover for the navigation bar icons
// document.addEventListener("DOMContentLoaded", () => {
//   const buttons = document.querySelectorAll("nav .btn");

//   buttons.forEach(button => {
//     const icon = button.querySelector(".nav-icon");

//     if (!icon) return;

//     button.addEventListener("mouseenter", () => {
//       icon.src = icon.dataset.hover;
//     });

//     button.addEventListener("mouseleave", () => {
//       icon.src = icon.dataset.default;
//     });
//   });
// });