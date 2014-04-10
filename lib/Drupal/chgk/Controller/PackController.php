<?php

/**
 * @file
 * Contains \Drupal\node\Controller\NodeController.
 */

namespace Drupal\chgk\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\chgk\QuestionTypeInterface;
use Drupal\chgk\PackInterface;
use Drupal\chgk\PackManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

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
    unset($build['packs']['#cache']);
    foreach ($chgk_pack->uriRelationships() as $rel) {      
      // Set the node path as the canonical URL to prevent duplicate content.
      $build['#attached']['drupal_add_html_head_link'][] = array(
        array(
        'rel' => $rel,
        'href' => $chgk_pack->url($rel),
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
  protected function buildPage(PackInterface $chgk_pack) {
    $viewBuilder = $this->entityManager()->getViewBuilder('chgk_pack');
    return array(
      'packs' => $this->entityManager()->getViewBuilder('chgk_pack')->view($chgk_pack)
    );
  }

  /**
   * {inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static($container->get('pack.manager'));
  }

  public function addChild( PackInterface $chgk_pack ) {
    $new_pack = $this->entityManager()->getStorage('chgk_pack')->create(array(
      'parent' => $chgk_pack->id(),
      'uid' => $chgk_pack->uid->value
    ));
    return $this->entityManager()->getForm($new_pack, 'add');
  }
}
