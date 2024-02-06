<?php

namespace Drupal\scrollrevealjs_ui\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Defines a form that configures ScrollReveal settings.
 */
class ScrollRevealSettings extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'scrollreveal_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // Attach ScrollReveal settings library.
    $form['#attached']['library'][] = 'scrollrevealjs_ui/scrollreveal-settings';

    // Get current settings.
    $config = $this->config('scrollrevealjs.settings');

    $form['settings'] = [
      '#type'  => 'details',
      '#title' => $this->t('ScrollReveal settings'),
      '#open'  => TRUE,
    ];

    // Let module handle load ScrollReveal.js library.
    $form['settings']['load'] = [
      '#type'          => 'checkbox',
      '#title'         => $this->t('Load ScrollReveal library'),
      '#default_value' => $config->get('load'),
      '#description'   => $this->t("If enabled, this module will attempt to load the ScrollReveal library for your site. To prevent loading twice, leave this option disabled if you're including the assets manually or through another module or theme."),
    ];

    // Let module handle load ScrollReveal.js library.
    $form['settings']['debug'] = [
      '#type'          => 'checkbox',
      '#title'         => $this->t('Debug mode'),
      '#default_value' => $config->get('debug'),
      '#description'   => $this->t("Controls whether or not to output help messages to the console when unexpected things occur at runtime."),
    ];

    // Show warning missing library and lock on cdn method.
    $method = $config->get('method');
    $method_lock_change = FALSE;
    if (!scrollrevealjs_check_installed()) {
      $method = 'cdn';
      $method_lock_change = TRUE;
      $method_warning = $this->t('You cannot set local due to the ScrollReveal.js javascript library is missing. Please <a href=":downloadUrl" rel="external" target="_blank">Download the library</a> and and extract to "/libraries/scrollreveal" directory.', [
        ':downloadUrl' => 'https://github.com/jlmakes/scrollreveal/archive/master.zip',
      ]);

      // Hide library warning message.
      $form['settings']['hide'] = [
        '#type'          => 'checkbox',
        '#title'         => $this->t('Hide warning'),
        '#default_value' => $config->get('hide') ?? FALSE,
        '#description'   => $this->t("If you want to use the CDN without installing the local library, you can turn off the warning."),
      ];

      $form['settings']['method_warning'] = [
        '#type'   => 'item',
        '#markup' => '<div class="library-status-report">' . $method_warning . '</div>',
        '#states' => [
          'invisible' => [
            ':input[name="hide"]' => ['checked' => TRUE],
          ],
        ],
      ];
    }

    // Load method library from CDN or Locally.
    $form['settings']['method'] = [
      '#type'          => 'select',
      '#title'         => $this->t('Attach method'),
      '#options'       => [
        'local' => $this->t('Local'),
        'cdn'   => $this->t('CDN'),
      ],
      '#default_value' => $method,
      '#description'   => $this->t('These settings control how the ScrollReveal library is loaded. You can choose to load from the CDN (External source) or from the local.'),
      '#disabled'      => $method_lock_change,
    ];

    // Production or minimized version.
    $form['settings']['minimized'] = [
      '#type'        => 'fieldset',
      '#title'       => $this->t('Development or Production version'),
      '#collapsible' => TRUE,
      '#collapsed'   => FALSE,
    ];
    $form['settings']['minimized']['minimized_options'] = [
      '#type'          => 'radios',
      '#options'       => [
        0 => $this->t('Use non-minimized library (Development)'),
        1 => $this->t('Use minimized library (Production)'),
      ],
      '#title'         => $this->t('Choose minimized or non-minimized library.'),
      '#description'   => $this->t('These settings work both methods with locally and CDN library. If debug enabled, make sure you’re working with the unminified distribution; the minified distribution cannot output to the console!'),
      '#default_value' => $config->get('minimized.options'),
    ];

    // Load ScrollReveal.js library Per-path.
    $form['settings']['url'] = [
      '#type'        => 'fieldset',
      '#title'       => $this->t('Load on specific URLs'),
      '#collapsible' => TRUE,
      '#collapsed'   => TRUE,
    ];
    $form['settings']['url']['url_visibility'] = [
      '#type'          => 'radios',
      '#title'         => $this->t('Load ScrollReveal.js on specific pages'),
      '#options'       => [
        0 => $this->t('All pages except those listed'),
        1 => $this->t('Only the listed pages'),
      ],
      '#default_value' => $config->get('url.visibility'),
    ];
    $form['settings']['url']['url_pages'] = [
      '#type'          => 'textarea',
      '#title'         => '<span class="element-invisible">' . $this->t('Pages') . '</span>',
      '#default_value' => _scrollrevealjs_ui_array_to_string($config->get('url.pages')),
      '#description'   => $this->t("Specify pages by using their paths. Enter one path per line. The '*' character is a wildcard. An example path is %admin-wildcard for every user page. %front is the front page.", [
        '%admin-wildcard' => '/admin/*',
        '%front'          => '<front>',
      ]),
    ];

    // ScrollReveal.js default options.
    $form['options'] = [
      '#type'  => 'details',
      '#title' => $this->t('ScrollReveal default options'),
      '#open'  => TRUE,
    ];

    // ScrollReveal.js distance.
    $form['options']['distance'] = [
      '#type'          => 'number',
      '#title'         => $this->t('Distance'),
      '#description'   => $this->t('Controls how far elements move when revealed. If your element already has 50px of X‑translation and you specify Distance "50px" and Origin "right", your element will initialize with 100px of X‑translation and animate to the computed value of 50px when revealed.'),
      '#default_value' => $config->get('options.distance'),
      '#field_suffix'  => 'px',
      '#attributes'    => ['class' => ['scrollreveal-distance']],
    ];

    // ScrollReveal.js delay.
    $form['options']['delay'] = [
      '#type'          => 'number',
      '#min'           => 0,
      '#title'         => $this->t('Delay'),
      '#description'   => $this->t('Is the time before reveal animations begin. By default, delay will be used for all reveal animations, but "Use delay" can be used to change when delay is applied. However, animations triggered by "Reset" will never use delay.'),
      '#default_value' => $config->get('options.delay'),
      '#field_suffix'  => 'ms',
      '#attributes'    => ['class' => ['scrollreveal-delay']],
    ];

    // ScrollReveal.js duration.
    $form['options']['duration'] = [
      '#type'          => 'number',
      '#title'         => $this->t('Duration'),
      '#description'   => $this->t('Controls how long animations take to complete.'),
      '#default_value' => $config->get('options.duration'),
      '#field_suffix'  => 'ms',
      '#attributes'    => ['class' => ['scrollreveal-duration']],
    ];

    // ScrollReveal.js interval.
    $form['options']['interval'] = [
      '#type'          => 'number',
      '#title'         => $this->t('Interval'),
      '#description'   => $this->t('Is the time between each reveal. Since animations are optimized for 60 frames/sec (or 16 ms/frame), non-zero values less than 16 are rounded up to 16.'),
      '#default_value' => $config->get('options.interval'),
      '#field_suffix'  => 'ms',
      '#attributes'    => ['class' => ['scrollreveal-interval']],
    ];

    // ScrollReveal.js opacity.
    $form['options']['opacity'] = [
      '#type'          => 'number',
      '#min'           => 0,
      '#max'           => 1,
      '#step'          => 0.1,
      '#title'         => $this->t('Opacity'),
      '#description'   => $this->t('Specifies the opacity they have prior to being revealed.'),
      '#default_value' => $config->get('options.opacity'),
      '#attributes'    => ['class' => ['scrollreveal-opacity']],
    ];

    // ScrollReveal.js easing functions.
    $form['options']['easing'] = [
      '#type'          => 'select',
      '#options'       => scrollrevealjs_easing_functions(),
      '#title'         => $this->t('Easing'),
      '#description'   => $this->t('Controls how animations transition between their start and end values.'),
      '#default_value' => $config->get('options.easing'),
    ];

    // ScrollReveal.js origin options.
    $form['options']['origin'] = [
      '#type'          => 'select',
      '#options'       => scrollrevealjs_origin_options(),
      '#title'         => $this->t('Origin'),
      '#description'   => $this->t('Specifies what direction elements come from when revealed. You will need a non-zero value assigned to Distance for Origin to have any visible impact on your animations.'),
      '#default_value' => $config->get('options.origin'),
    ];

    // ScrollReveal.js scale.
    $form['options']['scale'] = [
      '#type'          => 'number',
      '#min'           => 0,
      '#step'          => 0.1,
      '#title'         => $this->t('Scale'),
      '#description'   => $this->t('specifies the size of elements have prior to being revealed. CSS Transforms are preserved. If your element already a scale of 1.5 and you specify Scale 0.5, your element will initialize with a scale of 0.75 (50% its computed size) and animate back to the computed value of 1.5 when revealed.'),
      '#default_value' => $config->get('options.scale'),
      '#attributes'    => ['class' => ['scrollreveal-scale']],
    ];

    // ScrollReveal.js rotate.
    $form['options']['rotate'] = [
      '#type'          => 'fieldset',
      '#title'         => $this->t('Rotate'),
      '#description'   => $this->t('Specifies the rotation elements have prior to being revealed. CSS Transforms are preserved. For example, if your element already has 45° of Z‑rotation and you specify Rotate { z: 20 }, your element will initialize with 65° of Z‑rotation and animate back to the computed value of 45° when revealed.'),
      '#attributes'    => ['class' => ['scrollreveal-rotate']],
    ];
    $form['options']['rotate']['rotate_x'] = [
      '#type'          => 'number',
      '#title'         => $this->t('X'),
      '#default_value' => $config->get('options.rotate.x'),
      '#attributes'    => ['class' => ['scrollreveal-rotate-x']],
    ];
    $form['options']['rotate']['rotate_y'] = [
      '#type'          => 'number',
      '#title'         => $this->t('Y'),
      '#default_value' => $config->get('options.rotate.y'),
      '#attributes'    => ['class' => ['scrollreveal-rotate-y']],
    ];
    $form['options']['rotate']['rotate_z'] = [
      '#type'          => 'number',
      '#title'         => $this->t('Z'),
      '#default_value' => $config->get('options.rotate.z'),
      '#attributes'    => ['class' => ['scrollreveal-rotate-z']],
    ];

    // ScrollReveal.js default options config.
    $form['configs'] = [
      '#type'  => 'details',
      '#title' => $this->t('ScrollReveal global configs'),
      '#open'  => FALSE,
    ];

    // ScrollReveal.js container.
    $form['configs']['container'] = [
      '#type'          => 'textfield',
      '#title'         => $this->t('Container'),
      '#description'   => $this->t('Is used as the viewport, when determining element visibility. This is the element that ScrollReveal binds event listeners to. The reveal effect occurs when elements enter their viewport. Your custom containers must have their own scrollbars, or their children will always be "visible" and reveal immediately on page load.'),
      '#default_value' => $config->get('configs.container'),
    ];

    // ScrollReveal.js cleanup.
    $form['configs']['cleanup'] = [
      '#type'          => 'checkbox',
      '#title'         => $this->t('Cleanup'),
      '#default_value' => $config->get('configs.cleanup'),
      '#description'   => $this->t('When Cleanup is true, ScrollReveal will call clean() on your reveal target once they complete their animation. Keep in mind, animations using option.reset true will never “complete”. If you are calling sync() in your project, you likely do not want to enable options.cleanup: visible elements that have already revealed will reveal again after each call!'),
    ];

    // ScrollReveal.js reset.
    $form['configs']['reset'] = [
      '#type'          => 'checkbox',
      '#title'         => $this->t('Reset'),
      '#default_value' => $config->get('configs.reset'),
      '#description'   => $this->t('Enables/Disables elements returning to their initialized position when they leave the viewport. When true elements reveal each time they enter the viewport instead of once. Be careful over-using this effect!'),
    ];

    // ScrollReveal.js desktop.
    $form['configs']['desktop'] = [
      '#type'          => 'checkbox',
      '#title'         => $this->t('Desktop'),
      '#default_value' => $config->get('configs.desktop'),
      '#description'   => $this->t('Enables/Disables animations on desktop browsers.'),
    ];

    // ScrollReveal.js mobile.
    $form['configs']['mobile'] = [
      '#type'          => 'checkbox',
      '#title'         => $this->t('Mobile'),
      '#default_value' => $config->get('configs.mobile'),
      '#description'   => $this->t('Enables/Disables animations on mobile browsers.'),
    ];

    // ScrollReveal.js useDelay.
    $form['configs']['use_delay'] = [
      '#type'          => 'select',
      '#options'       => scrollrevealjs_use_delay_options(),
      '#title'         => $this->t('Use delay'),
      '#description'   => $this->t('Specifies when values assigned to options.delay are used.'),
      '#default_value' => $config->get('configs.useDelay'),
      '#attributes'    => ['class' => ['scrollreveal-use-delay']],
    ];

    // ScrollReveal.js viewFactor.
    $form['configs']['view_factor'] = [
      '#type'          => 'number',
      '#min'           => 0,
      '#max'           => 1,
      '#step'          => 0.1,
      '#title'         => $this->t('View factor'),
      '#description'   => $this->t('Specifies what portion of an element must be within the viewport for it to be considered visible. Be careful using this option with really tall elements! Instead, see if you can achieve your desired effect using "View Offset".'),
      '#default_value' => $config->get('configs.viewFactor'),
      '#attributes'    => ['class' => ['scrollreveal-view-factor']],
    ];

    // ScrollReveal.js viewOffset.
    $form['configs']['view_offset'] = [
      '#type'          => 'fieldset',
      '#title'         => $this->t('View offset'),
      '#description'   => $this->t('Expands/Contracts the active boundaries of the viewport when calculating element visibility.'),
      '#attributes'    => ['class' => ['scrollreveal-view-offset']],
    ];
    $form['configs']['view_offset']['view_offset_top'] = [
      '#type'          => 'number',
      '#title'         => $this->t('Top'),
      '#default_value' => $config->get('configs.viewOffset.top'),
      '#attributes'    => ['class' => ['scrollreveal-view-offset-top']],
    ];
    $form['configs']['view_offset']['view_offset_right'] = [
      '#type'          => 'number',
      '#title'         => $this->t('Right'),
      '#default_value' => $config->get('configs.viewOffset.right'),
      '#attributes'    => ['class' => ['scrollreveal-view-offset-right']],
    ];
    $form['configs']['view_offset']['view_offset_bottom'] = [
      '#type'          => 'number',
      '#title'         => $this->t('Bottom'),
      '#default_value' => $config->get('configs.viewOffset.bottom'),
      '#attributes'    => ['class' => ['scrollreveal-view-offset-bottom']],
    ];
    $form['configs']['view_offset']['view_offset_left'] = [
      '#type'          => 'number',
      '#title'         => $this->t('Left'),
      '#default_value' => $config->get('configs.viewOffset.left'),
      '#attributes'    => ['class' => ['scrollreveal-view-offset-left']],
    ];

    // ScrollReveal.js preview.
    $form['preview'] = [
      '#type'  => 'details',
      '#title' => $this->t('Animate preview'),
      '#open'  => TRUE,
    ];

    // ScrollReveal.js animation preview.
    $form['preview']['sample'] = [
      '#type'   => 'markup',
      '#markup' => '<div class="scrollreveal__preview"><div class="scrollreveal__sample">ScrollReveal!</div></div>',
    ];

    // Replay button for preview ScrollReveal.js current configs.
    $form['preview']['replay'] = [
      '#value'      => $this->t('Rebuild'),
      '#type'       => 'button',
      '#attributes' => ['class' => ['scrollreveal__replay']],
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    // @todo Field verification.
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();

    // Save the updated ScrollReveal.js settings.
    $this->config('scrollrevealjs.settings')
      ->set('load', $values['load'])
      ->set('debug', $values['debug'])
      ->set('hide', isset($values['hide']) && $values['hide'] !== 0 ?? FALSE)
      ->set('method', $values['method'])
      ->set('minimized.options', $values['minimized_options'])
      ->set('url.visibility', $values['url_visibility'])
      ->set('url.pages', _scrollrevealjs_ui_string_to_array($values['url_pages']))
      ->set('options.distance', $values['distance'])
      ->set('options.delay', $values['delay'])
      ->set('options.duration', $values['duration'])
      ->set('options.easing', $values['easing'])
      ->set('options.interval', $values['interval'])
      ->set('options.opacity', $values['opacity'])
      ->set('options.origin', $values['origin'])
      ->set('options.scale', $values['scale'])
      ->set('options.rotate.x', $values['rotate_x'])
      ->set('options.rotate.y', $values['rotate_y'])
      ->set('options.rotate.z', $values['rotate_z'])
      ->set('configs.container', $values['container'])
      ->set('configs.cleanup', $values['cleanup'])
      ->set('configs.reset', $values['reset'])
      ->set('configs.desktop', $values['desktop'])
      ->set('configs.mobile', $values['mobile'])
      ->set('configs.useDelay', $values['use_delay'])
      ->set('configs.viewFactor', $values['view_factor'])
      ->set('configs.viewOffset.top', $values['view_offset_top'])
      ->set('configs.viewOffset.right', $values['view_offset_right'])
      ->set('configs.viewOffset.bottom', $values['view_offset_bottom'])
      ->set('configs.viewOffset.left', $values['view_offset_left'])
      ->save();

    // Flush caches so the updated config can be checked.
    drupal_flush_all_caches();

    parent::submitForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'scrollrevealjs.settings',
    ];
  }

}
