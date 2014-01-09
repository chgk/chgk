<?php

/**
 * @file
 * Definition of Drupal\chgk\QuestionViewBuilder.
 */

namespace Drupal\chgk;

use Drupal\Core\Entity\EntityViewBuilder;

/**
 * Render controller for nodes.
 */
class QuestionViewBuilder extends EntityViewBuilder {
  /**
   * {@inheritdoc}
   */
  public function buildContent(array $entities, array $displays, $view_mode, $langcode = NULL) {
    parent::buildContent($entities, $displays, $view_mode, $langcode);
    // We want to add ':' after text field name
    // @todo It should not be here. Actually the theme should determine the label format
    foreach ($entities as $entity) {
      foreach (element_children($entity->content) as $field_name) {
        if ($entity->content[$field_name]['#field_type']=='text_long') {
          $entity->content[$field_name]['#title'].=':';
        }
      }
    }
  }

}
