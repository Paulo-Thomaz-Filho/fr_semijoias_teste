const USER_DROPDOWN_ID = "#userDropdown";
const DROPDOWN_TOGGLE_SELECTOR = "[data-bs-toggle=dropdown]";
const HOVER_DELAY = 200;

function showDropdownOnHover() {
  const dropdownElement = document.querySelector(USER_DROPDOWN_ID);
  if (!dropdownElement) return;

  const dropdownInstance =
    bootstrap.Dropdown.getOrCreateInstance(dropdownElement);
  dropdownInstance.show();
}

function hideDropdownOnLeave() {
  const dropdownElement = document.querySelector(USER_DROPDOWN_ID);
  if (!dropdownElement) return;

  const dropdownContainer = dropdownElement.closest(".dropdown");
  const dropdownInstance = bootstrap.Dropdown.getInstance(dropdownElement);

  if (!dropdownInstance) return;

  setTimeout(() => {
    if (!dropdownContainer.matches(":hover")) {
      dropdownInstance.hide();
    }
  }, HOVER_DELAY);
}

function initializeDropdownHover() {
  const dropdownContainer = getDropdownContainer();

  if (dropdownContainer) {
    attachHoverEvents(dropdownContainer);
  }
}

function getDropdownContainer() {
  const dropdownElement = document.querySelector(USER_DROPDOWN_ID);
  return dropdownElement ? dropdownElement.closest(".dropdown") : null;
}

function attachHoverEvents(dropdownContainer) {
  dropdownContainer.addEventListener("mouseenter", showDropdownOnHover);
  dropdownContainer.addEventListener("mouseleave", hideDropdownOnLeave);
}

document.addEventListener("DOMContentLoaded", initializeDropdownHover);
