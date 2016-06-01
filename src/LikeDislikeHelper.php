<?php

namespace Drupal\like_and_dislike;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeManager;
use Drupal\Core\Routing\RedirectDestinationInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Core\Url;
use Drupal\votingapi\Entity\Vote;
use Drupal\votingapi\Entity\VoteType;

class LikeDislikeHelper implements LikeDislikeHelperInterface {

  /**
   * @var \Drupal\votingapi\VoteStorageInterface
   */
  protected $voteStorage;

  /**
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $voteTypeStorage;

  /**
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;

  /**
   * @var \Drupal\Core\Routing\RedirectDestinationInterface
   */
  protected $redirectDestination;

  /**
   * @var \Drupal\Core\Config\ImmutableConfig
   */
  protected $config;

  /**
   * LikeDislikeHelper constructor.
   *
   * @param EntityTypeManager $entityTypeManager
   * @param AccountProxyInterface $currentUser
   * @param RedirectDestinationInterface $redirectDestination
   * @param \Drupal\Core\Config\ConfigFactoryInterface $configFactory
   */
  public function __construct(EntityTypeManager $entityTypeManager, AccountProxyInterface $currentUser, RedirectDestinationInterface $redirectDestination, ConfigFactoryInterface $configFactory) {
    $this->voteStorage = $entityTypeManager->getStorage('vote');
    $this->voteTypeStorage = $entityTypeManager->getStorage('vote_type');
    $this->currentUser = $currentUser;
    $this->redirectDestination = $redirectDestination;
    $this->config = $configFactory->get('like_and_dislike.settings');
  }

  /**
   * {@inheritdoc}
   */
  public function hasVote($entity_type_id, $entity_id, $vote_type_id, AccountInterface $account = NULL) {
    if (NULL === $account) {
      $account = $this->currentUser;
    }

    $user_votes = $this->voteStorage->getUserVotes(
      $account->id(),
      $vote_type_id,
      $entity_type_id,
      $entity_id
    );
    return !empty($user_votes);
  }

  /**
   * {@inheritdoc}
   */
  public function vote($entity_type_id, $entity_id, $vote_type_id, AccountInterface $account = NULL) {
    if (NULL === $account) {
      $account = $this->currentUser;
    }

    if ($this->hasVote($entity_type_id, $entity_id, $vote_type_id, $account)) {
      throw new AlreadyVotedException();
    }

    /** @var VoteType $vote_type */
    $vote_type = $this->voteTypeStorage->load($vote_type_id);

    /** @var Vote $vote */
    $vote = $this->voteStorage->create(['type' => $vote_type_id]);
    $vote->setVotedEntityId($entity_id);
    $vote->setVotedEntityType($entity_type_id);
    $vote->setValueType($vote_type->getValueType());
    $vote->setValue(1);
    $vote->setOwnerId($account->id());
    $vote->save();

    return $vote;
  }

  /**
   * {@inheritdoc}
   */
  public function unvote($entity_type_id, $entity_id, $vote_type_id, AccountInterface $account = NULL) {
    if (NULL === $account) {
      $account = $this->currentUser;
    }

    $this->voteStorage->deleteUserVotes(
      $account->id(),
      $vote_type_id,
      $entity_type_id,
      $entity_id
    );
  }

  /**
   * {@inheritdoc}
   */
  public function isAvailableForEntity(EntityInterface $entity) {
    return NULL !== $this->config->get($entity->getEntityTypeId() . '_' . $entity->bundle() . '_available');
  }

  /**
   * {@inheritdoc}
   */
  public function getLikeUrl(EntityInterface $entity) {
    return $this->generateVoteUrl($entity, 'like');
  }

  /**
   * {@inheritdoc}
   */
  public function getDislikeUrl(EntityInterface $entity) {
    return $this->generateVoteUrl($entity, 'dislike');
  }

  /**
   * Generates the vote URL.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity on which the vote might be saved.
   * @param string $vote_type_id
   *   The vote type ID of the vote.
   * @return \Drupal\Core\Url
   *   The URL of the vote.
   */
  protected function generateVoteUrl(EntityInterface $entity, $vote_type_id) {
    return Url::fromRoute('like_and_dislike.vote', [
      'entity_type_id' => $entity->getEntityTypeId(),
      'vote_type_id' => $vote_type_id,
      'entity_id' => $entity->id(),
      'destination' => $this->redirectDestination->get(),
    ]);
  }

}
