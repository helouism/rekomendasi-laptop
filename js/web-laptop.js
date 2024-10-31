(function ($) {
  "use strict"; 

  // Function to set sidebar state
  function setSidebarState(collapsed) {
    if (collapsed) {
      $("body").addClass("sidebar-toggled");
      $(".sidebar").addClass("toggled");
      $(".sidebar .collapse").collapse("hide");
    } else {
      $("body").removeClass("sidebar-toggled");
      $(".sidebar").removeClass("toggled");
    }
    localStorage.setItem("sidebarCollapsed", collapsed);
  }

  // Load saved sidebar state immediately when DOM is ready
  $(function() {
    // Remove the init class that was preventing flash
    document.documentElement.classList.remove('sidebar-toggled-init');
    
    // Apply the saved state
    const sidebarCollapsed = localStorage.getItem("sidebarCollapsed") === "true";
    setSidebarState(sidebarCollapsed);
  });

  // Toggle the side navigation
  $("#sidebarToggle, #sidebarToggleTop").on("click", function (e) {
    const isCurrentlyCollapsed = $(".sidebar").hasClass("toggled");
    setSidebarState(!isCurrentlyCollapsed);
  });

  // Close any open menu accordions when window is resized below 768px
  $(window).resize(function () {
    if ($(window).width() < 768) {
      $(".sidebar .collapse").collapse("hide");
    }

    // Toggle the side navigation when window is resized below 480px
    if ($(window).width() < 480 && !$(".sidebar").hasClass("toggled")) {
      setSidebarState(true);
    }
  });

  // Prevent the content wrapper from scrolling when the fixed side navigation hovered over
  $("body.fixed-nav .sidebar").on(
    "mousewheel DOMMouseScroll wheel",
    function (e) {
      if ($(window).width() > 768) {
        var e0 = e.originalEvent,
          delta = e0.wheelDelta || -e0.detail;
        this.scrollTop += (delta < 0 ? 1 : -1) * 30;
        e.preventDefault();
      }
    }
  );

  // Scroll to top button appear
  $(document).on("scroll", function () {
    var scrollDistance = $(this).scrollTop();
    if (scrollDistance > 100) {
      $(".scroll-to-top").fadeIn();
    } else {
      $(".scroll-to-top").fadeOut();
    }
  });

  // Smooth scrolling using jQuery easing
  $(document).on("click", "a.scroll-to-top", function (e) {
    var $anchor = $(this);
    $("html, body")
      .stop()
      .animate(
        {
          scrollTop: $($anchor.attr("href")).offset().top,
        },
        1000,
        "easeInOutExpo"
      );
    e.preventDefault();
  });
})(jQuery);