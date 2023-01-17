/******/ (() => { // webpackBootstrap
var __webpack_exports__ = {};
/* global jQuery */
jQuery(document).ready(function ($) {
  /**
   * Related accessories backlinks load button.
   */
  $('#related-accessories-backlinks-load').on('click', function (e) {
    e.preventDefault();
    $('#related-accessories-backlinks > .inside').html(window.adminBackLinks.i18n.Loading);
    $.post(window.ajaxurl, {
      action: 'related_accessories_backlinks',
      post: window.adminBackLinks.post
    }, function (response) {
      $('#related-accessories-backlinks > .inside').html(response);
    });
  });
});
/******/ })()
;