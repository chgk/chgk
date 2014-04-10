<?php

/**
 * @file
 * Definition of Drupal\chgk\Entity\Question.
 */

namespace Drupal\chgk\Entity;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\chgk\PackInterface;
use Drupal\Core\Field\FieldDefinition;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\EntityStorageInterface;
/**
 * Defines the question pack entity.
 *
 * @ContentEntityType(
 *   id = "chgk_pack",
 *   label = @Translation("Chgk Question Pack"),
 *   controllers = {
 *     "storage" = "Drupal\chgk\PackStorage",
 *     "view_builder" = "Drupal\chgk\PackViewBuilder",
 *     "access" = "Drupal\chgk\PackAccessController",
 *     "form" = {
 *       "add" = "Drupal\chgk\PackFormController",
 *       "default" = "Drupal\chgk\PackFormController",
 *       "delete" = "Drupal\chgk\Form\PackDeleteForm",
 *       "edit" = "Drupal\chgk\PackFormController"
 *     },
 *   },
 *   base_table = "chgk_pack",
 *   uri_callback = "chgk_pack_uri",
 *   fieldable = TRUE,
 *   translatable = TRUE,
 *   entity_keys = {
 *     "id" = "pid",
 *     "uuid" = "uuid",
 *     "label" = "title",
 *   },
 *   links = {
 *     "canonical" = "chgk.pack_view",
 *     "edit-form" = "chgk.pack_edit",
 *     "admin-form" = "chgk.pack_settings"
 *   },
 *   permission_granularity = "entity_type"
 * )
 */
class Pack extends ContentEntityBase implements PackInterface {
  public $pid;

  /**
   * Implements Drupal\Core\Entity\EntityInterface::id().
   */
  public function id() {
    return $this->get('pid')->value;
  }

  /**
   * {@inheritdoc}
   */
  protected function init() {
    parent::init();
    unset($this->pid);
  }

  /**
   * {@inheritdoc}
   */
  public static function preCreate(EntityStorageInterface $storage, array &$values) {
    parent::preCreate($storage, $values);
    // @todo Handle this through property defaults.
    if (empty($values['created'])) {
      $values['created'] = REQUEST_TIME;
    }
    if ( empty($values['uid']) ) {
      $values['uid'] = \Drupal::currentUser()->id();
    }
  }

  /**
   * {@inheritdoc}
   */
  public function preSave(EntityStorageInterface $storage) {
    parent::preSave($storage);
    // Before saving the pack, set changed and revision times.
    $this->changed->value = REQUEST_TIME;
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields['pid'] = FieldDefinition::create('integer')
      ->setLabel(t('Pack ID'))
      ->setDescription(t('The pack ID.'))
      ->setReadOnly(TRUE);

    $fields['uuid'] = FieldDefinition::create('uuid')
      ->setLabel(t('UUID'))
      ->setDescription(t('The pack UUID.'))
      ->setReadOnly(TRUE);

    $fields['uid'] = FieldDefinition::create('entity_reference')
      ->setLabel(t('User ID'))
      ->setDescription(t('The user ID of the set creator.'))
      ->setSettings(array(
        'target_type' => 'user',
        'default_value' => 0,
      ));

    $fields['parent'] = FieldDefinition::create('entity_reference')
      ->setLabel(t('Пакет-родитель'))
      ->setDescription(t('Идентификатор родительского пакета'))
      ->setSettings(array(
        'target_type' => 'chgk_pack',
        'default_value' => 0,
      ));

    $fields['tours'] = FieldDefinition::create('entity_reference')
      ->setLabel(t('Туры1'))
      ->setDescription(t('Туры1'))
      ->setSettings(array(
        'target_type' => 'chgk_pack',
        'default_value' => NULL,
      ));

    $fields['title'] = FieldDefinition::create('text')
      ->setLabel(t('Title'))
      ->setDescription(t('The title of this node, always treated as non-markup plain text.'))
      ->setClass('\Drupal\Core\Field\FieldItemList')
      ->setRequired(TRUE)
      ->setTranslatable(TRUE)
      ->setSettings(array(
        'default_value' => '',
        'max_length' => 255,
        'text_processing' => 0,
      ));

    $fields['type'] = FieldDefinition::create('integer')
      ->setLabel(t('Pack type'))
      ->setDescription(t('The pack type.'));

    $fields['machine_name'] = FieldDefinition::create('string')
      ->setLabel(t('Machine name'))
      ->setDescription(t('The machine name of the Pack'))
      ->setSettings(array(
        'default_value' => '',
        'max_length' => 32,
        'text_processing' => 0,
      ));
    
    // @todo Convert to a "created" field in https://drupal.org/node/2145103.
    $fields['created'] = FieldDefinition::create('integer')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the question was created.'));

    // @todo Convert to a "changed" field in https://drupal.org/node/2145103.
    $fields['changed'] = FieldDefinition::create('integer')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the question was last edited.'))
      ->setPropertyConstraints('value', array('EntityChanged' => array()));

    $fields['weight'] = FieldDefinition::create('integer')
      ->setLabel(t('Weight'))
      ->setDescription(t('The weight.'));


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

  public function setUserId( $uid ) {
    $this->set('uid', $uid);
    return $this;
  }
  
  public function getTours() {
    
  }

}