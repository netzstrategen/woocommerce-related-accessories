/* global jQuery */
jQuery(document).ready(function ($) {
  /**
   * Gets related accessories displayed items.
   *
   * @param {string} selector
   *
   * @return {int} Related accessories displayed items.
   */
  function getItemAmount(selector) {
    var slides = 1;

    if (window.innerWidth > 767) {
      slides = selector === 'related-accessories--summary' ? 3 : 4;
    }

    return slides;
  }
  /**
   * Toggle related accessories slideshows.
   */


  $('.related-accessories select, .related-accessories--summary select').on('change', function toggleAccessories() {
    var _this = this;

    var displayItems = getItemAmount($(this).parent().attr('class'));
    $(this).siblings('[class|="slideshow"]').hide('fast');
    $(this).siblings(".slideshow-".concat(this.value)).show(function () {
      var $slider = $(_this);
      $slider.flexslider({
        animation: 'slide',
        itemWidth: 210,
        itemMargin: 0,
        controlNav: false,
        minItems: displayItems,
        maxItems: displayItems,
        slideshow: false
      });
      var flexslider = $slider.data('flexslider'); // Turn width calculated slider into an actual "flexslider".

      flexslider.container.width('auto'); // Triggers resize event in order to refresh flickity
      // for the variation sliders.

      $slider.find('.js-gallerya-slider.flickity-enabled').flickity('resize');
      $(window).resize(function () {
        var gridSize = getItemAmount(); // Ensure slider to display correct amount of items.
        // Even if the item width is set via CSS, Flexslider does not know
        // how much items are in the viewport.

        flexslider.vars.minItems = gridSize;
        flexslider.vars.maxItems = gridSize; // Reset slider to zero as it may break otherwise after resizing.

        flexslider.currentSlide = 0;
        flexslider.animatingTo = 0;
        flexslider.doMath();
      });
    }); // Show/Hide reset button.

    if (this.value !== '') {
      $('#reset_related_accessories').show();
    } else {
      $('#reset_related_accessories').hide();
    }
  }); // Forces related accessories notice slideshow to be shown on page load.

  $('.related-accessories select').change();
  /**
   * Removes Flickity buttons if slideshow elements are less than 3.
   */

  $('.related-products__slider .flickity-slider').each(function adjustSliderButtons() {
    if ($(this).children('li').length < 3 && $(window).width() > 768) {
      // eslint-disable-next-line max-len
      $(this).closest('.related-products__slider').children('.flickity-button').hide();
    }
  });
  /**
   * Re-initializes gallerya lightbox after quick view is displayed.
   */

  $(document).on('qv_loader_stop', function () {
    // Prevents header to partially cover quick view.
    if (!$('.site-header').hasClass('site-header--collapsed')) {
      var headerHeight = $('.site-header').height();
      $('.yith-wcqv-wrapper').css('margin-top', "".concat(headerHeight + 20, "px"));
    } // Support for lightgallery.


    if (typeof $.fn.lightGallery === 'function') {
      $('.js-gallerya-lightbox').lightGallery({
        thumbnail: true,
        showThumbByDefault: false,
        subHtmlSelectorRelative: true,
        selector: '.gallerya__image > a'
      });
      $('.woocommerce-product-gallery').lightGallery({
        thumbnail: true,
        showThumbByDefault: false,
        subHtmlSelectorRelative: true,
        selector: '.woocommerce-product-gallery__image > a'
      });
    }
  });
  /**
   * Reset related accessories button.
   */

  $('#reset_related_accessories').on('click', function reset(e) {
    e.preventDefault();
    $('.related-accessories--summary select').val('');
    $('.related-accessories--summary [class|="slideshow"]').hide('fast');
    $(this).hide();
  });
});
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIm1haW4uanMiXSwibmFtZXMiOlsialF1ZXJ5IiwiZG9jdW1lbnQiLCJyZWFkeSIsIiQiLCJnZXRJdGVtQW1vdW50Iiwic2VsZWN0b3IiLCJzbGlkZXMiLCJ3aW5kb3ciLCJpbm5lcldpZHRoIiwib24iLCJ0b2dnbGVBY2Nlc3NvcmllcyIsImRpc3BsYXlJdGVtcyIsInBhcmVudCIsImF0dHIiLCJzaWJsaW5ncyIsImhpZGUiLCJ2YWx1ZSIsInNob3ciLCIkc2xpZGVyIiwiZmxleHNsaWRlciIsImFuaW1hdGlvbiIsIml0ZW1XaWR0aCIsIml0ZW1NYXJnaW4iLCJjb250cm9sTmF2IiwibWluSXRlbXMiLCJtYXhJdGVtcyIsInNsaWRlc2hvdyIsImRhdGEiLCJjb250YWluZXIiLCJ3aWR0aCIsImZpbmQiLCJmbGlja2l0eSIsInJlc2l6ZSIsImdyaWRTaXplIiwidmFycyIsImN1cnJlbnRTbGlkZSIsImFuaW1hdGluZ1RvIiwiZG9NYXRoIiwiY2hhbmdlIiwiZWFjaCIsImFkanVzdFNsaWRlckJ1dHRvbnMiLCJjaGlsZHJlbiIsImxlbmd0aCIsImNsb3Nlc3QiLCJoYXNDbGFzcyIsImhlYWRlckhlaWdodCIsImhlaWdodCIsImNzcyIsImZuIiwibGlnaHRHYWxsZXJ5IiwidGh1bWJuYWlsIiwic2hvd1RodW1iQnlEZWZhdWx0Iiwic3ViSHRtbFNlbGVjdG9yUmVsYXRpdmUiLCJyZXNldCIsImUiLCJwcmV2ZW50RGVmYXVsdCIsInZhbCJdLCJtYXBwaW5ncyI6IkFBQUE7QUFDQUEsTUFBTSxDQUFDQyxRQUFELENBQU4sQ0FBaUJDLEtBQWpCLENBQXVCLFVBQUNDLENBQUQsRUFBTztBQUM1QjtBQUNGO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNFLFdBQVNDLGFBQVQsQ0FBdUJDLFFBQXZCLEVBQWlDO0FBQy9CLFFBQUlDLE1BQU0sR0FBRyxDQUFiOztBQUNBLFFBQUlDLE1BQU0sQ0FBQ0MsVUFBUCxHQUFvQixHQUF4QixFQUE2QjtBQUMzQkYsTUFBQUEs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QkFBRCxDQUFELENBQWdDTSxFQUFoQyxDQUFtQyxPQUFuQyxFQUE0QyxTQUFTNEMsS0FBVCxDQUFlQyxDQUFmLEVBQWtCO0FBQzVEQSxJQUFBQSxDQUFDLENBQUNDLGNBQUY7QUFDQXBELElBQUFBLENBQUMsQ0FBQyxzQ0FBRCxDQUFELENBQTBDcUQsR0FBMUMsQ0FBOEMsRUFBOUM7QUFDQXJELElBQUFBLENBQUMsQ0FBQyxvREFBRCxDQUFELENBQXdEWSxJQUF4RCxDQUE2RCxNQUE3RDtBQUNBWixJQUFBQSxDQUFDLENBQUMsSUFBRCxDQUFELENBQVFZLElBQVI7QUFDRCxHQUxEO0FBTUQsQ0EvR0QiLCJzb3VyY2VzQ29udGVudCI6WyIvKiBnbG9iYWwgalF1ZXJ5ICovXG5qUXVlcnkoZG9jdW1lbnQpLnJlYWR5KCgkKSA9PiB7XG4gIC8qKlxuICAgKiBHZXRzIHJlbGF0ZWQgYWNjZXNzb3JpZXMgZGlzcGxheWVkIGl0ZW1zLlxuICAgKlxuICAgKiBAcGFyYW0ge3N0cmluZ30gc2VsZWN0b3JcbiAgICpcbiAgICogQHJldHVybiB7aW50fSBSZWxhdGVkIGFjY2Vzc29yaWVzIGRpc3BsYXllZCBpdGVtcy5cbiAgICovXG4gIGZ1bmN0aW9uIGdldEl0ZW1BbW91bnQoc2VsZWN0b3IpIHtcbiAgICBsZXQgc2xpZGVzID0gMTtcbiAgICBpZiAod2luZG93LmlubmVyV2lkdGggPiA3NjcpIHtcbiAgICAgIHNsaWRlcyA9IHNlbGVjdG9yID09PSAncmVsYXRlZC1hY2Nlc3Nvcmllcy0tc3VtbWFyeScgPyAzIDogNDtcbiAgICB9XG4gICAgcmV0dXJuIHNsaWRlcztcbiAgfVxuXG4gIC8qKlxuICAgKiBUb2dnbGUgcmVsYXRlZCBhY2Nlc3NvcmllcyBzbGlkZXNob3dzLlxuICAgKi9cbiAgJCgnLnJlbGF0ZWQtYWNjZXNzb3JpZXMgc2VsZWN0LCAucmVsYXRlZC1hY2Nlc3Nvcmllcy0tc3VtbWFyeSBzZWxlY3QnKS5vbignY2hhbmdlJywgZnVuY3Rpb24gdG9nZ2xlQWNjZXNzb3JpZXMoKSB7XG4gICAgY29uc3QgZGlzcGxheUl0ZW1zID0gZ2V0SXRlbUFtb3VudCgkKHRoaXMpLnBhcmVudCgpLmF0dHIoJ2NsYXNzJykpO1xuICAgICQodGhpcykuc2libGluZ3MoJ1tjbGFzc3w9XCJzbGlkZXNob3dcIl0nKS5oaWRlKCdmYXN0Jyk7XG4gICAgJCh0aGlzKS5zaWJsaW5ncyhgLnNsaWRlc2hvdy0ke3RoaXMudmFsdWV9YCkuc2hvdygoKSA9PiB7XG4gICAgICBjb25zdCAkc2xpZGVyID0gJCh0aGlzKTtcbiAgICAgICRzbGlkZXIuZmxleHNsaWRlcih7XG4gICAgICAgIGFuaW1hdGlvbjogJ3NsaWRlJyxcbiAgICAgICAgaXRlbVdpZHRoOiAyMTAsXG4gICAgICAgIGl0ZW1NYXJnaW46IDAsXG4gICAgICAgIGNvbnRyb2xOYXY6IGZhbHNlLFxuICAgICAgICBtaW5JdGVtczogZGlzcGxheUl0ZW1zLFxuICAgICAgICBtYXhJdGVtczogZGlzcGxheUl0ZW1zLFxuICAgICAgICBzbGlkZXNob3c6IGZhbHNlLFxuICAgICAgfSk7XG4gICAgICBjb25zdCBmbGV4c2xpZGVyID0gJHNsaWRlci5kYXRhKCdmbGV4c2xpZGVyJyk7XG4gICAgICAvLyBUdXJuIHdpZHRoIGNhbGN1bGF0ZWQgc2xpZGVyIGludG8gYW4gYWN0dWFsIFwiZmxleHNsaWRlclwiLlxuICAgICAgZmxleHNsaWRlci5jb250YWluZXIud2lkdGgoJ2F1dG8nKTtcblxuICAgICAgLy8gVHJpZ2dlcnMgcmVzaXplIGV2ZW50IGluIG9yZGVyIHRvIHJlZnJlc2ggZmxpY2tpdHlcbiAgICAgIC8vIGZvciB0aGUgdmFyaWF0aW9uIHNsaWRlcnMuXG4gICAgICAkc2xpZGVyLmZpbmQoJy5qcy1nYWxsZXJ5YS1zbGlkZXIuZmxpY2tpdHktZW5hYmxlZCcpLmZsaWNraXR5KCdyZXNpemUnKTtcblxuICAgICAgJCh3aW5kb3cpLnJlc2l6ZSgoKSA9PiB7XG4gICAgICAgIGNvbnN0IGdyaWRTaXplID0gZ2V0SXRlbUFtb3VudCgpO1xuICAgICAgICAvLyBFbnN1cmUgc2xpZGVyIHRvIGRpc3BsYXkgY29ycmVjdCBhbW91bnQgb2YgaXRlbXMuXG4gICAgICAgIC8vIEV2ZW4gaWYgdGhlIGl0ZW0gd2lkdGggaXMgc2V0IHZpYSBDU1MsIEZsZXhzbGlkZXIgZG9lcyBub3Qga25vd1xuICAgICAgICAvLyBob3cgbXVjaCBpdGVtcyBhcmUgaW4gdGhlIHZpZXdwb3J0LlxuICAgICAgICBmbGV4c2xpZGVyLnZhcnMubWluSXRlbXMgPSBncmlkU2l6ZTtcbiAgICAgICAgZmxleHNsaWRlci52YXJzLm1heEl0ZW1zID0gZ3JpZFNpemU7XG5cbiAgICAgICAgLy8gUmVzZXQgc2xpZGVyIHRvIHplcm8gYXMgaXQgbWF5IGJyZWFrIG90aGVyd2lzZSBhZnRlciByZXNpemluZy5cbiAgICAgICAgZmxleHNsaWRlci5jdXJyZW50U2xpZGUgPSAwO1xuICAgICAgICBmbGV4c2xpZGVyLmFuaW1hdGluZ1RvID0gMDtcbiAgICAgICAgZmxleHNsaWRlci5kb01hdGgoKTtcbiAgICAgIH0pO1xuICAgIH0pO1xuXG4gICAgLy8gU2hvdy9IaWRlIHJlc2V0IGJ1dHRvbi5cbiAgICBpZiAodGhpcy52YWx1ZSAhPT0gJycpIHtcbiAgICAgICQoJyNyZXNldF9yZWxhdGVkX2FjY2Vzc29yaWVzJykuc2hvdygpO1xuICAgIH0gZWxzZSB7XG4gICAgICAkKCcjcmVzZXRfcmVsYXRlZF9hY2Nlc3NvcmllcycpLmhpZGUoKTtcbiAgICB9XG4gIH0pO1xuICAvLyBGb3JjZXMgcmVsYXRlZCBhY2Nlc3NvcmllcyBub3RpY2Ugc2xpZGVzaG93IHRvIGJlIHNob3duIG9uIHBhZ2UgbG9hZC5cbiAgJCgnLnJlbGF0ZWQtYWNjZXNzb3JpZXMgc2VsZWN0JykuY2hhbmdlKCk7XG5cbiAgLyoqXG4gICAqIFJlbW92ZXMgRmxpY2tpdHkgYnV0dG9ucyBpZiBzbGlkZXNob3cgZWxlbWVudHMgYXJlIGxlc3MgdGhhbiAzLlxuICAgKi9cbiAgJCgnLnJlbGF0ZWQtcHJvZHVjdHNfX3NsaWRlciAuZmxpY2tpdHktc2xpZGVyJykuZWFjaChmdW5jdGlvbiBhZGp1c3RTbGlkZXJCdXR0b25zKCkge1xuICAgIGlmICgkKHRoaXMpLmNoaWxkcmVuKCdsaScpLmxlbmd0aCA8IDMgJiYgJCh3aW5kb3cpLndpZHRoKCkgPiA3NjgpIHtcbiAgICAgIC8vIGVzbGludC1kaXNhYmxlLW5leHQtbGluZSBtYXgtbGVuXG4gICAgICAkKHRoaXMpLmNsb3Nlc3QoJy5yZWxhdGVkLXByb2R1Y3RzX19zbGlkZXInKS5jaGlsZHJlbignLmZsaWNraXR5LWJ1dHRvbicpLmhpZGUoKTtcbiAgICB9XG4gIH0pO1xuXG4gIC8qKlxuICAgKiBSZS1pbml0aWFsaXplcyBnYWxsZXJ5YSBsaWdodGJveCBhZnRlciBxdWljayB2aWV3IGlzIGRpc3BsYXllZC5cbiAgICovXG4gICQoZG9jdW1lbnQpLm9uKCdxdl9sb2FkZXJfc3RvcCcsICgpID0+IHtcbiAgICAvLyBQcmV2ZW50cyBoZWFkZXIgdG8gcGFydGlhbGx5IGNvdmVyIHF1aWNrIHZpZXcuXG4gICAgaWYgKCEkKCcuc2l0ZS1oZWFkZXInKS5oYXNDbGFzcygnc2l0ZS1oZWFkZXItLWNvbGxhcHNlZCcpKSB7XG4gICAgICBjb25zdCBoZWFkZXJIZWlnaHQgPSAkKCcuc2l0ZS1oZWFkZXInKS5oZWlnaHQoKTtcbiAgICAgICQoJy55aXRoLXdjcXYtd3JhcHBlcicpLmNzcygnbWFyZ2luLXRvcCcsIGAke2hlYWRlckhlaWdodCArIDIwfXB4YCk7XG4gICAgfVxuICAgIC8vIFN1cHBvcnQgZm9yIGxpZ2h0Z2FsbGVyeS5cbiAgICBpZiAodHlwZW9mICQuZm4ubGlnaHRHYWxsZXJ5ID09PSAnZnVuY3Rpb24nKSB7XG4gICAgICAkKCcuanMtZ2FsbGVyeWEtbGlnaHRib3gnKS5saWdodEdhbGxlcnkoe1xuICAgICAgICB0aHVtYm5haWw6IHRydWUsXG4gICAgICAgIHNob3dUaHVtYkJ5RGVmYXVsdDogZmFsc2UsXG4gICAgICAgIHN1Ykh0bWxTZWxlY3RvclJlbGF0aXZlOiB0cnVlLFxuICAgICAgICBzZWxlY3RvcjogJy5nYWxsZXJ5YV9faW1hZ2UgPiBhJyxcbiAgICAgIH0pO1xuICAgICAgJCgnLndvb2NvbW1lcmNlLXByb2R1Y3QtZ2FsbGVyeScpLmxpZ2h0R2FsbGVyeSh7XG4gICAgICAgIHRodW1ibmFpbDogdHJ1ZSxcbiAgICAgICAgc2hvd1RodW1iQnlEZWZhdWx0OiBmYWxzZSxcbiAgICAgICAgc3ViSHRtbFNlbGVjdG9yUmVsYXRpdmU6IHRydWUsXG4gICAgICAgIHNlbGVjdG9yOiAnLndvb2NvbW1lcmNlLXByb2R1Y3QtZ2FsbGVyeV9faW1hZ2UgPiBhJyxcbiAgICAgIH0pO1xuICAgIH1cbiAgfSk7XG5cbiAgLyoqXG4gICAqIFJlc2V0IHJlbGF0ZWQgYWNjZXNzb3JpZXMgYnV0dG9uLlxuICAgKi9cbiAgJCgnI3Jlc2V0X3JlbGF0ZWRfYWNjZXNzb3JpZXMnKS5vbignY2xpY2snLCBmdW5jdGlvbiByZXNldChlKSB7XG4gICAgZS5wcmV2ZW50RGVmYXVsdCgpO1xuICAgICQoJy5yZWxhdGVkLWFjY2Vzc29yaWVzLS1zdW1tYXJ5IHNlbGVjdCcpLnZhbCgnJyk7XG4gICAgJCgnLnJlbGF0ZWQtYWNjZXNzb3JpZXMtLXN1bW1hcnkgW2NsYXNzfD1cInNsaWRlc2hvd1wiXScpLmhpZGUoJ2Zhc3QnKTtcbiAgICAkKHRoaXMpLmhpZGUoKTtcbiAgfSk7XG59KTtcbiJdLCJmaWxlIjoibWFpbi5qcyJ9