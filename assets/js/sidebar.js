// sidebar.js
// Sidebar toggle functionality
// This script toggles the sidebar visibility when the hamburger icon is clicked
 document.addEventListener("DOMContentLoaded", () => {
 const hamburger = document.querySelector('.hamburger');
  const sidebar = document.querySelector('aside.sidebar');

  hamburger.addEventListener('click', () => {
    sidebar.classList.toggle('active');
  });

document.querySelectorAll('aside.sidebar nav a').forEach(link => {
  link.addEventListener('click', () => {
    if (window.innerWidth <= 768) {
      sidebar.classList.remove('active');
    }
  });
});


 });
