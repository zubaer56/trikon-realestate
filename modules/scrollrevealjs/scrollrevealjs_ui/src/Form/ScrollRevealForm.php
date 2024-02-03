<?php

namespace Drupal\scrollrevealjs_ui\Form;

use Drupal\Component\Datetime\TimeInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\scrollrevealjs_ui\ScrollRevealManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * ScrollReveal JS add and edit form.
 *
 * @internal
 */
class ScrollRevealForm extends FormBase {

  /**
   * Animate manager.
   *
   * @var \Drupal\scrollrevealjs_ui\ScrollRevealManagerInterface
   */
  protected $targetManager;

  /**
   * A config object for the ScrollReveal settings.
   *
   * @var \Drupal\Core\Config\Config
   */
  protected $config;

  /**
   * The time service.
   *
   * @var \Drupal\Component\Datetime\TimeInterface
   */
  protected $time;

  /**
   * Constructs a new ScrollReveal object.
   *
   * @param \Drupal\scrollrevealjs_ui\ScrollRevealManagerInterface $target_manager
   *   The ScrollReveal manager.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   A config factory for retrieving required config objects.
   * @param \Drupal\Component\Datetime\TimeInterface $time
   *   The time service.
   */
  public function __construct(ScrollRevealManagerInterface $target_manager, ConfigFactoryInterface $config_factory, TimeInterface $time) {
    $this->targetManager = $target_manager;
    $this->config = $config_factory->get('scrollrevealjs.settings');
    $this->time = $time;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('scrollrevealjs.target_manager'),
      $container->get('config.factory'),
      $container->get('datetime.time'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'scrollreveal_form';
  }

  /**
   * {@inheritdoc}
   *
   * @param array $form
   *   A nested array form elements comprising the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   * @param int $sid
   *   (optional) ScrollReveal id to be passed on to
   *   \Drupal::formBuilder()->getForm() for use as the default value of the
   *   ScrollReveal ID form data.
   */
  public function buildForm(array $form, FormStateInterface $form_state, int $sid = 0) {
    // Attach ScrollReveal form library.
    $form['#attached']['library'][] = 'scrollrevealjs_ui/scrollreveal-form';

    // Get ScrollReveal data by ID.
    $scrollreveal = $this->targetManager->findById($sid) ?? [];

    // Set stored ScrollReveal data.
    $target  = $scrollreveal['target'] ?? '';
    $label   = $scrollreveal['label'] ?? '';
    $comment = $scrollreveal['comment'] ?? '';
    $status  = $scrollreveal['status'] ?? TRUE;
    $options = [];

    // Handle the case when $scrollreveal is not an array or option is not set.
    if (is_array($scrollreveal) && isset($scrollreveal['options'])) {
      $options = unserialize($scrollreveal['options'], ['allowed_classes' => FALSE]) ?? '';
    }

    // Store animate id.
    $form['scrollreveal_id'] = [
      '#type'  => 'value',
      '#value' => $sid,
    ];

    // Load the ScrollReveal JS configuration settings.
    $config = $this->config;

    // The default selector.
    $form['target'] = [
      '#title'         => $this->t('Selector'),
      '#type'          => 'textfield',
      '#required'      => TRUE,
      '#size'          => 64,
      '#maxlength'     => 256,
      '#default_value' => $target,
      '#description'   => $this->t('Enter a valid element or a css selector.'),
    ];

    // The label of this selector.
    $form['label'] = [
      '#title'         => $this->t('Label'),
      '#type'          => 'textfield',
      '#required'      => FALSE,
      '#size'          => 64,
      '#maxlength'     => 64,
      '#default_value' => $label ?? '',
      '#description'   => $this->t('The label for this scrollreveal selector like <em>About block</em>.'),
    ];

    // ScrollReveal options.
    $form['options'] = [
      '#title' => $this->t('ScrollReveal options'),
      '#type'  => 'details',
      '#open'  => TRUE,
    ];

    // ScrollReveal.js distance.
    $form['options']['distance'] = [
      '#type'          => 'number',
      '#title'         => $this->t('Distance'),
      '#description'   => $this->t('Controls how far elements move when revealed. If your element already has 50px of X‑translation and you specify Distance "50px" and Origin "right", your element will initialize with 100px of X‑translation and animate to the computed value of 50px when revealed.'),
      '#default_value' => $options['distance'] ?? $config->get('options.distance'),
      '#field_suffix'  => 'px',
      '#attributes'    => ['class' => ['scrollreveal-distance']],
    ];

    // ScrollReveal.js delay.
    $form['options']['delay'] = [
      '#type'          => 'number',
      '#min'           => 0,
      '#title'         => $this->t('Delay'),
      '#description'   => $this->t('Is the time before reveal animations begin. By default, delay will be used for all reveal animations, but "Use delay" can be used to change when delay is applied. However, animations triggered by "Reset" will never use delay.'),
      '#default_value' => $options['delay'] ?? $config->get('options.delay'),
      '#field_suffix'  => 'ms',
      '#attributes'    => ['class' => ['scrollreveal-delay']],
    ];

    // ScrollReveal.js duration.
    $form['options']['duration'] = [
      '#type'          => 'number',
      '#title'         => $this->t('Duration'),
      '#description'   => $this->t('Controls how long animations take to complete.'),
      '#default_value' => $options['duration'] ?? $config->get('options.duration'),
      '#field_suffix'  => 'ms',
      '#attributes'    => ['class' => ['scrollreveal-duration']],
    ];

    // ScrollReveal.js interval.
    $form['options']['interval'] = [
      '#type'          => 'number',
      '#title'         => $this->t('Interval'),
      '#description'   => $this->t('Is the time between each reveal. Since animations are optimized for 60 frames/sec (or 16 ms/frame), non-zero values less than 16 are rounded up to 16.'),
      '#default_value' => $options['interval'] ?? $config->get('options.interval'),
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
      '#default_value' => $options['opacity'] ?? $config->get('options.opacity'),
      '#attributes'    => ['class' => ['scrollreveal-opacity']],
    ];

    // ScrollReveal.js easing functions.
    $form['options']['easing'] = [
      '#type'          => 'select',
      '#options'       => scrollrevealjs_easing_functions(),
      '#title'         => $this->t('Easing'),
      '#description'   => $this->t('Controls how animations transition between their start and end values.'),
      '#default_value' => $options['easing'] ?? $config->get('options.easing'),
    ];

    // ScrollReveal.js origin options.
    $form['options']['origin'] = [
      '#type'          => 'select',
      '#options'       => scrollrevealjs_origin_options(),
      '#title'         => $this->t('Origin'),
      '#description'   => $this->t('Specifies what direction elements come from when revealed. You will need a non-zero value assigned to Distance for Origin to have any visible impact on your animations.'),
      '#default_value' => $options['origin'] ?? $config->get('options.origin'),
    ];

    // ScrollReveal.js scale.
    $form['options']['scale'] = [
      '#type'          => 'number',
      '#min'           => 0,
      '#step'          => 0.1,
      '#title'         => $this->t('Scale'),
      '#description'   => $this->t('specifies the size of elements have prior to being revealed. CSS Transforms are preserved. If your element already a scale of 1.5 and you specify Scale 0.5, your element will initialize with a scale of 0.75 (50% its computed size) and animate back to the computed value of 1.5 when revealed.'),
      '#default_value' => $options['scale'] ?? $config->get('options.scale'),
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
      '#default_value' => $options['rotate']['x'] ?? $config->get('options.rotate.x'),
      '#attributes'    => ['class' => ['scrollreveal-rotate-x']],
    ];
    $form['options']['rotate']['rotate_y'] = [
      '#type'          => 'number',
      '#title'         => $this->t('Y'),
      '#default_value' => $options['rotate']['y'] ?? $config->get('options.rotate.y'),
      '#attributes'    => ['class' => ['scrollreveal-rotate-y']],
    ];
    $form['options']['rotate']['rotate_z'] = [
      '#type'          => 'number',
      '#title'         => $this->t('Z'),
      '#default_value' => $options['rotate']['z'] ?? $config->get('options.rotate.z'),
      '#attributes'    => ['class' => ['scrollreveal-rotate-z']],
    ];

    // ScrollReveal.js preview.
    $form['preview'] = [
      '#type'  => 'details',
      '#title' => $this->t('Preview'),
      '#open'  => TRUE,
    ];

    // ScrollReveal.js animation preview.
    $form['preview']['sample'] = [
      '#type'   => 'markup',
      '#markup' => '<div class="scrollreveal__preview"><div class="scrollreveal__sample">Animate On Scroll!</div></div>',
    ];

    // Replay button for preview ScrollReveal.js current configs.
    $form['preview']['replay'] = [
      '#value'      => $this->t('Rebuild'),
      '#type'       => 'button',
      '#attributes' => ['class' => ['scrollreveal__replay']],
    ];

    // The comment for describe animate settings and usage in website.
    $form['comment'] = [
      '#type'          => 'textarea',
      '#title'         => $this->t('Comment'),
      '#description'   => $this->t('Describe this animate settings and usage in your website.'),
      '#default_value' => $comment ?? '',
      '#rows'          => 2,
      '#weight'        => 96,
    ];

    // Enabled status for this animate.
    $form['status'] = [
      '#type'          => 'checkbox',
      '#title'         => $this->t('Enabled'),
      '#description'   => $this->t('Animate will appear on pages that have this target.'),
      '#default_value' => $status ?? TRUE,
      '#weight'        => 99,
    ];

    $form['actions'] = ['#type' => 'actions'];
    $form['actions']['submit'] = [
      '#type'        => 'submit',
      '#button_type' => 'primary',
      '#value'       => $this->t('Save'),
      '#submit'      => [[$this, 'submitForm']],
    ];

    if ($sid != 0) {
      // Add a 'Remove' button for animate form.
      $form['actions']['delete'] = [
        '#type'       => 'link',
        '#title'      => $this->t('Delete'),
        '#url'        => Url::fromRoute('scrollreveal.delete', ['sid' => $sid]),
        '#attributes' => [
          'class' => [
            'action-link',
            'action-link--danger',
            'action-link--icon-trash',
          ],
        ],
      ];

      // Redirect to list for submit handler on edit form.
      $form['actions']['submit']['#submit'] = ['::submitForm', '::overview'];
    }
    else {
      // Add a 'Save and go to list' button for add form.
      $form['actions']['overview'] = [
        '#type'   => 'submit',
        '#value'  => $this->t('Save and go to list'),
        '#submit' => array_merge($form['actions']['submit']['#submit'], ['::overview']),
        '#weight' => 20,
      ];
    }

    return $form;
  }

  /**
   * Submit handler for removing target.
   *
   * @param array[] $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public function remove(&$form, FormStateInterface $form_state) {
    $sid = $form_state->getValue('scrollreveal_id');
    $form_state->setRedirect('scrollreveal.delete', ['sid' => $sid]);
  }

  /**
   * Form submission handler for the 'overview' action.
   *
   * @param array[] $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public function overview(array $form, FormStateInterface $form_state): void {
    $form_state->setRedirect('scrollreveal.admin');
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $sid    = $form_state->getValue('scrollreveal_id');
    $is_new = $sid == 0;
    $target = trim($form_state->getValue('target'));

    if ($is_new) {
      if ($this->targetManager->isScrollReveal($target)) {
        $form_state->setErrorByName('target', $this->t('This target is already exists.'));
      }
    }
    else {
      if ($this->targetManager->findById($sid)) {
        $animate = $this->targetManager->findById($sid);

        if ($target != $animate['target'] && $this->targetManager->isScrollReveal($target)) {
          $form_state->setErrorByName('target', $this->t('This target is already added.'));
        }
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Get all form field values.
    $values = $form_state->getValues();

    // Set main ScrollReveal database column data.
    $sid     = $values['scrollreveal_id'];
    $target  = trim($values['target']);
    $label   = trim($values['label']);
    $comment = trim($values['comment']);
    $status  = $values['status'];

    // Provide a label from target if was empty.
    if (empty($label)) {
      $label = ucfirst(trim(preg_replace("/[^a-zA-Z0-9]+/", " ", $target)));
    }

    // Set variables from main ScrollReveal settings.
    $variables['distance']    = $values['distance'];
    $variables['delay']       = $values['delay'];
    $variables['duration']    = $values['duration'];
    $variables['interval']    = $values['interval'];
    $variables['opacity']     = $values['opacity'];
    $variables['easing']      = $values['easing'];
    $variables['origin']      = $values['origin'];
    $variables['scale']       = $values['scale'];
    $variables['rotate']['x'] = $values['rotate_x'];
    $variables['rotate']['y'] = $values['rotate_y'];
    $variables['rotate']['z'] = $values['rotate_z'];

    // Serialize options variables.
    $options = serialize($variables);

    // The Unix timestamp when the ScrollReveal was most recently saved.
    $changed = $this->time->getCurrentTime();

    // Save ScrollReveal.
    $this->targetManager->addScrollReveal($sid, $target, $label, $comment, $changed, $status, $options);
    $this->messenger()
      ->addStatus($this->t('The target %target has been added.', ['%target' => $target]));

    // Flush caches so the updated config can be checked.
    drupal_flush_all_caches();
  }

}
