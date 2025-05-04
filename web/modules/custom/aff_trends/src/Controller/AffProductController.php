<?php

namespace Drupal\aff_trends\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class AffProductController extends ControllerBase {

  protected $entityTypeManager;

  public function __construct(EntityTypeManagerInterface $entityTypeManager) {
    $this->entityTypeManager = $entityTypeManager;
  }

  public static function create(ContainerInterface $container) {
    return new static($container->get('entity_type.manager'));
  }

  public function overview() {
    $storage = $this->entityTypeManager->getStorage('aff_product');
    $products = $storage->loadMultiple();

    $rows = [];
    foreach ($products as $product) {
      $rows[] = [
        $product->label(),
        $product->get('field_aff_source')->value,
        $product->get('field_price')->value,
        $product->get('field_aff_url')->uri,
      ];
    }

    return [
      '#type' => 'table',
      '#header' => ['Title', 'Source', 'Price', 'Affiliate URL'],
      '#rows' => $rows,
      '#empty' => $this->t('No products found.'),
    ];
  }
}
