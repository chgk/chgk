<?php

/**
 * @file
 * Definition of Drupal\chgk\PackFormController.
 */

namespace Drupal\chgk;

use Drupal\Core\Entity\ContentEntityFormController;
use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Cache\Cache;


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

    $form['#attached']['css'] = array(drupal_get_path('module', 'chgk') . '/css/chgk.admin.css');
/*    foreach (array('pid', 'type', 'uid', 'parent') as $key) {
      $form[$key] = array(
        '#type' => 'value',
        '#value' => isset($pack->$key) ? $pack->$key : NULL,
      );
    }*/
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
        '#title'       => t('Текстовый идентификатор'),
        '#type'        => 'textfield',
        '#required'    => TRUE,
        '#maxlength' => 20,
        '#weight'      => -3,
        '#default_value'=>$pack->machine_name->value,
        '#description' => $this->t('Состоит из латинских букв, цифр, дефиса, точки. Точкой отделяется номер тура. Рекомендуемый формат: имя, две цифры на год, символ подчёркивания, номер тура. Например "province08","ovsch08-3.1"'),
    );

    $this->addToursField( $form, $form_state );

    $form += array('#submit' => array());

    $form = parent::form($form, $form_state, $pack);

    return $form;
  }

  private function addToursField( &$form, &$form_state ) {
    $wrapper_id = 'tours-add-more-wrapper';
    $header = array(('Туры'), t('Вес'), t('Действия'));
    $table = array(
      '#type' => 'table',
      '#header' => $header,
      '#prefix' => '<div id="' . $wrapper_id . '">',
      '#suffix' => '</div>',
      '#empty' => t('У этого пакета нет туров'),
      '#tabledrag' => array(
        array(
          'action' => 'order',
          'relationship' => 'sibling',
          'group' => 'tours-weight',
        ),
      )
    );

    $tours_count = $this->getToursCount( $form_state );
    if ($tours_count === FALSE) {
      $tours_count = $this->entity->tours->isEmpty()?0:$this->entity->tours->count();
      $this->setToursCount($form_state, $tours_count);
    }
    for ( $delta = 0; $delta<$tours_count; $delta++) {
      if (!$this->entity->tours[$delta]->isEmpty()) {
        $tour = $this->entity->tours[$delta]->entity;
      } else {
        $tour = $this->entityManager->getStorage('chgk_pack')->create(array(
          'parent' => $this->entity->id(),
          'uid' => $this->entity->uid->value,
          'short_title'=>t('!number тур', array('!number'=>$delta+1))
        ));
      }
      $item = array(
        '#attributes' => array('class'=>array('draggable')),
        '#pack' => $tour,
        '#delta' => $delta,
        '#weight' => $delta,
      );
      $item['name'] = 
        array(
            '#type' => 'textfield',
            '#default_value' => $tour->short_title->value,
      );

      $item['weight'] =  array(
          '#type' => 'weight',
          '#delta' => 50,
          '#default_value' =>  $delta,
          '#weight' => 100,
          '#parents' => array('tour_list', $delta, 'weight'),
          '#attributes' => array('class' => array('tours-weight')),
      );

      $item['actions'] = array(
      );
      $item['actions']['links'] = array(
          '#attributes' => array(
            'class' => array('operations'),
          ),
          '#type' => 'links',
          '#theme' => 'links',

      );
      if ($tour->id()) {
        $item['actions']['links']['#links'][] = array(
          'title' => t('Просмотр'),
        )+$tour->urlInfo();

        $item['actions']['links']['#links'][] = array(
          'title' => t('Редактирование'),
        )+$tour->urlInfo('edit-form');
      }

      $item['actions']['delete'] = array(
        '#type' => 'submit',
        '#name' => 'remove_tour_'.$delta,
        '#value' => t('Удалить'),
        '#submit' => array(array( $this, 'deleteTourSubmit' ) ),
        '#delta' => $delta,
        '#attributes' => array(
           'class' => array('tour-remove-button'),
         ),
        '#ajax' => array(
          'callback' => array( $this, 'removeTourAjax' ),
          'wrapper' => $wrapper_id,
          'effect' => 'none',
        ),
      );

      $table[$delta] = $item;
    }

    $add_more = array(
      '#type' => 'submit',
      '#value' => t('Добавить тур'),
      '#attributes' => array('class' => array('chgk-pack-add-more-submit')),
      '#limit_validation_errors' => array(array('tour_list')),
      '#submit' => array(array( $this, 'addTourSubmit' )),
      '#ajax' => array(
        'callback' => array( $this, 'addTourAjax' ),
        'wrapper' => $wrapper_id,
        'effect' => 'slide',
      ),
    );

    $form['tour_list'] = $table;
    $form['add_more'] = $add_more;
  }



  private function getToursCount( $form_state ) {
    return isset($form_state['chgk_tours_count'])?$form_state['chgk_tours_count']:FALSE;
  }

  private function setToursCount( &$form_state, $count ) {
    return $form_state['chgk_tours_count'] = $count;
  }
  
  private function sortTours( &$form_state ) {
    $values = $form_state['values']['tour_list'];
    foreach ( $values as $key => $value ){
      $values[$key]['old_key'] = $key;
      $packs[$key] = $this->entity->tours[$key]->getValue();
      $sort_array[$key] = $value['weight'];
    }
    $keys = array_keys($sort_array);
    array_multisort($sort_array, $packs, $form_state['input']['tour_list'],$keys);
    $this->entity->tours->setValue($packs);
    return array_flip($keys);
  }

  public function addTourSubmit( $form, &$form_state) {
    $this->sortTours( $form_state );
    $tours_count = $this->getToursCount( $form_state );
    $tours_count++;
    $this->setToursCount( $form_state, $tours_count );
    $form_state['rebuild'] = TRUE;
  }


  public function deleteTourSubmit( $form, &$form_state) {
    $map = $this->sortTours( $form_state );
    $parents = $form_state['triggering_element']['#array_parents'];
    
    //array parent -- 'tour_list', $delta, 'actions', 'delete'
    $delta = $map[$parents[count($parents)-3]];
    $form_state['deleted_tours'][] = $this->entity->tours[$delta]->target_id;
    foreach ($this->entity->tours as $i=>$tour) {
      if ($i>$delta) {
        $this->entity->tours[$i-1] = $this->entity->tours[$i];
      }
    }
    
    $this->entity->tours->offsetUnset($this->entity->tours->count()-1);
    unset($form_state['input']['tour_list'][$delta]);
    $form_state['input']['tour_list'] = array_values($form_state['input']['tour_list']);
    $tours_count = $this->getToursCount( $form_state );
    $tours_count--;
    $this->setToursCount( $form_state, $tours_count );

    $form_state['rebuild'] = TRUE;
  }
  
  public function addTourAjax($form, $form_state) {
    $element = $form['tour_list'];
    $tours_count = $this->getToursCount( $form_state );
    $element[$tours_count-1]['name']['#prefix'] = '<div class="ajax-new-content">';
    $element[$tours_count-1]['name']['#suffix'] = '</div>';
    return $element;
  }

  public function removeTourAjax($form, $form_state) {
    $element = $form['tour_list'];
    return $element;
  }

  /**
   * Overrides Drupal\Core\Entity\EntityFormController::save().
   */
  public function save(array $form, array &$form_state) {
    if (isset($form_state['deleted_tours'])){
      $storage_controller = $this->entityManager->getStorage('chgk_pack');
      $deleted = $storage_controller->loadMultiple($form_state['deleted_tours']);
      $storage_controller->delete($deleted);
    }
    
    $pack = $this->entity;
    if (!$pack->tours->isEmpty()) {
      foreach ($pack->tours as $t) {
        $pid = $t->entity->save();
      }
    }
    
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

    Cache::invalidateTags(array('content' => TRUE));
  }
  
  public function buildEntity(array $form, array &$form_state) {
    $entity = parent::buildEntity($form, $form_state);
    $values = NestedArray::getValue($form_state['values'], array('tour_list'), $key_exists);
    if ($key_exists) {
      $packs = array();
      foreach ($form_state['values']['tour_list'] as $delta => $tour) {
        $pack = $form['tour_list'][$delta]['#pack'];
        $pack->short_title->setValue($tour['name']);
        $pack->weight->setValue($tour['weight']);
        $packs[$delta] = $pack;
      }
      $entity->tours->setValue($packs);
    }

    return $entity;
  }
  
  /**
   * Overrides Drupal\Core\Entity\EntityFormController::delete().
   */
  public function delete(array $form, array &$form_state) {
    $destination = array();
    $query = \Drupal::request()->query;
    if ($query->has('destination')) {
      $destination = drupal_get_destination();
      $query->remove('destination');
    }
    $form_state['redirect_route'] = array(
      'route_name' => 'chgk.pack_delete_confirm',
      'route_parameters' => array(
        'chgk_pack' => $this->entity->id(),
      ),
      'options' => array(
        'query' => $destination,
      ),
    );
  }

}
