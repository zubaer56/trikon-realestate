/**
 * @file
 * Contains definition of the behaviour ScrollReveal.js.
 */

(function ($, Drupal, drupalSettings, once) {
  "use strict";

  const debug = drupalSettings.scrollreveal.debug;
  const sample = drupalSettings.scrollreveal.options;
  const target = sample.target;
  const configs = drupalSettings.scrollreveal.configs;
  console.log(configs);

  Drupal.behaviors.scrollRevealForm = {
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
              cleanup: configs.cleanup,
              reset: configs.reset,
              desktop: configs.desktop,
              mobile: configs.mobile,
              useDelay: configs.useDelay,
              viewFactor: configs.viewFactor,
              viewOffset: {}
            };

            if (options.easing === 'cubic-bezier') {
              options.easing = 'cubic-bezier(0.5, 0, 0, 1)';
            }

            if (configs.container !== 'document.documentElement' && configs.container !== '') {
              options.container = configs.container;
            }

            if (parseInt(configs.viewOffset.top) !== 0) {
              options.viewOffset.top = parseInt(configs.viewOffset.top);
            }
            if (parseInt(configs.viewOffset.right) !== 0) {
              options.viewOffset.right = parseInt(configs.viewOffset.right);
            }
            if (parseInt(configs.viewOffset.bottom) !== 0) {
              options.viewOffset.bottom = parseInt(configs.viewOffset.bottom);
            }
            if (parseInt(configs.viewOffset.left) !== 0) {
              options.viewOffset.left = parseInt(configs.viewOffset.left);
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
