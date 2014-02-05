<?php

/**
 * @file
 * Definition of Drupal\chgk\PackViewBuilder.
 */

namespace Drupal\chgk;

use Drupal\Core\Entity\EntityViewBuilder;

/**
 * Render controller for nodes.
 */
class PackViewBuilder extends EntityViewBuilder {
  /**
   * {@inheritdoc}
   */
  public function buildContent(array $entities, array $displays, $view_mode, $langcode = NULL) {
    parent::buildContent($entities, $displays, $view_mode, $langcode);
    if ($displays['chgk_pack']->getComponent('tours')) {
      $this->buildTours($entities);
    }
  }
  
  private function buildTours($entities) {
    $storage = $this->entityManager->getStorageController('chgk_pack');
    foreach ($entities as $entity) {
      $tours = $entity->get('tours');
      $ids = array();
      foreach ($tours as $delta => $tour) {
        if ($tour->target_id !== NULL) {
          $ids[] = $tour->target_id;
        } else {
          unset($tours[$delta]);
        }
      }
      $target_entities = entity_load_multiple('chgk_pack', $ids);
      $elements = array();
      foreach ($tours as $delta => $tour) {
        if (!isset($target_entities[$tour->target_id]))   continue;
        $target_entity = clone $target_entities[$tour->target_id];
        $elements[$delta] = entity_view($target_entity, 'full');
      }
      $entity->content['tours'] = $elements;
    }
  }
}
