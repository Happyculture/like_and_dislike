<?php

/**
 * @file
 * Contains \Drupal\like_and_dislike\Controller\VoteController.
 */

namespace Drupal\like_and_dislike\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\like_and_dislike\AlreadyVotedException;
use Drupal\like_and_dislike\LikeDislikeHelperInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Returns responses for Like & Dislikes routes.
 */
class VoteController extends ControllerBase implements ContainerInjectionInterface {

  /**
   * @var \Drupal\like_and_dislike\LikeDislikeHelperInterface
   */
  protected $likeDislikeHelper;

  /**
   * VoteController constructor.
   *
   * @param \Drupal\like_and_dislike\LikeDislikeHelperInterface $likeDislikeHelper
   */
  public function __construct(LikeDislikeHelperInterface $likeDislikeHelper) {
    $this->likeDislikeHelper = $likeDislikeHelper;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('like_dislike.helper')
    );
  }

  /**
   * Callback for like_and_dislike.vote route.
   */
  function vote($entity_type_id, $vote_type_id, $entity_id, Request $request) {

    try {
      $this->likeDislikeHelper->vote($entity_type_id, $entity_id, $vote_type_id);
      drupal_set_message(t('Your :type vote was added.', [
        ':type' => $vote_type_id
      ]));
    }
    catch (AlreadyVotedException $e) {
      drupal_set_message(
        t('You are not allowed to vote the same way multiple times.'),
        'warning'
      );
    }

    $url = $request->getUriForPath($request->getPathInfo());
    return new RedirectResponse($url);
  }
}
