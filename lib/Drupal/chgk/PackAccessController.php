<?php

/**
 * @file
 * Contains \Drupal\chgk\PackAccessController.
 */

namespace Drupal\chgk;

use Drupal\Core\Entity\EntityAccessController;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Defines an access controller for the chgk question entity.
 *
 * @see \Drupal\chgk\Entity\Pack
 */
class PackAccessController extends EntityAccessController {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, $langcode, AccountInterface $account) {
    switch ($operation) {
      case 'view':
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
