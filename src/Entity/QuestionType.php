<?php

/**
 * @file
 * Contains \Drupal\chgk\Entity\QuestionType.
 */

namespace Drupal\chgk\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;


/**
 * Defines the Question type configuration entity.
 *
 * @ConfigEntityType(
 *   id = "chgk_question_type",
 *   label = @Translation("Тип вопроса"),
 *   config_prefix = "question_type",
 *   bundle_of = "chgk_question",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *   },
 *   controllers = {
 *     "list_builder" = "Drupal\chgk\QuestionTypeListBuilder",
 *     "form" = {
 *       "add" = "Drupal\chgk\QuestionTypeForm",
 *       "edit" = "Drupal\chgk\QuestionTypeForm",
 *       "delete" = "Drupal\chgk\QuestionTypeDeleteConfirmForm"
 *     },
 *   },
 *   admin_permission = "administer content types",
 *   links = {
 *     "edit-form" = "chgk.question_type_edit",
 *     "delete-form" = "chgk.question_type_delete_confirm"
 *   }
 * )
 */
class QuestionType extends ConfigEntityBase {
  /**
   * The machine name of this question type.
   *
   * @var string
   */
  public $id;

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
}
