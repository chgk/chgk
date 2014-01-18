<?php

/**
 * @file
 * Contains \Drupal\node\Controller\NodeController.
 */

namespace Drupal\chgk\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\chgk\QuestionTypeInterface;
use Drupal\chgk\PackInterface;


/**
 * Returns responses for Node routes.
 */
class PackController extends ControllerBase {


  /**
   * Displays a pack page.
   *
   * @param \Drupal\node\PackInterface $chgk_pack
   *   The pack we are displaying.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function page(PackInterface $chgk_pack) {
    $build = $this->buildPage($chgk_pack);
    foreach ($chgk_pack->uriRelationships() as $rel) {      
      $uri = $chgk_pack->uri($rel);
      // Set the node path as the canonical URL to prevent duplicate content.
      $build['#attached']['drupal_add_html_head_link'][] = array(
        array(
        'rel' => $rel,
        'href' => $this->urlGenerator()->generateFromPath($uri['path'], $uri['options']),
        )
        , TRUE);
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
  public function pageTitle(PackInterface $chgk_pack) {
    return $chgk_pack->label();
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
