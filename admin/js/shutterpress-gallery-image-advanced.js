(function ($, rwmb) {
  "use strict";

  if (typeof rwmb === "undefined") {
    return;
  }
  var views = (rwmb.views = rwmb.views || {}),
    MediaField = views.MediaField,
    MediaItem = views.MediaItem,
    MediaList = views.MediaList,
    ImageField;
  ImageField = views.ImageField = MediaField.extend({
    createList: function () {
      this.list = new MediaList({
        controller: this.controller,
        itemView: MediaItem.extend({
          className: "rwmb-image-item",
          template: wp.template("rwmb-image-item"),
        }),
      });
    },
  });
  function initImageField() {
    var $this = $(this),
      view = $this.data("view");
    if (view) {
      return;
    }
    view = new ImageField({
      input: this,
    });
    $this.siblings(".rwmb-media-view").remove();
    $this.after(view.el);
    $this.data("view", view);
  }
  function init(e) {
    $(e.target).find(".rwmb-custom_image_advanced").each(initImageField);
  }
  rwmb.$document
    .on("mb_ready", init)
    .on("clone", ".rwmb-custom_image_advanced", initImageField);
})(jQuery, rwmb);
