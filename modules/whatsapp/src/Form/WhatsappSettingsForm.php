<?php

namespace Drupal\whatsapp\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure WhatsApp module settings.
 */
class WhatsappSettingsForm extends ConfigFormBase {

  /**
   * Config settings.
   *
   * @var string
   */
  const SETTINGS = 'whatsapp.settings';

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'whatsapp_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      static::SETTINGS,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config(static::SETTINGS);

    $form['widget_key'] = [
      '#type' => 'key_select',
      '#title' => $this->t('Widget key'),
      '#default_value' => $config->get('widget_key'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->configFactory->getEditable(static::SETTINGS)
      ->set('widget_key', $form_state->getValue('widget_key'))
      ->save();

    parent::submitForm($form, $form_state);
  }

}
