document.addEventListener("DOMContentLoaded", function () {
  var items = document.querySelectorAll(".item");
  var loadMoreButton = document.getElementById("load-more");

  function showInitialItems() {
    items.forEach(function (item, index) {
      if (index < 3) {
        item.classList.add("visible");
      }
    });
  }

  function showAllItems() {
    items.forEach(function (item) {
      item.classList.add("visible");
    });
    loadMoreButton.style.display = "none";
  }

  loadMoreButton.addEventListener("click", showAllItems);

  showInitialItems();
});
