/**
 * @file
 * Contains definition of the behaviour ScrollReveal.js.
 */

(function ($, Drupal, drupalSettings, once) {
  "use strict";

  const debug  = drupalSettings.scrollreveal.debug;
  const sample = drupalSettings.scrollreveal.options;
  const target = sample.target;

  Drupal.behaviors.scrollRevealSettings = {
    attach: function (context, settings) {

      if (once('scrollreveal__sample', target).length) {
        $(once('scrollreveal__replay', '.scrollreveal__replay', context)).on(
          'click',
          function (event) {
            $(target).attr('class', 'scrollreveal__sample');
            let options = {
              distance: $('#edit-distance').val() + 'px',
              delay: Number($('#edit-delay').val()),
              duration: Number($('#edit-duration').val()),
              interval: Number($('#edit-interval').val()),
              opacity: Number($('#edit-opacity').val()),
              easing: $('#edit-easing').val(),
              scale: Number($('#edit-scale').val()),
              rotate: {
                x: parseInt($('#edit-rotate-x').val()),
                y: parseInt($('#edit-rotate-y').val()),
                z: parseInt($('#edit-rotate-z').val()),
              },
              // ScrollReveal.js global configuration.
              cleanup: $('#edit-cleanup').is(':checked'),
              reset: $('#edit-reset').is(':checked'),
              desktop: $('#edit-desktop').is(':checked'),
              mobile: $('#edit-mobile').is(':checked'),
              useDelay: $('#edit-use-delay').val(),
              viewFactor: parseInt($('#edit-view-factor').val()),
              viewOffset: {}
            };

            if ($('#edit-container').val() !== 'document.documentElement' && $('#edit-container').val() !== '') {
              options.container = $('#edit-container').val();
            }
            if ($('#edit-view-offset-top').val() != 0) {
              options.viewOffset.top = parseInt($('#edit-view-offset-top').val());
            }
            if ($('#edit-view-offset-right').val() != 0) {
              options.viewOffset.right = parseInt($('#edit-view-offset-right').val());
            }
            if ($('#edit-view-offset-bottom').val() != 0) {
              options.viewOffset.bottom = parseInt($('#edit-view-offset-bottom').val());
            }
            if ($('#edit-view-offset-left').val() != 0) {
              options.viewOffset.left = parseInt($('#edit-view-offset-left').val());
            }
            setTimeout(function () {
              new Drupal.scrollRevealDemo(options);
            }, 10);
            event.preventDefault();
        }).trigger('click');

      }
    }
  };

  Drupal.scrollRevealDemo = function (options) {
    // Initial ScrollReveal.
    ScrollReveal().reveal(target, options);

    if (debug) {
      console.log(options);
      ScrollReveal.debug = debug;
    }
  };

})(jQuery, Drupal, drupalSettings, once);
