<?php

/**
 * @file
 * Contains \Drupal\chgk\QuestionAccessController\QuestionAccessControllerTest
 */

namespace Drupal\chgk\Tests;

use Drupal\chgk\QuestionAccessController;
use Drupal\Tests\UnitTestCase;
use Drupal\system\Tests\Entity\EntityUnitTestBase;
use Drupal\Core\Language\Language;

/**
 * Tests the node bulk form plugin.
 *
 * @see Drupal\chgk\QuestionAccessController
 */
class QuestionAccessControllerTest extends UnitTestCase {
  public static function getInfo() {
    return array(
      'name' => 'Chgk: Question access controller',
      'description' => 'Tests Question access controller',
      'group' => 'Chgk',
    );
  }
  
  public function setUp() {
    $this->question = $this->getMockBuilder('Drupal\chgk\Entity\Question')
      ->disableOriginalConstructor()
      ->getMock();
     $this->entityType =  $this->getMockBuilder('Drupal\Core\Entity\EntityType')->disableOriginalConstructor()
      ->getMock();
     $this->moduleHandler = $this->getMock('Drupal\Core\Extension\ModuleHandlerInterface');
     $this->moduleHandler->expects($this->any())->method('invokeAll')->will($this->returnValue(array()));
     $this->questionAccessController = new QuestionAccessController($this->entityType);
     $this->questionAccessController->setModuleHandler($this->moduleHandler);
  } 
  
  protected function getAccountMock( $permissions = array()) {
    if (!is_array($permissions)) $permissions = array($permissions);
    $account = $this->getMockBuilder('Drupal\Core\Session\AccountInterface')->getMock();
    $account->expects($this->any())->method('id')->will($this->returnValue(rand()));
    $account->expects($this->any())->method('hasPermission')->will(
      $this->returnCallback( function ( $perm ) use ($permissions) {return in_array($perm, $permissions);})
    );
    return $account;
  }
  
  public function testAccess() {
    $account = $this->getAccountMock();
    $this->assertFalse($this->questionAccessController->access($this->question, 'update', Language::LANGCODE_DEFAULT, $account));
    $this->assertFalse($this->questionAccessController->access($this->question, 'view', Language::LANGCODE_DEFAULT, $account));
    $this->assertFalse($this->questionAccessController->access($this->question, 'delete', Language::LANGCODE_DEFAULT, $account));

    $account = $this->getAccountMock('access content');
    $this->assertTrue($this->questionAccessController->access($this->question, 'view', Language::LANGCODE_DEFAULT, $account));

    $account = $this->getAccountMock('edit questions');
    $this->assertTrue($this->questionAccessController->access($this->question, 'update', Language::LANGCODE_DEFAULT, $account));

    $account = $this->getAccountMock('delete questions');
    $this->assertTrue($this->questionAccessController->access($this->question, 'delete', Language::LANGCODE_DEFAULT, $account));

  }
  
  public function checkCreateAccess() {
    $account = $this->getAccountMock();
    $this->assertFalse($this->questionAccessController->createAccess(NULL, $account));
    $account = $this->getAccountMock('create questions');
    $this->assertTrue($this->questionAccessController->createAccess(NULL, $account));

  }
}

