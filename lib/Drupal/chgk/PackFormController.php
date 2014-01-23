<?php

/**
 * @file
 * Definition of Drupal\chgk\PackFormController.
 */

namespace Drupal\chgk;

use Drupal\Core\Entity\ContentEntityFormController;

/**
 * Form controller for the question edit forms.
 */
class PackFormController extends ContentEntityFormController {

  public $entity;

  /**
   * Overrides Drupal\Core\Entity\EntityFormController::form().
   */
  public function form(array $form, array &$form_state) {
    $request = $this->getRequest();
    $pack = $this->entity;
    // Basic question information.
    // These elements are just values so they are not even sent to the client.
//    print_r($pack->parent->entity);
    foreach (array('pid', 'type', 'uid', 'parent') as $key) {
      $form[$key] = array(
        '#type' => 'value',
        '#value' => isset($pack->$key) ? $pack->$key : NULL,
      );
    }
    $form['title'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Title'),
      '#default_value' => $pack->title->value,
      '#description' => $this->t('Полное название пакета. Например &laquo;Международный синхронный турнир "15-й Открытый Кубок России". 1 тур&raquo;'),
      '#maxlength' => 255,
      '#required' => TRUE,
      '#weight' => -5,
    );

    
   $form['machine_name'] = array(
      '#title'       => 'Текстовый идентификатор',
      '#type'        => 'textfield',
      '#required'    => TRUE,
      '#maxlength' => 20,
      '#weight'      => -3,
      '#default_value'=>$pack->machine_name->value,
      '#description' => $this->t('Состоит из латинских букв, цифр, дефиса, точки. Точкой отделяется номер тура. Рекомендуемый формат: имя, две цифры на год, символ подчёркивания, номер тура. Например "province08","ovsch08-3.1"'),
  );

/*  $form['tours'] = array(
      '#type' => 'table',
      '#header' => array(t('Тур')),
      '#empty' => t('В этом пакете нет туров'),
      '#tableselect' => FALSE,
      '#tabledrag' => TRUE,
      '#tree' => TRUE,
 );
 foreach (array(1,2,3,4)) {
 }
*/


//  

    // This form uses a button-level #submit handler for the form's main submit
    // action. node_form_submit() manually invokes all form-level #submit
    // handlers of the form. Without explicitly setting #submit, Form API would
    // auto-detect node_form_submit() as submit handler, but that is the
    // button-level #submit handler for the 'Save' action.
    $form += array('#submit' => array());

    $form = parent::form($form, $form_state, $pack);
    return $form;
  }

  /**
   * Overrides Drupal\Core\Entity\EntityFormController::save().
   */
  public function save(array $form, array &$form_state) {
    $pack = $this->entity;
    $insert = $pack ->isNew();
    $pack->save();
    if ($insert) {
      drupal_set_message(t('New question pack has been created.'));
    }
    else {
      drupal_set_message(t('Question pack has been updated.'));
    }

    if ($pack->id()) {
      $form_state['values']['pid'] = $pack->id();
      $form_state['pid'] = $pack->id();
      $form_state['redirect_route'] = array(
        'route_name' => 'chgk.pack_view',
        'route_parameters' => array(
          'chgk_pack' => $pack->id(),
        ),
      );
    }
    else {
      // In the unlikely case something went wrong on save, the node will be
      // rebuilt and question form redisplayed the same way as in preview.
      drupal_set_message(t('The question pack could not be saved.'), 'error');
      $form_state['rebuild'] = TRUE;
    }

    // Clear the page and block caches.
    cache_invalidate_tags(array('content' => TRUE));
  }

  protected function prepareEntity() {
/*    $pack = $this->entity;
    if ( $pack->getUserId() == 0 ) {
      if ($this->parent->value) {
        $pack->setUserId($this->parent->value);
      } else {
        $pack->setUserId(\Drupal::currentUser()->id());
      }
    }*/
  }
}
