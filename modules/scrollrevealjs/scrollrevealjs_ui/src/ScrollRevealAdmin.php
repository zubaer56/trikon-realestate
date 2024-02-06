<?php

namespace Drupal\scrollrevealjs_ui;

use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormBuilderInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\scrollrevealjs_ui\Form\ScrollRevealFilter;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Displays ScrollReveal JS selector.
 *
 * @internal
 */
class ScrollRevealAdmin extends FormBase {

  /**
   * Animate manager.
   *
   * @var \Drupal\scrollrevealjs_ui\ScrollRevealManagerInterface
   */
  protected $targetManager;

  /**
   * The date formatter service.
   *
   * @var \Drupal\Core\Datetime\DateFormatterInterface
   */
  protected $dateFormatter;

  /**
   * The form builder.
   *
   * @var \Drupal\Core\Form\FormBuilderInterface
   */
  protected $formBuilder;

  /**
   * The current request.
   *
   * @var \Symfony\Component\HttpFoundation\Request
   */
  protected $currentRequest;

  /**
   * Constructs a new ScrollReveal object.
   *
   * @param \Drupal\scrollrevealjs_ui\ScrollRevealManagerInterface $target_manager
   *   The Animate selector manager.
   * @param \Drupal\Core\Datetime\DateFormatterInterface $date_formatter
   *   The date formatter service.
   * @param \Drupal\Core\Form\FormBuilderInterface $form_builder
   *   The form builder.
   * @param \Symfony\Component\HttpFoundation\Request $current_request
   *   The current request.
   */
  public function __construct(ScrollRevealManagerInterface $target_manager, DateFormatterInterface $date_formatter, FormBuilderInterface $form_builder, Request $current_request) {
    $this->targetManager  = $target_manager;
    $this->dateFormatter  = $date_formatter;
    $this->formBuilder    = $form_builder;
    $this->currentRequest = $current_request;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('scrollrevealjs.target_manager'),
      $container->get('date.formatter'),
      $container->get('form_builder'),
      $container->get('request_stack')->getCurrentRequest(),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'scrollreveal_admin_form';
  }

  /**
   * {@inheritdoc}
   *
   * @param array $form
   *   A nested array form elements comprising the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // Attach ScrollReveal JS admin list library.
    $form['#attached']['library'][] = 'scrollrevealjs_ui/scrollreveal-list';

    $search = $this->currentRequest->query->get('search');
    $status = $this->currentRequest->query->get('status') ?? NULL;

    /** @var \Drupal\scrollrevealjs_ui\Form\ScrollRevealFilter $form */
    $form['scrollreveal_admin_filter_form'] = $this->formBuilder->getForm(ScrollRevealFilter::class, $search, $status);
    $form['#attributes']['class'][] = 'scrollreveal-filter';
    $form['#attributes']['class'][] = 'views-exposed-form';

    $header = [
      [
        'data'  => $this->t('Selector'),
        'field' => 's.sid',
      ],
      [
        'data'  => $this->t('Label'),
        'field' => 's.label',
      ],
      [
        'data'  => $this->t('Status'),
        'field' => 's.status',
      ],
      [
        'data'  => $this->t('Updated'),
        'field' => 's.changed',
        'sort'  => 'desc',
      ],
      $this->t('Operations'),
    ];

    $rows = [];
    $result = $this->targetManager->findAll($header, $search, $status);
    foreach ($result as $scrollreveal) {
      $row = [];
      $row[] = $scrollreveal->target;
      $row[] = $scrollreveal->label;
      $row[] = $scrollreveal->status ? $this->t('Enabled') : $this->t('Disabled');
      $row[] = $this->dateFormatter->format($scrollreveal->changed, 'short');
      $links = [];
      $links['edit'] = [
        'title' => $this->t('Edit'),
        'url'   => Url::fromRoute('scrollreveal.edit', ['sid' => $scrollreveal->sid]),
      ];
      $links['delete'] = [
        'title' => $this->t('Delete'),
        'url'   => Url::fromRoute('scrollreveal.delete', ['sid' => $scrollreveal->sid]),
      ];
      $links['duplicate'] = [
        'title' => $this->t('Duplicate'),
        'url'   => Url::fromRoute('scrollreveal.duplicate', ['sid' => $scrollreveal->sid]),
      ];
      $row[] = [
        'data' => [
          '#type'  => 'operations',
          '#links' => $links,
        ],
      ];
      $rows[] = $row;
    }

    $form['scrollreveal_admin_table'] = [
      '#type'   => 'table',
      '#header' => $header,
      '#rows'   => $rows,
      '#empty'  => $this->t('No scrollreveal CSS selector available. <a href=":link">Add target</a> .', [
        ':link' => Url::fromRoute('scrollreveal.add')
          ->toString(),
      ]),
    ];

    $form['pager'] = ['#type' => 'pager'];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // @todo Add operations to ScrollReveal admin form.
  }

}
