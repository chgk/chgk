<?php

/**
 * @file
 * Definition of Drupal\chgk\Entity\Question.
 */

namespace Drupal\chgk\Entity;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Field\FieldDefinition;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\ContentEntityInterface;

/**
 * Defines the chgk question entity.
 *
 * @ContentEntityType(
 *   id = "chgk_question",
 *   label = @Translation("Вопрос"),
 *   bundle_label = @Translation("Тип вопроса"),
 *   controllers = {
 *     "view_builder" = "Drupal\chgk\QuestionViewBuilder",
 *     "access" = "Drupal\chgk\QuestionAccessController",
 *     "form" = {
 *       "default" = "Drupal\chgk\QuestionForm",
 *       "delete" = "Drupal\chgk\Form\QuestionDeleteForm",
 *       "edit" = "Drupal\chgk\QuestionForm",
 *       "add" = "Drupal\chgk\QuestionForm"
 *     },
 *   },
 *   base_table = "chgk_question",
 *   fieldable = TRUE,
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
 * )
 */
class Question extends ContentEntityBase implements ContentEntityInterface {

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields['qid'] = FieldDefinition::create('integer')
      ->setLabel(t('Внутренний идентификатор вопроса'))
      ->setDescription(t('The question ID.'))
      ->setReadOnly(TRUE);

    $fields['uuid'] = FieldDefinition::create('uuid')
      ->setLabel(t('UUID'))
      ->setDescription(t('UUID.'))
      ->setReadOnly(TRUE);
     $fields['uid'] = FieldDefinition::create('entity_reference')
      ->setLabel(t('User ID'))
      ->setDescription(t('Идентификатор пользователя, который добавил вопрос'))
      ->setSettings(array(
        'target_type' => 'user',
        'default_value' => 0,
      ));
    $fields['type'] = FieldDefinition::create('entity_reference')
      ->setLabel(t('Тип'))
      ->setDescription(t('Тип вопроса'))
      ->setSetting('target_type', 'chgk_question_type')
      ->setReadOnly(TRUE);

    // @todo Convert to a "created" field in https://drupal.org/node/2145103.
    $fields['created'] = FieldDefinition::create('integer')
      ->setLabel(t('Время создания'))
      ->setDescription(t('Время, когда вопрос был добавлен в базу'));

    // @todo Convert to a "changed" field in https://drupal.org/node/2145103.
    $fields['changed'] = FieldDefinition::create('integer')
      ->setLabel(t('Время изменения'))
      ->setDescription(t('Время, когда вопрос последний раз редактировался'))
      ->setPropertyConstraints('value', array('EntityChanged' => array()));


    return $fields;
  }

}
