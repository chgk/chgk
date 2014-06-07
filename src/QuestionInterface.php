<?php

/**
 * @file
 * Contains Drupal\chgk\QuestionInterface.
 */

namespace Drupal\chgk;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;

/**
 * Provides an interface defining a question entity.
 */
interface QuestionInterface extends ContentEntityInterface, EntityChangedInterface {

}
