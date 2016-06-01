<?php

namespace Drupal\like_and_dislike;

use Drupal\Core\Session\AccountInterface;

interface LikeDislikeHelperInterface {

  /**
   * Saves a vote.
   *
   * @param $entity_type_id
   *   The entity type on which the vote is done.
   * @param $entity_id
   *   The entity ID on which the vote is done.
   * @param $vote_type_id
   *   The ID of the Vote Type.
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user account that owns the vote.
   *
   * @return \Drupal\votingapi\Entity\Vote
   *   The Vote object that have been created.
   */
  public function vote($entity_type_id, $entity_id, $vote_type_id, AccountInterface $account = NULL);

  /**
   * Deletes a vote.
   *
   * @param $entity_type_id
   *   The entity type on which the vote is done.
   * @param $entity_id
   *   The entity ID on which the vote is done.
   * @param $vote_type_id
   *   The ID of the Vote Type.
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user account that owns the vote.
   *
   * @return void
   */
  public function unvote($entity_type_id, $entity_id, $vote_type_id, AccountInterface $account = NULL);

}
