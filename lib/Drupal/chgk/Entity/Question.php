<?php

/**
 * @file
 * Definition of Drupal\chgk\Entity\Question.
 */

namespace Drupal\chgk\Entity;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\chgk\QuestionInterface;
use Drupal\Core\Field\FieldDefinition;
use Drupal\Core\Entity\EntityStorageControllerInterface;

/**
 * Defines the chgk question entity.
 *
 * @EntityType(
 *   id = "chgk_question",
 *   label = @Translation("Chgk Question"),
 *   bundle_label = @Translation("Question Type"),
 *   controllers = {
 *     "storage" = "Drupal\Core\Entity\FieldableDatabaseStorageController",
 *     "view_builder" = "Drupal\chgk\QuestionViewBuilder",
 *     "access" = "Drupal\chgk\QuestionAccessController",
 *     "form" = {
 *       "default" = "Drupal\chgk\QuestionFormController",
 *       "delete" = "Drupal\chgk\Form\QuestionDeleteForm",
 *       "edit" = "Drupal\chgk\QuestionFormController"
 *     },
 *     "translation" = "Drupal\chgk\QuestionTranslationController"
 *   },
 *   base_table = "chgk_question",
 *   uri_callback = "chgk_question_uri",
 *   fieldable = TRUE,
 *   translatable = TRUE,
 *   entity_keys = {
 *     "id" = "qid",
 *     "bundle" = "type",
 *     "uuid" = "uuid"
 *   },
 *   bundle_keys = {
 *     "bundle" = "id"
 *   },
 *   bundle_entity_type = "chgk_question_type",
 *   links = {
 *     "canonical" = "chgk.question_view",
 *     "edit-form" = "chgk.question_edit",
 *     "admin-form" = "chgk.question_type_edit"
 *   },
 *   permission_granularity = "entity_type"
 * )
 */
class Question extends ContentEntityBase implements QuestionInterface {
  public $qid;

  /**
   * Implements Drupal\Core\Entity\EntityInterface::id().
   */
  public function id() {
    return $this->get('qid')->value;
  }

  /**
   * {@inheritdoc}
   */
  protected function init() {
    parent::init();
    unset($this->qid);
    unset($this->uuid);
    unset($this->uid);
  }

  /**
   * {@inheritdoc}
   */
  public static function preCreate(EntityStorageControllerInterface $storage_controller, array &$values) {
    parent::preCreate($storage_controller, $values);
    // @todo Handle this through property defaults.
    if (empty($values['created'])) {
      $values['created'] = REQUEST_TIME;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function preSave(EntityStorageControllerInterface $storage_controller) {
    parent::preSave($storage_controller);
    // Before saving the node, set changed and revision times.
    $this->changed->value = REQUEST_TIME;
  }


  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions($entity_type) {
    $fields['qid'] = FieldDefinition::create('integer')
      ->setLabel(t('Question ID'))
      ->setDescription(t('The question ID.'))
      ->setReadOnly(TRUE);

    $fields['uuid'] = FieldDefinition::create('uuid')
      ->setLabel(t('UUID'))
      ->setDescription(t('The pack UUID.'))
      ->setReadOnly(TRUE);
     $fields['uid'] = FieldDefinition::create('entity_reference')
      ->setLabel(t('User ID'))
      ->setDescription(t('The user ID of the question creator.'))
      ->setSettings(array(
        'target_type' => 'user',
        'default_value' => 0,
      ));
    $fields['type'] = FieldDefinition::create('entity_reference')
      ->setLabel(t('Type'))
      ->setDescription(t('The question type.'))
      ->setSetting('target_type', 'chgk_question_type')
      ->setReadOnly(TRUE);

    // @todo Convert to a "created" field in https://drupal.org/node/2145103.
    $fields['created'] = FieldDefinition::create('integer')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the question was created.'));

    // @todo Convert to a "changed" field in https://drupal.org/node/2145103.
    $fields['changed'] = FieldDefinition::create('integer')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the question was last edited.'))
      ->setPropertyConstraints('value', array('EntityChanged' => array()));


    return $fields;
  }
  /**
   * {@inheritdoc}
   */
  public function getChangedTime() {
    return $this->get('changed')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function getUserId() {
    return $this->get('uid')->target_id;
  }

  /**
   * {@inheritdoc}
   */
  public function getUser() {
    return $this->get('uid')->entity;
  }

}