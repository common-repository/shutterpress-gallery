// src/lightgallery-init.js

// Import LightGallery and its plugins
import lightGallery from 'lightgallery/lightgallery.umd';
import lgThumbnail from 'lightgallery/plugins/thumbnail/lg-thumbnail.umd';
import lgZoom from 'lightgallery/plugins/zoom/lg-zoom.umd';
import lgFullscreen from 'lightgallery/plugins/fullscreen/lg-fullscreen.umd';
import lgAutoplay from 'lightgallery/plugins/autoplay/lg-autoplay.umd';

// Assign lightGallery and each plugin to the global window object
window.lightGallery = lightGallery;
window.lgThumbnail = lgThumbnail;
window.lgZoom = lgZoom;
window.lgFullscreen = lgFullscreen;
window.lgAutoplay = lgAutoplay;