<?php

/**
 * @file
 * Contains \Drupal\node\Form\QestionTypeDeleteConfirmForm
 */

namespace Drupal\chgk;

use Drupal\Core\Entity\EntityConfirmFormBase;
use Drupal\Core\Url;

/**
 * Provides a form for content type deletion.
 */
class QuestionTypeDeleteConfirmForm extends EntityConfirmFormBase {

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return t('Вы уверены, что хотите удалить тип вопросов %type?', array('%type' => $this->entity->label()));
  }


  /**
   * {@inheritdoc}
   */
  public function getConfirmText() {
    return t('Удалить');
  }
  
  /**
   * {@inheritdoc}
   */
  public function getCancelText() {
    return $this->t('Не удалять');
  }
  
  /**
   * {@inheritdoc}
   */
  public function getDescription() {
    return $this->t('Если вы удалите тип вопросов, то восстановить его не удастся. Все вопросы этого типа также будут удалены.');
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelRoute() {
    return new Url('chgk.question_types');
  }


  /**
   * {@inheritdoc}
   */
  public function submit(array $form, array &$form_state) {
    $this->entity->delete();
    $t_args = array('%name' => $this->entity->label());
    drupal_set_message(t('Тип вопросов %name удалён', $t_args));
    watchdog('node', 'Удалён тип вопросов %name.', $t_args, WATCHDOG_NOTICE);

    $form_state['redirect_route'] = $this->getCancelRoute();
  }



}
