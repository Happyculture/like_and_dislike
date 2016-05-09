<?php

/**
 * @file
 * Contains \Drupal\like_and_dislike\LikeDislikePermissions.
 */

namespace Drupal\like_and_dislike;

use Drupal\comment\Entity\CommentType;
use Drupal\Core\Config\Entity\ConfigEntityInterface;
use Drupal\Core\Routing\UrlGeneratorTrait;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\node\Entity\NodeType;
use Drupal\votingapi\Entity\VoteType;

/**
 * Provides dynamic permissions for nodes of different types.
 */
class LikeDislikePermissions {

  use StringTranslationTrait;
  use UrlGeneratorTrait;

  /**
   * Returns an array of vote type permissions.
   *
   * @return array
   *   The vote type permissions.
   * @see \Drupal\user\PermissionHandlerInterface::getPermissions()
   */
  public function voteTypePermissions() {
    $perms = array();

    // Generate vote permissions for all node types.
    foreach (NodeType::loadMultiple() as $type) {
      $perms = array_merge($perms, $this->buildPermissions($type));
    }
    // Generate vote permissions for all comment types.
    if (\Drupal::moduleHandler()->moduleExists('comment')) {
      foreach (CommentType::loadMultiple() as $type) {
        $perms = array_merge($perms, $this->buildPermissions($type));
      }
    }

    return $perms;
  }

  /**
   * Returns a list of permissions for a given vote type.
   *
   * @param \Drupal\votingapi\Entity\VoteType $type
   *   The vote type.
   *
   * @return array
   *   An associative array of permission names and descriptions.
   */
  protected function buildPermissions(ConfigEntityInterface $type) {
    $type_id = $type->id();
    $type_params = array('%type_name' => $type->label());

    $perms = [];
    foreach (VoteType::loadMultiple() as $vote_type) {
      $type_params['%vote_type_name'] = $vote_type->label();
      $vote_type_id = $vote_type->id();
      $perms["create $vote_type_id vote on $type_id"] = [
        'title' => $this->t(
          '%type_name: add %vote_type_name',
          $type_params
        ),
      ];
    }
    $perms["delete own $type_id vote"] = [
      'title' => $this->t('%type_name: delete own vote', $type_params),
    ];
    $perms["delete any $type_id vote"] = [
      'title' => $this->t('%type_name: delete any vote', $type_params),
    ];
    return $perms;
  }

}
