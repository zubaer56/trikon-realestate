<?php

namespace Drupal\scrollrevealjs_ui\Form;

use Drupal\Component\Datetime\TimeInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\scrollrevealjs_ui\ScrollRevealManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a form to duplicate ScrollReveal.
 *
 * @internal
 */
class ScrollRevealDuplicate extends FormBase {

  /**
   * The ScrollReveal id.
   *
   * @var int
   */
  protected $scrollreveal;

  /**
   * The ScrollReveal target manager.
   *
   * @var \Drupal\scrollrevealjs_ui\ScrollRevealManagerInterface
   */
  protected $targetManager;

  /**
   * The time service.
   *
   * @var \Drupal\Component\Datetime\TimeInterface
   */
  protected $time;

  /**
   * Constructs a new ScrollRevealDuplicate object.
   *
   * @param \Drupal\scrollrevealjs_ui\ScrollRevealManagerInterface $target_manager
   *   The ScrollReveal target manager.
   * @param \Drupal\Component\Datetime\TimeInterface $time
   *   The time service.
   */
  public function __construct(ScrollRevealManagerInterface $target_manager, TimeInterface $time) {
    $this->targetManager = $target_manager;
    $this->time = $time;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('scrollrevealjs.target_manager'),
      $container->get('datetime.time'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'scrollreveal_duplicate_form';
  }

  /**
   * {@inheritdoc}
   *
   * @param array $form
   *   A nested array form elements comprising the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   * @param int $sid
   *   The ScrollReveal record ID to remove.
   */
  public function buildForm(array $form, FormStateInterface $form_state, $sid = 0) {
    $form['scrollreveal_id'] = [
      '#type'  => 'value',
      '#value' => $sid,
    ];

    // New target to duplicate effect.
    $form['target'] = [
      '#title'         => $this->t('Target'),
      '#type'          => 'textfield',
      '#required'      => TRUE,
      '#size'          => 64,
      '#maxlength'     => 255,
      '#default_value' => '',
      '#description'   => $this->t('Here, you can use HTML tag, class with dot(.) and ID with hash(#) prefix. Be sure your selector has plain text content. e.g. ".page-title" or ".block-title".'),
      '#placeholder'   => $this->t('Enter valid selector'),
    ];

    $form['actions'] = ['#type' => 'actions'];
    $form['actions']['submit'] = [
      '#type'        => 'submit',
      '#button_type' => 'primary',
      '#value'       => $this->t('Duplicate'),
    ];

    return $form;
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
        $scrollreveal = $this->targetManager->findById($sid);

        if ($target != $scrollreveal['target'] && $this->targetManager->isScrollReveal($target)) {
          $form_state->setErrorByName('target', $this->t('This target is already added.'));
        }
      }
    }
  }

  /**
   * Form submission handler for the 'duplicate' action.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   A reference to a keyed array containing the current state of the form.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();

    // Get duplicate ScrollReveal data by ID.
    $sid = $values['scrollreveal_id'];
    $scrollreveal = $this->targetManager->findById($sid);

    // Set duplicated ScrollReveal field values for new target.
    $target  = trim($values['target']);
    $label   = ucfirst(trim(preg_replace("/[^a-zA-Z0-9]+/", " ", $target)));
    $comment = $scrollreveal['comment'];
    $status  = 1;
    $options = $scrollreveal['options'];

    // The Unix timestamp when the ScrollReveal was most recently saved.
    $changed = $this->time->getCurrentTime();

    // Save ScrollReveal.
    $new_sid = $this->targetManager->addScrollReveal(0, $target, $label, $comment, $changed, $status, $options);
    $this->messenger()
      ->addStatus($this->t('The target %target has been duplicated.', ['%target' => $target]));

    // Flush caches so the updated config can be checked.
    drupal_flush_all_caches();

    // Redirect to duplicated ScrollReveal edit form.
    $form_state->setRedirect('scrollreveal.edit', ['sid' => $new_sid]);
  }

}
