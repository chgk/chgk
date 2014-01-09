<?php

/**
 * @file
 * Contains \Drupal\chgk\QuestionTypeFormController.
 */

namespace Drupal\chgk;

use Drupal\Core\Entity\EntityFormController;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Component\Utility\String;

/**
 * Form controller for node type forms.
 */
class QuestionTypeFormController extends EntityFormController {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, array &$form_state) {
    $form = parent::form($form, $form_state);

    $type = $this->entity;
    if ($this->operation == 'add') {
      $form['#title'] = String::checkPlain($this->t('Add question type'));
    }
    elseif ($this->operation == 'edit') {
      $form['#title'] = $this->t('Edit %label question type', array('%label' => $type->label()));
    }


    $form['label'] = array(
      '#title' => t('Name'),
      '#type' => 'textfield',
      '#default_value' => $type->label,
      '#description' => t('The human-readable name of this content type. This text will be displayed as part of the list on the <em>Add new content</em> page. It is recommended that this name begin with a capital letter and contain only letters, numbers, and spaces. This name must be unique.'),
      '#required' => TRUE,
      '#size' => 30,
    );

    $form['id'] = array(
      '#type' => 'machine_name',
      '#default_value' => $type->id(),
      '#maxlength' => 32,
      '#machine_name' => array(
        'exists' => 'chgk_question_type_load',
        'source' => array('label'),
      ),
      '#description' => t('A unique machine-readable name for this content type. It must only contain lowercase letters, numbers, and underscores. This name will be used for constructing the URL of the %node-add page, in which underscores will be converted into hyphens.', array(
        '%node-add' => t('Add new content'),
      )),
    );

    $form['description'] = array(
      '#title' => t('Description'),
      '#type' => 'textarea',
      '#default_value' => $type->description,
      '#description' => t('Describe this content type. The text will be displayed on the <em>Add new content</em> page.'),
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  protected function actions(array $form, array &$form_state) {
    $actions = parent::actions($form, $form_state);
    $actions['submit']['#value'] = t('Save');
    $actions['delete']['#value'] = t('Delete');
    $actions['delete']['#access'] = $this->entity->access('delete');
    return $actions;
  }

  /**
   * {@inheritdoc}
   */
  public function validate(array $form, array &$form_state) {
    parent::validate($form, $form_state);

    $id = trim($form_state['values']['id']);
    // '0' is invalid, since elsewhere we check it using empty().
    if ($id == '0') {
      $this->setFormError('type', $form_state, $this->t("Invalid machine-readable name. Enter a name other than %invalid.", array('%invalid' => $id)));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, array &$form_state) {
    $type = $this->entity;
    $type->id = trim($type->id());
    $type->label = trim($type->label);
    $status = $type->save();

    $t_args = array('%name' => $type->label());

    if ($status == SAVED_UPDATED) {
      drupal_set_message(t('The question type %name has been updated.', $t_args));
    }
    elseif ($status == SAVED_NEW) {
      drupal_set_message(t('The question type %name has been added.', $t_args));
      watchdog('node', 'Added question type %name.', $t_args, WATCHDOG_NOTICE, l(t('view'), 'admin/structure/question_types'));
    }

    $form_state['redirect_route']['route_name'] = 'chgk.overview_question_types';
  }

  /**
   * {@inheritdoc}
   */
  public function delete(array $form, array &$form_state) {
    $form_state['redirect_route'] = array(
      'route_name' => 'node.type_delete_confirm',
      'route_parameters' => array(
        'node_type' => $this->entity->id(),
      ),
    );
  }

}
