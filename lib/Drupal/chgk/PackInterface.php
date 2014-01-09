<?php

/**
 * @file
 * Contains \Drupal\chgk\PackInterface.
 */

namespace Drupal\chgk;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;

/**
 * Provides an interface defining a question entity.
 */
interface PackInterface extends ContentEntityInterface, EntityChangedInterface {

}
