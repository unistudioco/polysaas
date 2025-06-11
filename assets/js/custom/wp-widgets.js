document.addEventListener("DOMContentLoaded", function() {
    // Function to handle the click event on menu items with children
    function toggleSubMenu(e) {
      e.preventDefault();
      e.stopPropagation();
  
      var parent = this.parentElement;
      var subMenu = parent.querySelector(".sub-menu");
  
      if (parent.classList.contains("active")) {
        parent.classList.remove("active");
        subMenu.style.display = "none";
      } else {
        parent.classList.add("active");
        subMenu.style.display = "flex";
      }
    }
  
    // Attach the click event to all menu items with children
    var menuItems = document.querySelectorAll(".menu-item-has-children > a");
    menuItems.forEach(function(menuItem) {
      menuItem.addEventListener("click", toggleSubMenu);
    });
  
    // Attach the click event to all sub-menu items to prevent propagation
    var subMenuItems = document.querySelectorAll(".sub-menu a");
    subMenuItems.forEach(function(subMenuItem) {
      subMenuItem.addEventListener("click", function(e) {
        e.stopPropagation();
      });
    });
  });
  