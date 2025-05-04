<?php

namespace Drupal\aff_trends\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Returns a simple page with affiliate products.
 */
class AffProductController extends ControllerBase {
  public function overview() {
    return [
      '#markup' => $this->t('Affiliate Product list goes here (View or Table).'),
    ];
  }
}
