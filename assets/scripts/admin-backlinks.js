/* global jQuery */
jQuery(document).ready(($) => {
  /**
   * Related accessories backlinks load button.
   */
  $('#related-accessories-backlinks-load').on('click', (e) => {
    e.preventDefault();
    $('#related-accessories-backlinks > .inside')
      .html(window.adminBackLinks.i18n.Loading);

    $.post(window.ajaxurl, {
      action: 'related_accessories_backlinks',
      post: window.adminBackLinks.post,
    },
    (response) => {
      $('#related-accessories-backlinks > .inside').html(response);
    });
  });
});
