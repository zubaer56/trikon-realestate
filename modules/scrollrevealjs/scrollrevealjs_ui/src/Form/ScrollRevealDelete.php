<?php

namespace Drupal\scrollrevealjs_ui\Form;

use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\scrollrevealjs_ui\ScrollRevealManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Provides a form to remove target CSS selector.
 *
 * @internal
 */
class ScrollRevealDelete extends ConfirmFormBase {

  /**
   * The ScrollReveal.
   *
   * @var array
   */
  protected $scrollreveal;

  /**
   * The ScrollReveal manager.
   *
   * @var \Drupal\scrollrevealjs_ui\ScrollRevealManagerInterface
   */
  protected $targetManager;

  /**
   * Constructs a new ScrollRevealDelete object.
   *
   * @param \Drupal\scrollrevealjs_ui\ScrollRevealManagerInterface $target_manager
   *   The ScrollReveal target manager.
   */
  public function __construct(ScrollRevealManagerInterface $target_manager) {
    $this->targetManager = $target_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('scrollrevealjs.target_manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'scrollreveal_delete_form';
  }

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return $this->t('Are you sure you want to remove %target from ScrollReveal targets?', ['%target' => $this->scrollreveal['target']]);
  }

  /**
   * {@inheritdoc}
   */
  public function getConfirmText() {
    return $this->t('Delete');
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
    if (!$this->scrollreveal = $this->targetManager->findById($sid)) {
      throw new NotFoundHttpException();
    }
    $form['scrollreveal_id'] = [
      '#type'  => 'value',
      '#value' => $sid,
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $sid = $form_state->getValue('scrollreveal_id');
    $this->targetManager->removeScrollReveal($sid);
    $this->logger('user')
      ->notice('Deleted %target', ['%target' => $this->scrollreveal['target']]);
    $this->messenger()
      ->addStatus($this->t('The ScrollReveal target %target was deleted.', ['%target' => $this->scrollreveal['target']]));

    // Flush caches so the updated config can be checked.
    drupal_flush_all_caches();

    $form_state->setRedirectUrl($this->getCancelUrl());
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl() {
    return new Url('scrollreveal.admin');
  }

}
