jQuery(document).ready(function ($) {
  function debounce(func, wait, immediate) {
    var timeout;
    return function () {
      var context = this,
        args = arguments;
      var later = function () {
        timeout = null;
        if (!immediate) func.apply(context, args);
      };
      var callNow = immediate && !timeout;
      clearTimeout(timeout);
      timeout = setTimeout(later, wait);
      if (callNow) func.apply(context, args);
    };
  }
  function initializeMasonry($scope) {
    var $container = $scope.find(".sp-gallery-masonry-gallery");
    var $items = $container.find(".sp-gallery-masonry-item");
    if ($container.length === 0 || $items.length === 0) {
      return;
    }
    $container.imagesLoaded(function () {
      var windowWidth = $(window).width();
      var breakpoints = elementorFrontend.config.breakpoints;
      const columns_desktop = parseInt($container.attr("data-columns")) || 3;
      const columns_tablet =
        parseInt($container.attr("data-columns-tablet")) || 2;
      const columns_mobile =
        parseInt($container.attr("data-columns-mobile")) || 1;
      var columns;
      if (windowWidth >= breakpoints.lg) {
        columns = columns_desktop;
      } else if (
        windowWidth >= breakpoints.md &&
        windowWidth < breakpoints.lg
      ) {
        columns = columns_tablet;
      } else if (windowWidth < breakpoints.md) {
        columns = columns_mobile;
      }
      let gap = parseInt($container.attr("data-gap"));
      gap = isNaN(gap) || gap === null ? 10 : gap;
      const containerWidth = $container.width();
      const columnWidth = (containerWidth - (columns - 1) * gap) / columns;
      const columnHeights = Array(columns).fill(0);
      $items.each(function () {
        const $item = $(this);
        const minHeight = Math.min(...columnHeights);
        const columnIndex = columnHeights.indexOf(minHeight);
        $item.css({
          position: "absolute",
          top: `${minHeight}px`,
          left: `${columnIndex * (columnWidth + gap)}px`,
          "margin-bottom": "0px",
        });
        columnHeights[columnIndex] += $item.outerHeight(true) + gap;
      });
      const maxHeight = Math.max(...columnHeights);
      $container.css("height", `${maxHeight}px`);
    });
  }
  elementorFrontend.hooks.addAction(
    "frontend/element_ready/shutterpress_gallery.default",
    function ($scope) {
      debounce(initializeMasonry($scope), 100, true);
      var $container = $scope.find(".sp-gallery-masonry-gallery");
      if ($container.length === 0) {
        $scope
          .find(".sp-gallery-elementor-gallery")
          .removeClass("sp-gallery-hidden");
        return;
      }
      if (typeof ResizeObserver !== "undefined") {
        const resizeObserver = new ResizeObserver(function () {
          debounce(initializeMasonry($scope), 100);
        });
        resizeObserver.observe($container[0]);
      } else {
      }
      $scope
        .find(".sp-gallery-elementor-gallery")
        .removeClass("sp-gallery-hidden");
    },
  );
});
