const container = document.querySelector(".container");
const linkItems = document.querySelectorAll(".link-item");
const darkMode = document.querySelector(".dark-mode");

// Container Hover
container.addEventListener("mouseenter", () => {
  container.classList.add("active");
});

// Container Hover Leave
container.addEventListener("mouseleave", () => {
  container.classList.remove("active");
});

// Link-items Clicked
linkItems.forEach((linkItem) => {
  linkItem.addEventListener("click", () => {
    linkItems.forEach((item) => item.classList.remove("active"));
    linkItem.classList.add("active");
  });
});

// Dark Mode Functionality
darkMode.addEventListener("click", function () {
  document.body.classList.toggle("dark-mode");

  if (document.body.classList.contains("dark-mode")) {
    darkMode.querySelector("span").textContent = "light mode";
    darkMode.querySelector("ion-icon").setAttribute("name", "sunny-outline");
    localStorage.setItem("theme", "dark"); // Guardar tema
  } else {
    darkMode.querySelector("span").textContent = "dark mode";
    darkMode.querySelector("ion-icon").setAttribute("name", "moon-outline");
    localStorage.setItem("theme", "light"); // Guardar tema
  }
});

// Check for saved theme on load
document.addEventListener("DOMContentLoaded", function() {
  const savedTheme = localStorage.getItem("theme");

  if (savedTheme === "dark") {
    document.body.classList.add("dark-mode");
    darkMode.querySelector("span").textContent = "light mode";
    darkMode.querySelector("ion-icon").setAttribute("name", "sunny-outline");
  }
});