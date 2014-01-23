<?php

/**
 * @file
 * Definition of Drupal\chgk\PackStorageController.
 */

namespace Drupal\chgk;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\Query\QueryInterface;
use Drupal\Core\Entity\FieldableDatabaseStorageController;

/**
 * Defines a Controller class for question package
 */
class PackStorageController extends FieldableDatabaseStorageController implements PackStorageControllerInterface {
  /**
   * {@inheritdoc}
   */
  public function loadChildren($pid) {
    $query = $this->database->select('chgk_pack', 'p');
    $query->addField('p', 'pid');
    $query->condition('p.parent', $pid);
    $query->orderBy('p.weight');
    $query->orderBy('p.title');
    return $query->execute()->fetchCol();
  }
  
  protected function buildQuery($ids, $revision_id = FALSE) {
    $query = parent::buildQuery( $ids, $revision_id );
    $query->leftJoin($this->entityInfo->getBaseTable(), 'base2', "base.{$this->idKey} = base2.parent");
    $query->groupBy("base.{$this->idKey}");
    $query->addExpression('COUNT(*)', 'tour_count');
    return $query;
  }
  
  protected function doLoadFieldItems($entities, $age) {
    parent::doLoadFieldItems($entities, $age);
    $ids = array_keys($entities);
    $results = $this->database->select('chgk_pack', 'p')
        ->fields('p')
        ->condition('parent', $ids, 'IN')
        ->orderBy('weight')
        ->execute();
    $counts=array();
    foreach ($results as $row) {
      $pid = $row->parent;
      if (!isset($counts[$pid])) $counts[$pid] = 0;
      $entities[$row->parent]->getTranslation('x-default')->{'tours'}[$counts[$pid]] = array('target_id' => $row->pid);
      $counts[$pid]++;
    }
  }

}
