<?php

/**
 * @file
 * Contains \Drupal\chgk\Tests\Menu\ChgkLocalTasksTest.
 */

namespace Drupal\chgk\Tests\Menu;

use Drupal\Tests\Core\Menu\LocalTaskIntegrationTest;

/**
 * Tests existence of action local tasks.
 *
 * @group Drupal
 * @group chgk
 */
class ChgkLocalTasksTest extends LocalTaskIntegrationTest {

  public static function getInfo() {
    return array(
      'name' => 'Chgk local tasks test',
      'description' => 'Chgk action local tasks.',
      'group' => 'chgk',
    );
  }

  public function setUp() {
    $this->directoryList = array('chgk' => 'modules/chgk');
    parent::setUp();
  }

  /**
   * Tests local task existence.
   */
  public function testActionLocalTasks() {
    $this->assertLocalTasks('chgk.question_type_edit', array(array('chgk.question_type_edit')));
    $this->assertLocalTasks('chgk.overview_question_types', array(array('chgk.overview_question_types')));
    $this->assertLocalTasks('chgk.question_view', array(array('chgk.question_view', 'chgk.question_edit', 'chgk.question_delete_confirm')));
    $this->assertLocalTasks('chgk.pack_view', array(array('chgk.pack_view', 'chgk.pack_edit', 'chgk.pack_delete_confirm')));
  }
}
