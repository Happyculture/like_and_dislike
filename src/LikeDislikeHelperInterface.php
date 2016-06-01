<?php

namespace Drupal\like_and_dislike;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;

interface LikeDislikeHelperInterface {

  /**
   * Checks if an user already has a vote of the given type.
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
   * @return bool
   *   TRUE if the user has a vote of the given type, FALSE otherwise.
   */
  public function hasVote($entity_type_id, $entity_id, $vote_type_id, AccountInterface $account = NULL);

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

  /**
   * Checks if the votes are enabled for this entity.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity to check.
   *
   * @return bool
   *   TRUE if the votes are allowed on the entity, FALSE otherwise.
   */
  public function isAvailableForEntity(EntityInterface $entity);

  /**
   * Gets the like URL.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity to like.
   *
   * @return \Drupal\Core\Url
   *   The URL where to go to add a like to the entity.
   */
  public function getLikeUrl(EntityInterface $entity);

  /**
   * Gets the dislike URL.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity to dislike.
   *
   * @return \Drupal\Core\Url
   *   The URL where to go to add a dislike to the entity.
   */
  public function getDislikeUrl(EntityInterface $entity);

}
