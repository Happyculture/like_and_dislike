<?php

namespace Drupal\like_and_dislike;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Session\AccountInterface;

class LikeDislikeAccessController extends ControllerBase {

  public function vote($entity_type_id, AccountInterface $account) {
    return AccessResult::allowedIfHasPermission($account, "add or remove like votes on $entity_type_id");
  }

}
