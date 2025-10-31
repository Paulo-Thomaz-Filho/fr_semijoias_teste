const SECTIONS_SELECTOR = 'main section[id$="-section"]';
const NAV_LINKS_SELECTOR = "aside ul li a";
const MAIN_ELEMENT_SELECTOR = "main.main-with-sidebar";
const ACTIVE_SECTION_SELECTOR = "main section.is-active";
const DEFAULT_SECTION = "dashboard-section";

let currentSectionId = DEFAULT_SECTION;

function showSection(sectionId) {
  currentSectionId = sectionId;

  hideAllSections();

  showTargetSection();

  updateMainElementState();

  updateNavigationActiveState();
}

function hideAllSections() {
  const allSections = document.querySelectorAll(SECTIONS_SELECTOR);
  allSections.forEach((section) => {
    section.classList.remove("is-active");
    section.setAttribute("hidden", "");
    section.setAttribute("aria-hidden", "true");
  });
}

function showTargetSection() {
  const targetSection = document.getElementById(currentSectionId);
  if (targetSection) {
    targetSection.classList.add("is-active");
    targetSection.removeAttribute("hidden");
    targetSection.setAttribute("aria-hidden", "false");

    if (typeof window.initializeVisibleTables === "function") {
      window.initializeVisibleTables();
    }
    // Atualiza dashboard ao navegar para ela
    if (currentSectionId === "dashboard-section" && typeof window.atualizarDashboard === "function") {
      window.atualizarDashboard();
    }
  }
}

function updateMainElementState() {
  const mainElement = document.querySelector(MAIN_ELEMENT_SELECTOR);
  if (mainElement) {
    if (currentSectionId === DEFAULT_SECTION) {
      mainElement.classList.add("dashboard-active");
    } else {
      mainElement.classList.remove("dashboard-active");
    }
  }
}

function updateNavigationActiveState() {
  clearAllNavigationActiveStates();

  setActiveNavigationLink();
}

function clearAllNavigationActiveStates() {
  const allLinks = document.querySelectorAll(NAV_LINKS_SELECTOR);
  allLinks.forEach((link) => {
    link.classList.remove("sidebar-link-active", "bg-body-secondary");
  });
}

function setActiveNavigationLink() {
  const activeLink = document.querySelector(
    `a[onclick="showSection('${currentSectionId}')"]`
  );
  if (activeLink) {
    activeLink.classList.add("sidebar-link-active");
  }
}

function initializeNavigation() {
  setInitialActiveSection();

  attachNavigationClickHandlers();
}

function setInitialActiveSection() {
  const activeSection = document.querySelector(ACTIVE_SECTION_SELECTOR);
  if (activeSection && activeSection.id) {
    showSection(activeSection.id);
  } else {
    showSection(DEFAULT_SECTION);
  }
}

function attachNavigationClickHandlers() {
  const navLinks = document.querySelectorAll(NAV_LINKS_SELECTOR);
  navLinks.forEach((link) => {
    link.addEventListener("click", handleNavigationClick);
  });
}

function handleNavigationClick() {
  const navLinks = document.querySelectorAll(NAV_LINKS_SELECTOR);

  navLinks.forEach((link) => {
    link.classList.remove("sidebar-link-active", "bg-body-secondary");
  });

  this.classList.add("sidebar-link-active");
}

document.addEventListener("DOMContentLoaded", initializeNavigation);
