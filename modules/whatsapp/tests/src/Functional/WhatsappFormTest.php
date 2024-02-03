<?php

namespace Drupal\Tests\whatsapp\Functional;

use Drupal\Tests\BrowserTestBase;

/**
 * WhatsApp functional test.
 *
 * @group whatsapp
 */
class WhatsappFormTest extends BrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['whatsapp', 'key', 'user'];

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stable';

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->adminUser = $this->drupalCreateUser([
      'whatsapp configuration form',
      'administer keys',
    ]);
    $this->drupalLogin($this->adminUser);
  }

  /**
   * Tests the config form.
   */
  public function testSettingsForm() {
    $this->drupalGet('admin/config/system/keys/add');

    $testingKey = [
      'id' => 'testing_key',
      'label' => 'Testing Key',
      'key_type' => 'authentication',
      'key_input_settings[key_value]' => str_pad('', 4, 'z'),
    ];

    $this->submitForm($testingKey, 'Save');

    $this->drupalGet('admin/config/system/keys/add');

    $changedKey = [
      'id' => 'changed_key',
      'label' => 'Changed Key',
      'key_type' => 'authentication',
      'key_input_settings[key_value]' => str_pad('', 4, 'y'),
    ];

    $this->submitForm($changedKey, 'Save');

    $assert_session = $this->assertSession();

    $edit = [
      'id' => 'testing_key',
    ];

    $this->drupalGet('admin/config/services/whatsapp');
    $this->submitForm($edit, 'Save configuration');

    $assert_session->statusCodeEquals(200);
    $widget_key_testing = $this->container->get('config.factory')->get('whatsapp.settings')->get('widget_key');
    $this->assertEquals('Testing Key', $this->container->get('key.repository')->getKey($widget_key_testing)->label());

    $change = [
      'id' => 'changed_key',
    ];

    $this->drupalGet('admin/config/services/whatsapp');
    $this->submitForm($change, 'Save configuration');

    $assert_session->statusCodeEquals(200);
    $widget_key_changed = $this->container->get('config.factory')->get('whatsapp.settings')->get('widget_key');
    $this->assertEquals('Changed Key', $this->container->get('key.repository')->getKey($widget_key_changed)->label());
  }

}
