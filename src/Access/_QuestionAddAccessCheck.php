<?php

/**
 * @file
 * Contains \Drupal\node\Access\QuestionAddAccessCheck.
 */

namespace Drupal\chgk\Access;

use Drupal\Core\Entity\EntityCreateAccessCheck;
use Drupal\Core\Session\AccountInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Determines access to for node add pages.
 */
class QuestionAddAccessCheck extends EntityCreateAccessCheck {

  /**
   * {@inheritdoc}
   */
  protected $requirementsKey = '_question_add_access';

  /**
   * {@inheritdoc}
   */
  public function access(Route $route, Request $request, AccountInterface $account) {
    $access_controller = user_access('bypass node access', $account);
    // If a node type is set on the request, just check that.
    return static::DENY;
  }

}
