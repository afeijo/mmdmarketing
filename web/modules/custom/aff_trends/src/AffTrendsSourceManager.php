<?php

namespace Drupal\aff_trends;

use Drupal\Core\Plugin\DefaultPluginManager;

class AffTrendsSourceManager extends DefaultPluginManager {
  public function __construct(\Traversable $namespaces, \Drupal\Core\Cache\CacheBackendInterface $cache_backend, \Drupal\Core\Extension\ModuleHandlerInterface $module_handler) {
    parent::__construct('Plugin/AffTrendsSource', $namespaces, $module_handler, NULL, 'Drupal\Component\Annotation\Plugin', ['Drupal\Component\Annotation\Plugin']);
    $this->alterInfo('aff_trends_source_info');
    $this->setCacheBackend($cache_backend, 'aff_trends_source_plugins');
  }
}
