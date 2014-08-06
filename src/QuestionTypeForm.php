<?php

/**
 * @file
 * Contains \Drupal\chgk\QuestionTypeForm.
 */

namespace Drupal\chgk;

use Drupal\Core\Entity\EntityForm;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Component\Utility\String;

/**
 * Form controller for node type forms.
 */
class QuestionTypeForm extends EntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, array &$form_state) {
    $form = parent::form($form, $form_state);
    $type = $this->entity;
    if ($this->operation == 'add') {
      $form['#title'] = String::checkPlain($this->t('Добавление типа вопроса'));
    }
    elseif ($this->operation == 'edit') {
      $form['#title'] = $this->t('Редактирование типа вопросов "%label"', array('%label' => $type->label()));
    }


    $form['label'] = array(
      '#title' => t('Название типа'),
      '#type' => 'textfield',
      '#default_value' => $type->label,
      '#description' => t('Название типа вопросов.'),
      '#required' => TRUE,
      '#size' => 30,
    );

    $form['id'] = array(
      '#type' => 'machine_name',
      '#default_value' => $type->id(),
      '#maxlength' => 32,
      '#machine_name' => array(
        'exists' => '\Drupal\chgk\Entity\QuestionType::load',
        'source' => array('label'),
      ),
      '#description' => t('Машинное имя типа. Должно состоять из строчных латинских букв, цифр и знаков подчёркивания'),
    );

    $form['description'] = array(
      '#title' => t('Описание'),
      '#type' => 'textarea',
      '#default_value' => $type->description,
      '#description' => t('Описание типа вопросов'),
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  protected function actions(array $form, array &$form_state) {
    $actions = parent::actions($form, $form_state);
    $actions['submit']['#value'] = t('Сохранить');
    $actions['delete']['#value'] = t('Удалить');
    $actions['delete']['#access'] = $this->entity->access('delete');
    return $actions;
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
      drupal_set_message(t('Тип вопроса %name обновлён.', $t_args));
    }
    elseif ($status == SAVED_NEW) {
      drupal_set_message(t('Добавлен новый тип вопросов "%name".', $t_args));
    }
    $form_state['redirect_route']['route_name'] = 'chgk.question_types';
  }

  /**
   * {@inheritdoc}
   */
  public function delete(array $form, array &$form_state) {
    $form_state['redirect_route'] = array(
      'route_name' => 'chgk.question_type_delete_confirm',
      'route_parameters' => array(
        'chgk_question_type' => $this->entity->id(),
      ),
    );
  }
}
