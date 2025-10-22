const TABLE_SELECTOR = ".table";
const VISIBLE_TABLES_SELECTOR = "section:not([hidden]) .table";
const SORT_INITIALIZED_ATTRIBUTE = "data-sort-initialized";
const EXCLUDED_COLUMNS = ["ações"];
const STATUS_ORDER = ["N/A"];

let tablesSortStates = new WeakMap();

function initializeTableSort(table) {
  if (isTableAlreadyInitialized(table)) {
    return;
  }

  markTableAsInitialized(table);
  initializeSortStates(table);
  setupTableHeaders(table);
}

function isTableAlreadyInitialized(table) {
  return table.hasAttribute(SORT_INITIALIZED_ATTRIBUTE);
}

function markTableAsInitialized(table) {
  table.setAttribute(SORT_INITIALIZED_ATTRIBUTE, "true");
}

function initializeSortStates(table) {
  const headers = table.querySelectorAll("thead th");
  const sortStates = {};

  headers.forEach((header, index) => {
    if (!isExcludedColumn(header)) {
      sortStates[index] = null;
    }
  });

  tablesSortStates.set(table, sortStates);
}

function isExcludedColumn(header) {
  const headerText = header.textContent.trim().toLowerCase();
  return EXCLUDED_COLUMNS.some((excluded) => headerText.includes(excluded));
}

function setupTableHeaders(table) {
  const headers = table.querySelectorAll("thead th");

  headers.forEach((header, index) => {
    if (isExcludedColumn(header)) {
      return;
    }

    makeHeaderClickable(header);
    addSortArrow(header);
    attachSortEvent(table, header, index);
  });
}

function makeHeaderClickable(header) {
  header.style.cursor = "pointer";
}

function addSortArrow(header) {
  const arrow = document.createElement("span");
  arrow.className = "ms-1 text-muted";
  arrow.innerHTML = getSortArrowHTML();
  header.appendChild(arrow);
}

function getSortArrowHTML() {
  return (
    '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">' +
    '<line x1="12" y1="6" x2="12" y2="18"/>' +
    '<line x1="12" y1="18" x2="7" y2="13"/>' +
    '<line x1="12" y1="18" x2="17" y2="13"/>' +
    "</svg>"
  );
}

function attachSortEvent(table, header, index) {
  header.addEventListener("click", () => {
    handleHeaderClick(table, header, index);
  });
}

function handleHeaderClick(table, header, index) {
  if (isExcludedColumn(header)) {
    return;
  }

  const sortStates = tablesSortStates.get(table);
  const newState = toggleSortState(sortStates[index]);

  updateSortStates(table, index, newState);
  updateVisualIndicators(table, header, newState);
  sortTableRows(table, index, newState);
}

function toggleSortState(currentState) {
  return currentState === "asc" ? "desc" : "asc";
}

function updateSortStates(table, activeIndex, newState) {
  const sortStates = tablesSortStates.get(table);

  Object.keys(sortStates).forEach((key) => {
    sortStates[key] = null;
  });

  sortStates[activeIndex] = newState;
}

function updateVisualIndicators(table, activeHeader, newState) {
  const headers = table.querySelectorAll("thead th");

  headers.forEach((header) => {
    const arrow = header.querySelector("span:last-child");
    if (arrow) {
      arrow.style.transform = "rotate(0deg)";
    }
  });

  const activeArrow = activeHeader.querySelector("span:last-child");
  if (activeArrow) {
    activeArrow.style.transform =
      newState === "desc" ? "rotate(180deg)" : "rotate(0deg)";
  }
}

function sortTableRows(table, columnIndex, sortOrder) {
  const tbody = table.querySelector("tbody");
  const rows = Array.from(tbody.querySelectorAll("tr"));

  rows.sort((a, b) => compareRows(a, b, columnIndex, sortOrder));

  rows.forEach((row) => tbody.appendChild(row));
}

function compareRows(rowA, rowB, columnIndex, sortOrder) {
  const textA = getCellText(rowA, columnIndex);
  const textB = getCellText(rowB, columnIndex);

  const statusComparison = compareStatus(textA, textB);
  if (statusComparison !== null) {
    return sortOrder === "asc" ? statusComparison : -statusComparison;
  }

  const numericComparison = compareNumeric(textA, textB);
  if (numericComparison !== null) {
    return sortOrder === "asc" ? numericComparison : -numericComparison;
  }

  const textComparison = textA.localeCompare(textB, "pt-BR");
  return sortOrder === "asc" ? textComparison : -textComparison;
}

function getCellText(row, columnIndex) {
  return row.cells[columnIndex].textContent.trim().replace(/^•\s*/, "");
}

function compareStatus(textA, textB) {
  const indexA = STATUS_ORDER.indexOf(textA);
  const indexB = STATUS_ORDER.indexOf(textB);

  if (indexA !== -1 && indexB !== -1) {
    return indexA - indexB;
  }

  return null;
}

function compareNumeric(textA, textB) {
  const numA = parseFloat(textA.replace(/[R$\s.]/g, "").replace(",", "."));
  const numB = parseFloat(textB.replace(/[R$\s.]/g, "").replace(",", "."));

  if (!isNaN(numA) && !isNaN(numB)) {
    return numA - numB;
  }

  return null;
}

function initializeAllTables() {
  const tables = document.querySelectorAll(TABLE_SELECTOR);
  tables.forEach((table) => {
    initializeTableSort(table);
  });
}

function initializeVisibleTables() {
  const visibleTables = document.querySelectorAll(VISIBLE_TABLES_SELECTOR);
  visibleTables.forEach((table) => {
    initializeTableSort(table);
  });
}

initializeAllTables();

window.initializeVisibleTables = initializeVisibleTables;
