<?php

/**
 * @file
 * Contains \Drupal\chgk\PackStorageControllerInterface.
*/

namespace Drupal\chgk;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityStorageInterface;

/**
 * Defines a common interface for taxonomy term entity controller classes.
 */
interface PackStorageInterface extends EntityStorageInterface {

  /**
   * Finds all children of a pack ID.
   *
   * @param int $tid
   *   Pack ID to retrieve children for.
   *
   * @return array
   *   An array of pack ids that are the children of the pack $tid.
   */
  public function loadChildren($pid);

}
