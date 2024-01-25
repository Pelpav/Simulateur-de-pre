// dashboard.js

document.addEventListener("DOMContentLoaded", function () {
  // Your JavaScript code here
  var userRows = document.querySelectorAll(".user-row");

  userRows.forEach(function (row) {
    row.addEventListener("click", function () {
      var userId = row.getAttribute("data-user-id");
      window.location.href = "PHP/user_details.php?user_id=" + userId;
    });
  });
});
