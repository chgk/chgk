<?php

/**
 * @file
 * Contains \Drupal\node\Controller\NodeController.
 */

namespace Drupal\chgk\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\chgk\QuestionTypeInterface;
use Drupal\chgk\QuestionInterface;
use Drupal\chgk\PackInterface;


/**
 * Returns responses for Node routes.
 */
class QuestionController extends ControllerBase {

  /**
   * Displays add question links for available question types.
   *
   * Redirects to question/add/[type] if only one question type is available.
   *
   * @return array
   *   A render array for a list of the question types that can be added; however,
   *   if there is only one question type defined for the site, the function
   *   redirects to the question add page for that one question type and does not return
   *   at all.
   *
   * @see node_menu()
   */
  public function addPage() {
    $content = $this->entityManager()->getStorage('chgk_question_type')->loadMultiple();
    // Bypass the node/add listing if only one question type is available.
    if (count($content) == 1) {
      $type = array_shift($content);
      return $this->redirect('chgk_question.add', array('node_type' => $type->type));
    }

    return array(
      '#theme' => 'chgk_question_add_list',
      '#content' => $content,
    );
  }

  /**
   * Provides the question submission form.
   *
   * @param \Drupal\chgk\QuestionTypeInterface $question_type
   *   The question type entity for the node.
   *
   * @return array
   *   A question submission form.
   */
  public function add(QuestionTypeInterface $chgk_question_type) {
    $account = $this->currentUser();
    $question = $this->entityManager()->getStorage('chgk_question')->create(array(
      'type' => $chgk_question_type->id(),
      'uid' => $account->id(),
    ));
    $form = $this->entityManager()->getForm($question);


    return $form;
  }

  /**
   * Provides the question submission form for a pack.
   *
   * @param \Drupal\chgk\QuestionTypeInterface $question_type
   *   The question type entity for the node.
   *
   * @return array
   *   A question submission form.
   */
  public function addChild(QuestionTypeInterface $chgk_question_type, PackInterface $chgk_pack) {
    $account = $this->currentUser();
    $question = $this->entityManager()->getStorageController('chgk_question')->create(array(
      'type' => $chgk_question_type->id(),
      'uid' => $account->id(),
    ));
    $form = $this->entityManager()->getForm($question, 'add', array('parent'=>$chgk_pack));


    return $form;
  }

  /**
   * Displays a node.
   *
   * @param \Drupal\node\NodeInterface $node
   *   The node we are displaying.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function page(QuestionInterface $chgk_question) {

    $build = $this->buildPage($chgk_question);
unset($build['questions']['#cache']);
    foreach ($chgk_question->uriRelationships() as $rel) {
      // Set the node path as the canonical URL to prevent duplicate content.
      $build['#attached']['drupal_add_html_head_link'][] = array(
        array(
        'rel' => $rel,
        'href' => $chgk_question->url($rel),
        )
        , TRUE);

      if ($rel == 'canonical') {
        // Set the non-aliased canonical path as a default shortlink.
        $build['#attached']['drupal_add_html_head_link'][] = array(
          array(
            'rel' => 'shortlink',
            'href' => $chgk_question->url($rel,  array('alias' => TRUE)),
          )
        , TRUE);
      }
    }
    return $build;
  }

  /**
   * The _title_callback for the node.view route.
   *
   * @param NodeInterface $node
   *   The current node.
   *
   * @return string
   *   The page title.
   */
  public function pageTitle(QuestionInterface $chgk_question) {
    return $chgk_question->id();
  }

  /**
   * Builds a question page render array.
   *
   * @param \Drupal\node\QuestionInterface $node
   *   The node we are displaying.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  protected function buildPage(QuestionInterface $chgk_question) {
    return array('questions' => $this->entityManager()->getViewBuilder('chgk_question')->view($chgk_question)
    );
  }


}
