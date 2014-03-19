<?php

/**
 * @file
 * Definition of Drupal\chgk\Tests\ChgkQuestionTypeTest.
 */

namespace Drupal\chgk\Tests;

/**
 * Tests related to node types.
 */
class ChgkQuestionTypeTest extends ChgkTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = array('field_ui', 'chgk', 'datetime');

  public static function getInfo() {
    return array(
      'name' => 'Question types',
      'description' => 'Ensures that question type functions work correctly.',
      'group' => 'Chgk',
    );
  }


  /**
   * Tests creating a question type programmatically and via a form.
   */
  function testQuestionTypeCreation() {
    // Create a content type programmaticaly.
    $type = $this->createQuestionType();

    $type_exists = (bool) entity_load('chgk_question_type', $type->id);
    $this->assertTrue($type_exists, 'The new question type has been created');

/*    // Login a test user.
    $web_user = $this->drupalCreateUser(array('create ' . $type->label . ' content'));
    $this->drupalLogin($web_user);

    $this->drupalGet('/add/' . $type->type);
    $this->assertResponse(200, 'The new content type can be accessed at node/add.');
*/
    // Create a content type via the user interface.
    $web_user = $this->drupalCreateUser(array('administer question types'));
    $this->drupalLogin($web_user);
    $edit = array(
      'label' => 'foo',
      'id' => 'foo',
    );
    $this->drupalPostForm('admin/structure/question_types/add', $edit, t('Save question type'));
    $type_exists = (bool) entity_load('chgk_question_type', 'foo');
    $this->assertTrue($type_exists, 'The new question type has been created');
  }

}
