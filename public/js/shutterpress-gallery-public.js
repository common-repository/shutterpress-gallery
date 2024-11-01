(function ($) {
  "use strict";

  $(function () {
    if (typeof shutterpressData !== "undefined" && shutterpressData !== null) {
      var likedImages = shutterpressData.is_logged_in
        ? Array.isArray(shutterpressData.liked_images)
          ? shutterpressData.liked_images
          : Object.values(shutterpressData.liked_images)
        : Cookies.get("liked_images")
          ? JSON.parse(Cookies.get("liked_images"))
          : [];
      if (!Array.isArray(likedImages)) {
        likedImages = [];
      }
      likedImages.forEach(function (imageId) {
        $('.sp-gallery-like-icon[data-image-id="' + imageId + '"]').addClass(
          "sp-gallery-liked-image",
        );
      });
    } else {
    }
    function generateDynamicGalleryStyles() {
      const galleryElements = $('[id^="sp-gallery-"].sp-gallery');
      if (galleryElements.length === 0) {
        return;
      }
      let css = "";
      galleryElements.each(function () {
        const galleryElement = $(this);
        const galleryId = galleryElement.attr("id");
        const layout = galleryElement.data("layout");
        const columns = galleryElement.data("columns");
        const gap = galleryElement.data("gap");
        const columnsTablet = galleryElement.data("columns-tablet");
        const columnsMobile = galleryElement.data("columns-mobile");
        const breakpointTablet = galleryElement.data("breakpoint-tablet");
        const breakpointMobile = galleryElement.data("breakpoint-mobile");
        if (layout === "grid") {
          const baseSelector = `#${galleryId}.sp-gallery-grid-gallery`;
          css += `
						${baseSelector} {
							display: grid;
							grid-template-columns: repeat(${columns}, 1fr);
							grid-gap: ${gap}px;
						}
		
						/* For tablets (max-width: ${breakpointTablet}px) */
						@media (max-width: ${breakpointTablet}px) {
							${baseSelector} {
								grid-template-columns: repeat(${columnsTablet}, 1fr);
							}
						}
		
						/* For mobile (max-width: ${breakpointMobile}px) */
						@media (max-width: ${breakpointMobile}px) {
							${baseSelector} {
								grid-template-columns: repeat(${columnsMobile}, 1fr);
							}
						}
					`;
        }
        if (layout === "masonry") {
          const baseSelector = `#${galleryId}.sp-gallery-masonry-gallery .sp-gallery-item`;
          css += `
							${baseSelector} {
								margin-bottom: ${gap}px;
								width: calc((100% - (${columns} - 1) * ${gap}px) / ${columns});
							}
			
							/* For tablets (max-width: ${breakpointTablet}px) */
							@media (max-width: ${breakpointTablet}px) {
								${baseSelector} {
									width: calc((100% - (${columnsTablet} - 1) * ${gap}px) / ${columnsTablet});
								}
							}
			
							/* For mobile (max-width: ${breakpointMobile}px) */
							@media (max-width: ${breakpointMobile}px) {
								${baseSelector} {
									width: calc((100% - (${columnsMobile} - 1) * ${gap}px) / ${columnsMobile});
								}
							}
						`;
        }
      });
      const styleElement = $('<style id="shutterpress-gallery-inline-style">', {
        type: "text/css",
      }).text(css);
      $("head")
        .append(styleElement)
        .promise()
        .done(function () {
          setTimeout(function () {
            $(".sp-gallery").removeClass("sp-gallery-hidden");
          }, 500);
        });
    }
    generateDynamicGalleryStyles();
    $(".sp-gallery-like-icon").on("click", function () {
      var $this = $(this);
      var imageId = $this.data("image-id");
      var isLoggedIn = shutterpressData.is_logged_in;
      $this.toggleClass("sp-gallery-liked-image");
      if (isLoggedIn) {
        $.post(
          shutterpressData.ajax_url,
          {
            action: "sp_gallery_toggle_user_like",
            image_id: imageId,
            _ajax_nonce: shutterpressData.nonce,
          },
          function (response) {
            if (response.success) {
              if ($this.hasClass("sp-gallery-liked-image")) {
                if (!likedImages.includes(imageId)) {
                  likedImages.push(imageId);
                }
              } else {
                likedImages = likedImages.filter(function (id) {
                  return id !== imageId;
                });
              }
              Cookies.set("liked_images", JSON.stringify(likedImages), {
                expires: 365,
                path: "/",
                sameSite: "Strict",
              });
            } else {
            }
          },
        ).fail(function (jqXHR, textStatus, errorThrown) {});
      } else {
        if ($this.hasClass("sp-gallery-liked-image")) {
          if (!likedImages.includes(imageId)) {
            likedImages.push(imageId);
          }
        } else {
          likedImages = likedImages.filter(function (id) {
            return id !== imageId;
          });
        }
        if (Array.isArray(likedImages)) {
          Cookies.set("liked_images", JSON.stringify(likedImages), {
            expires: 365,
            path: "/",
            sameSite: "Strict",
          });
        } else {
        }
      }
    });
    $(".sp-gallery-masonry-gallery").each(function () {
      var $gallery = $(this);
      var layoutType = $gallery.data("layout");
      if (layoutType === "masonry") {
        $gallery.imagesLoaded(function () {
          var columnGap = $gallery.data("gap");
          var $masonryGallery = $gallery.masonry({
            itemSelector: ".sp-gallery-masonry-item",
            columnWidth: ".sp-gallery-masonry-item",
            percentPosition: true,
            gutter: columnGap,
          });
          $(window).resize(function () {
            $masonryGallery.masonry("layout");
          });
        });
      }
    });
    function filterLikedPhotos(gallerySelector) {
      var layoutType = $(gallerySelector).data("layout");
      $(gallerySelector)
        .find(".sp-gallery-item")
        .each(function () {
          var imageId = $(this).find(".sp-gallery-like-icon").data("image-id");
          if (!likedImages.includes(imageId)) {
            $(this).hide();
            if (layoutType === "masonry") {
              $(this)
                .removeClass("sp-gallery-masonry-item")
                .addClass("sp-gallery-masonry-item-hidden");
            }
          } else {
            $(this).show();
            if (layoutType === "masonry") {
              $(this)
                .addClass("sp-gallery-masonry-item")
                .removeClass("sp-gallery-masonry-item-hidden");
            }
          }
        });
      if (layoutType === "masonry") {
        $(gallerySelector).masonry("layout");
      }
    }
    function handleFilterClick(
      $button,
      buttonSelector,
      buttonTextSelector,
      galleryId,
    ) {
      $button.blur();
      var gallerySelector = "#sp-gallery-" + galleryId;
      var $gallery = $(gallerySelector);
      var likedIconsOnPage = $gallery.find(
        ".sp-gallery-like-icon.sp-gallery-liked-image",
      );
      if (likedIconsOnPage.length === 0) {
        return;
      }
      var filterActive = $gallery.data("filter-active") || false;
      if (!filterActive) {
        $(buttonSelector).addClass("sp-gallery-filter-active");
        if (buttonTextSelector !== null) {
          $(buttonTextSelector).text("Show All Photos");
        }
        filterLikedPhotos(gallerySelector);
      } else {
        $(buttonSelector).removeClass("sp-gallery-filter-active");
        if (buttonTextSelector !== null) {
          $(buttonTextSelector).text("Show Favourite Photos");
        }
        $gallery.find(".sp-gallery-item").show();
        if ($gallery.data("layout") === "masonry") {
          $gallery.masonry("layout");
        }
      }
      $gallery.data("filter-active", !filterActive);
    }
    $('[id^="sp-gallery-filter-liked-photos-"]').each(function () {
      var $button = $(this);
      var galleryId = $button
        .attr("id")
        .replace("sp-gallery-filter-liked-photos-", "");
      var layoutType = $("#sp-gallery-" + galleryId).data("layout");
      $button.off("click").on("click", function () {
        var buttonTextSelector = getButtonTextSelector($button);
        handleFilterClick(
          $button,
          "#" + $button.attr("id"),
          buttonTextSelector,
          galleryId,
        );
      });
    });
    function getButtonTextSelector($button) {
      if ($button.hasClass("wp-block-button")) {
        return "#" + $button.attr("id") + " .wp-element-button";
      } else if ($button.hasClass("elementor-button")) {
        return "#" + $button.attr("id") + " .elementor-button-text";
      } else if ($button.hasClass("sp-gallery-button")) {
        return "#" + $button.attr("id");
      }
      return null;
    }
    $("[id^='sp-gallery-']").each(function () {
      var $gallery = $(this);
      var galleryId = $gallery.attr("id");
      var lightboxEnabled = $gallery.data("lightbox");
      if (lightboxEnabled) {
        lightGallery(document.getElementById(galleryId), {
          plugins: [lgThumbnail, lgZoom, lgAutoplay, lgFullscreen],
          thumbnail: true,
          zoom: true,
          selector: ".sp-gallery-item-lightbox",
          download: true,
          autoplay: true,
          fullscreen: true,
        });
      }
    });
  });
})(jQuery);
