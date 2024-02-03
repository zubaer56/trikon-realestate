/**
 * @file
 * Contains definition of the behaviour ScrollReveal.js.
 */

(function ($, Drupal, drupalSettings, once) {
  "use strict";

  const debug = drupalSettings.scrollreveal.debug;
  const configs = drupalSettings.scrollreveal.configs;
  console.log(configs);

  Drupal.behaviors.scrollRevealInit = {
    attach: function (context, settings) {

      const elements = drupalSettings.scrollreveal.elements;

      $.each(elements, function (index, element) {
        let target = element.target;

        if (once('scrollrevealjs', target).length) {
          let options = {
            distance: Number(element.distance) + 'px',
            delay: Number(element.delay),
            duration: Number(element.duration),
            interval: Number(element.interval),
            opacity: Number(element.opacity),
            easing: element.easing,
            scale: Number(element.scale),
            rotate: {
              x: parseInt(element.rotate.x),
              y: parseInt(element.rotate.y),
              z: parseInt(element.rotate.z),
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

          new Drupal.scrollRevealJS(target, options);
        }
      });

    }
  };

  Drupal.scrollRevealJS = function (target, options) {
    if (debug) {
      console.log(options);
      ScrollReveal.debug = debug;
    }

    // Initial ScrollReveal.
    ScrollReveal().reveal(target, options);
  };

})(jQuery, Drupal, drupalSettings, once);
