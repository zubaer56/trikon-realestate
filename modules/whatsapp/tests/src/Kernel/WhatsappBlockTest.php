<?php

namespace Drupal\Tests\whatsapp\Kernel;

use Drupal\KernelTests\KernelTestBase;
use Drupal\Tests\block\Traits\BlockCreationTrait;

/**
 * Tests the WhatsApp block.
 *
 * @group whatsapp
 */
class WhatsappBlockTest extends KernelTestBase {

  use BlockCreationTrait;

  /**
   * Modules to install.
   *
   * @var array
   */
  protected static $modules = ['block', 'whatsapp', 'whatsapp_test', 'key', 'system', 'user'];

  /**
   * The block being tested.
   *
   * @var \Drupal\whatsapp\Plugin\Block
   */
  protected $block;

  /**
   * The block storage.
   *
   * @var \Drupal\Core\Config\Entity\ConfigEntityStorageInterface
   */
  protected $storage;

  /**
   * The renderer.
   *
   * @var \Drupal\Core\Render\RendererInterface
   */
  protected $renderer;

  /**
   * The key value.
   *
   * @var string
   */
  protected $key = 'muahahaha';

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->installConfig(['whatsapp_test', 'key', 'whatsapp']);
    $this->storage = $this->container
      ->get('entity_type.manager')
      ->getStorage('block');

    $this->block = $this->storage->create([
      'id' => 'whatsappblock_test',
      'theme' => 'stark',
      'plugin' => 'whatsapp_block',
    ]);
    $this->block->save();

    $this->container->get('cache.render')->deleteAll();

    $this->renderer = $this->container->get('renderer');
  }

  /**
   * Tests the rendering of blocks.
   */
  public function testBasicRendering() {
    $entity = $this->storage->load('whatsappblock_test');
    $builder = $this->container->get('entity_type.manager')->getViewBuilder('block');
    $output = $builder->view($entity, 'block');

    $expected = [];
    $expected[] = '<div id="block-whatsappblock-test">';
    $expected[] = '  ';
    $expected[] = '    ';
    $expected[] = '      <script defer src="//widget.tochat.be/bundle.js?key=' . $this->key . '"></script>';
    $expected[] = '  </div>';
    $expected[] = '';
    $expected_output = implode("\n", $expected);
    $this->assertEquals($expected_output, $this->renderer->renderRoot($output));
  }

}
