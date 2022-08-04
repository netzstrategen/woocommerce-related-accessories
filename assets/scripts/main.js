/* global jQuery */
/* eslint-disable no-invalid-this */
jQuery(document).ready(function($) {
  /**
   * Gets related accessories displayed items.
   *
   * @param {string} selector
   *
   * @return {int} Related accessories displayed items.
   */
  function getItemAmount(selector) {
    let slides = 1;
    if (window.innerWidth > 767) {
      slides = selector === 'related-accessories--summary' ? 3 : 4;
    }
    return slides;
  }

  /**
   * Toggle related accessories slideshows.
   */
  // eslint-disable-next-line max-len
  $('.related-accessories select, .related-accessories--summary select').on('change', function() {
    const displayItems = getItemAmount($(this).parent().attr('class'));
    $(this).siblings('[class|="slideshow"]').hide('fast');
    $(this).siblings('.slideshow-' + this.value).show(function() {
      const $slider = $(this);
      $slider.flexslider({
        animation: 'slide',
        itemWidth: 210,
        itemMargin: 0,
        controlNav: false,
        minItems: displayItems,
        maxItems: displayItems,
        slideshow: false,
      });
      const flexslider = $slider.data('flexslider');
      // Turn width calculated slider into an actual "flexslider".
      flexslider.container.width('auto');

      // Triggers resize event in order to refresh flickity
      // for the variation sliders.
      $slider.find('.js-gallerya-slider.flickity-enabled').flickity('resize');

      $(window).resize(function() {
        const gridSize = getItemAmount();
        // Ensure slider to display correct amount of items.
        // Even if the item width is set via CSS, Flexslider does not know
        // how much items are in the viewport.
        flexslider.vars.minItems = gridSize;
        flexslider.vars.maxItems = gridSize;

        // Reset slider to zero as it may break otherwise after resizing.
        flexslider.currentSlide = 0;
        flexslider.animatingTo = 0;
        flexslider.doMath();
      });
    });

    // Show/Hide reset button.
    if (this.value !== '') {
      $('#reset_related_accessories').show();
    }
    else {
      $('#reset_related_accessories').hide();
    }
  });
  // Forces related accessories notice slideshow to be shown on page load.
  $('.related-accessories select').change();

  /**
   * Removes Flickity buttons if slideshow elements are less than 3.
   */
  $('.related-products__slider .flickity-slider').each(function() {
    if ($(this).children('li').length < 3 && $(window).width() > 768) {
      // eslint-disable-next-line max-len
      $(this).closest('.related-products__slider').children('.flickity-button').hide();
    }
  });

  /**
   * Re-initializes gallerya lightbox after quick view is displayed.
   */
  $(document).on('qv_loader_stop', function() {
    // Prevents header to partially cover quick view.
    if (!$('.site-header').hasClass('site-header--collapsed')) {
      const headerHeight = $('.site-header').height();
      $('.yith-wcqv-wrapper').css('margin-top', headerHeight + 20 + 'px');
    }
    $('.js-gallerya-lightbox').lightGallery({
      thumbnail: true,
      showThumbByDefault: false,
      subHtmlSelectorRelative: true,
      selector: '.gallerya__image > a',
    });
    $('.woocommerce-product-gallery').lightGallery({
      thumbnail: true,
      showThumbByDefault: false,
      subHtmlSelectorRelative: true,
      selector: '.woocommerce-product-gallery__image > a',
    });
  });

  /**
   * Reset related accessories button.
   */
  $('#reset_related_accessories').on('click', function(e) {
    e.preventDefault();
    $('.related-accessories--summary select').val('');
    $('.related-accessories--summary [class|="slideshow"]').hide('fast');
    $(this).hide();
  });

  // Triggers related accessories reveal on product added to cart via AJAX.
  // This will extend to full screen with and slide up to draw customer's
  // attention.
  $('body').on('added_to_cart', function() {
    const addToCartButton = $('.single_add_to_cart_button.ajax_add_to_cart');
    if (!addToCartButton.length) {
      return;
    }
    const accessoriesWrapper = $('.related-accessories--summary');
    if (addToCartButton.hasClass('ajax_add_to_cart') && !accessoriesWrapper.hasClass('related-accessories--popout')) {
      const productMeta = $('.product-summary .product_meta');
      const accessoriesSelect = accessoriesWrapper.find('select');
      if (accessoriesSelect.length) {
        productMeta.slideUp();
        accessoriesSelect.find('option')[1].selected=true;
        accessoriesSelect.change();
        accessoriesWrapper.addClass('related-accessories--popout').prepend('<a href="#" class="close-popout">ⓧ</a>');
        $('.close-popout').on('click', function(e) {
          e.preventDefault();
          accessoriesWrapper.removeClass('related-accessories--popout');
          $('#reset_related_accessories').trigger('click');
          $('.close-popout').remove();
          productMeta.slideDown();
        });
      }
    }
  });
});
