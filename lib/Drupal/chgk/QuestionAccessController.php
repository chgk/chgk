<?php

/**
 * @file
 * Contains \Drupal\chgk\QuestionAccessController.
 */

namespace Drupal\chgk;

use Drupal\Core\Entity\EntityAccessController;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Defines an access controller for the chgk question entity.
 *
 * @see \Drupal\chgk\Entity\Question
 */
class QuestionAccessController extends EntityAccessController {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, $langcode, AccountInterface $account) {
    switch ($operation) {
      case 'view':
      $r = $account->hasPermission('access content');
 
      var_dump($r);
        return $account->hasPermission('access content');
        break;

      case 'update':
        return $account->hasPermission('edit questions');
        break;

      case 'delete':
        return $account->hasPermission('delete questions');
        break;
    }
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return $account->hasPermission('create questions');
  }

}
