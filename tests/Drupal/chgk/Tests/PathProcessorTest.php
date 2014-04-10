<?php

/**
 * @file
 * Contains \Drupal\chgk\QuestionAccessController\QuestionAccessControllerTest
 */

namespace Drupal\chgk\Tests;

use Drupal\chgk\PathProcessor;
use Drupal\Tests\UnitTestCase;

/**
 * Tests the questions access controller
 *
 * @group Drupal
 * @group chgk
 *
 * @see Drupal\chgk\QuestionAccessController
 */
class PathProcessorTest extends UnitTestCase {
  public static function getInfo() {
    return array(
      'name' => 'Chgk: Path processor',
      'description' => 'Tests Path processor',
      'group' => 'Chgk',
    );
  }

  public function setUp() {
    $this->request = $this->getMockBuilder('Symfony\Component\HttpFoundation\Request')->getMock();

    $storages['chgk_pack'] = $this->getMockBuilder('Drupal\chgk\PackStorageController')
       ->disableOriginalConstructor()
       ->getMock();
    $storages['chgk_pack']->expects( $this->any() )->method('idToMachineName')->will(
       $this->returnCallback(function( $id ) {return preg_match('/\D/', $id)?FALSE:"name$id";})
    );
    $storages['chgk_pack']->expects( $this->any() )->method('machineNameToId')->will(
       $this->returnCallback(function( $machine_name ) {$res = preg_replace('/\D/', '', $machine_name);return $res?$res:FALSE;})
    );

    $getStorageCallback = function ( $entity_name ) use ($storages) {
        return $storages[ $entity_name ];
    };

    $this->entityManager = $this->getMockBuilder('Drupal\Core\Entity\EntityManager')
      ->disableOriginalConstructor()
      ->getMock();

    $this->entityManager->expects( $this->any() )->method('getStorage')->will(
      $this->returnCallback($getStorageCallback));

    $this->pathProcessor = new PathProcessor( $this->entityManager );
  }

  public function testProcessInbound() {
    $this->assertEquals($this->pathProcessor->processInbound('tour/name123', $this->request), 'pack/123');
    $this->assertEquals($this->pathProcessor->processInbound('tour/namewrong', $this->request), 'tour/namewrong');
  }

  public function testProcessOutbound() {
    $options = array();
    $this->assertEquals($this->pathProcessor->processOutbound('pack/123', $options, $this->request), 'tour/name123');
    $this->assertEquals($this->pathProcessor->processOutbound('pack/wrong', $options, $this->request), 'pack/wrong');
    $options['alias'] = 'ALIAS';
    $this->assertEquals($this->pathProcessor->processOutbound('pack/123', $options, $this->request), 'pack/123');
  }


}