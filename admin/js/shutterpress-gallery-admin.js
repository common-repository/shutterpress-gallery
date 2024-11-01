jQuery(document).ready(function ($) {
  function getQueryParam(param) {
    var urlParams = new URLSearchParams(window.location.search);
    return urlParams.get(param);
  }
  var currentPage = getQueryParam("page");
  if (currentPage === "sp-gallery-settings") {
    var tabs = document.querySelectorAll(".nav-tab");
    var tabContents = document.querySelectorAll(".tab-content");
    function hideAllTabContents() {
      tabContents.forEach(function (content) {
        content.style.display = "none";
      });
    }
    function deactivateAllTabs() {
      tabs.forEach(function (tab) {
        tab.classList.remove("nav-tab-active");
      });
    }
    function showTabContent(tabId) {
      var targetTabContent = document.querySelector("#" + tabId);
      if (targetTabContent) {
        targetTabContent.style.display = "block";
      }
    }
    tabs.forEach(function (tab) {
      tab.addEventListener("click", function (e) {
        e.preventDefault();
        var tabId = this.getAttribute("href").split("tab=")[1];
        deactivateAllTabs();
        hideAllTabContents();
        this.classList.add("nav-tab-active");
        showTabContent(tabId);
        var pageParam = getQueryParam("page") || "sp-gallery-settings";
        var postTypeParam =
          getQueryParam("post_type") || "shutterpress-gallery";
        var newUrl =
          window.location.protocol +
          "//" +
          window.location.host +
          window.location.pathname +
          "?post_type=" +
          postTypeParam +
          "&page=" +
          pageParam +
          "&tab=" +
          tabId;
        window.history.pushState(
          {
            path: newUrl,
          },
          "",
          newUrl,
        );
      });
    });
    var activeTab = getQueryParam("tab");
    if (activeTab) {
      deactivateAllTabs();
      hideAllTabContents();
      var initialTab = document.querySelector(
        'a[href*="tab=' + activeTab + '"]',
      );
      if (initialTab) {
        initialTab.classList.add("nav-tab-active");
        showTabContent(activeTab);
      } else {
        tabs[0].classList.add("nav-tab-active");
        tabContents[0].style.display = "block";
      }
    } else {
      tabs[0].classList.add("nav-tab-active");
      tabContents[0].style.display = "block";
    }
  }
  $(".sp-gallery-color-picker").wpColorPicker();
});
