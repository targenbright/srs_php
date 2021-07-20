/**
 * This script changes navbar elements to active on clicking. Might need
 * to make class more specific if more li elements are added to the
 * page.
 */
 $(document).ready(function() {
    $("li").click(function(e) {
      $("li").removeClass("active");
      $(this).addClass("active");
    });
  });