<?php

/**
 * @file
 * Contains \Drupal\chgk\PathProcessor
 */

namespace Drupal\chgk;

use Drupal\Core\Path\AliasManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Core\PathProcessor\InboundPathProcessorInterface;
use Drupal\Core\PathProcessor\OutboundPathProcessorInterface;
use Drupal\Core\Entity\EntityManagerInterface;



/**
 * Processes the inbound path using path alias lookups.
 */
class PathProcessor implements InboundPathProcessorInterface, OutboundPathProcessorInterface {


  protected $entityManager;

  /**
   * Constructs a PathProcessor object.
   *
   * @param \Drupal\Core\Entity\EntityManagerInterface $entity_manager
   *   The entity manager.
   */
  public function __construct(EntityManagerInterface $entity_manager) {
    $this->entityManager = $entity_manager;
  }



  /**
   * Implements Drupal\Core\PathProcessor\InboundPathProcessorInterface::processInbound().
   */
  public function processInbound($path, Request $request) {
    if (preg_match('~^tour/([^/]+)$~' , $path, $matches )) {
      $pid = $this->entityManager->getStorage('chgk_pack')->machineNameToId($matches[1]);
      if ($pid) $path = 'pack/'.$pid;
    }
    return $path;
  }

  /**
   * Implements Drupal\Core\PathProcessor\OutboundPathProcessorInterface::processOutbound().
   */
  public function processOutbound($path, &$options = array(), Request $request = NULL) {
    if (empty($options['alias'])) {
      if (preg_match('~^pack/([^/]+)$~' , $path, $matches )) {
        $machine_name = $this->entityManager->getController('chgk_pack', 'storage')->idToMachineName($matches[1]);
        if ($machine_name) $path = 'tour/'.$machine_name;
      }
    }
    return $path;
  }

}
