document.getElementById("load-more").addEventListener("click", function () {
  var hiddenItems = document.querySelectorAll(".item.d-none");
  hiddenItems.forEach(function (item) {
    item.classList.remove("d-none");
  });
  this.style.display = "none";
});
