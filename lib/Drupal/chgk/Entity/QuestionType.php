<?php

/**
 * @file
 * Contains \Drupal\node\Entity\QuestionType.
 */

namespace Drupal\chgk\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\chgk\QuestionTypeInterface;
use Drupal\Core\Entity\EntityTypeInterface;


/**
 * Defines the Question type configuration entity.
 *
 * @ConfigEntityType(
 *   id = "chgk_question_type",
 *   label = @Translation("Question type"),
 *   controllers = {
 *     "form" = {
 *       "add" = "Drupal\chgk\QuestionTypeFormController",
 *       "edit" = "Drupal\chgk\QuestionTypeFormController",
 *       "delete" = "Drupal\chgk\Form\QuestionTypeDeleteConfirm"
 *     },
 *     "list_builder" = "Drupal\chgk\QuestionTypeListBuilder",
 *   },
 *   admin_permission = "administer question types",
 *   config_prefix = "question_type",
 *   bundle_of = "chgk_question",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "edit-form" = "chgk.question_type_edit"     
 *   }
 * )
 */
class QuestionType extends ConfigEntityBase implements QuestionTypeInterface {

  /**
   * The machine name of this question type.
   *
   * @var string
   */
  public $id;

  /**
   * The UUID of the question type.
   *
   * @var string
   */
  public $uuid;

  /**
   * The human-readable name of the question type.
   *
   * @var string
   */
  public $label;

  /**
   * A brief description of this question type.
   *
   * @var string
   */
  public $description;


  /**
   * Module-specific settings for this node type, keyed by module name.
   *
   * @var array
   *
   * @todo Pluginify.
   */
  public $settings = array();

  /**
   * {@inheritdoc}
   */
  public function id() {
    return $this->id;
  }

  /**
   * {@inheritdoc}
   */
  public function postSave(EntityStorageInterface $storage, $update = TRUE) {
    parent::postSave($storage, $update);
    $this->clearUpdateCache($update);
    if ( $update && $this->getOriginalId() != $this->id() ) {
      $this->moveQuestions();
    }
  }

  /**
   * Moves questions after type name is changed
   */
  private function moveQuestions() {
    //  To be implemented
  }

  /**
   * Clears caches after update
   * 
   * @param bool $update
   *   TRUE if the entity has been updated, or FALSE if it has been inserted.
   */
  private function clearUpdateCache($update) {
    if (!$update || $this->getOriginalId() != $this->id()) {
      // Clear the question type cache, so the new type appears or old type was renamed.
      \Drupal::cache()->deleteTags(array('chgk_question_types' => TRUE));
    }
    else {
      // Invalidate the cache tag of the updated node type only.
      cache()->invalidateTags(array('chgk_question_type' => $this->id()));
    }
  } 
}