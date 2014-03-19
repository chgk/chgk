<?php

/**
 * @file
 * Definition of Drupal\chgk\Tests\ChgkTestBase.
 */

namespace Drupal\chgk\Tests;

use Drupal\simpletest\WebTestBase;

/**
 * Sets up page and article content types.
 */
abstract class ChgkTestBase extends WebTestBase {
  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = array('chgk', 'datetime');

  /**
   * Creates a custom content type based on default settings.
   *
   * @param array $values
   *   An array of settings to change from the defaults.
   *   Example: 'type' => 'foo'.
   *
   * @return \Drupal\node\Entity\NodeType
   *   Created content type.
   */
  protected function createQuestionType(array $values = array()) {
    // Find a non-existent random type name.
    if (!isset($values['type'])) {
      do {
        $id = strtolower($this->randomName(8));
      } while (chgk_question_type_load($id));
    }
    else {
      $id = $values['type'];
    }
    $values += array(
      'id' => $id,
      'label' => $id,
    );
    $type = entity_create('chgk_question_type', $values);
    $status = $type->save();
    menu_router_rebuild();

    $this->assertEqual($status, SAVED_NEW, t('Created question type %type.', array('%type' => $type->id())));

    return $type;
  }
}
