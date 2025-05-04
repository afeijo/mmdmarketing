<?php

namespace Drupal\aff_trends\Plugin\AffTrendsSource;

use Drupal\aff_trends\Entity\AffProduct;
use Drupal\Core\Plugin\PluginBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use GuzzleHttp\ClientInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\File\FileSystemInterface;

/**
 * @Plugin(
 *   id = "shopee_trending",
 *   label = @Translation("Shopee Trending")
 * )
 */
class ShopeeTrendingPlugin extends PluginBase implements ContainerFactoryPluginInterface {

  protected $httpClient;
  protected $logger;
  protected $fileSystem;

  public function __construct(array $configuration, $plugin_id, $plugin_definition, ClientInterface $http_client, LoggerChannelFactoryInterface $logger_factory, FileSystemInterface $file_system) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->httpClient = $http_client;
    $this->logger = $logger_factory->get('aff_trends');
    $this->fileSystem = $file_system;
  }

  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('http_client'),
      $container->get('logger.factory'),
      $container->get('file_system')
    );
  }

  public function collect(): void {
    try {
      $response = $this->httpClient->request('GET', 'https://shopee.com.br/api/v4/recommend/recommend?bundle=top_products', [
        'headers' => [
          'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/90 Safari/537.36',
          'Referer' => 'https://shopee.com.br/',
          'Accept' => 'application/json',
        ],
      ]);
      $json = $response->getBody();
    }
    catch (\Exception $e) {
      $this->logger->warning('Shopee API failed, using fallback. ' . $e->getMessage());
      $path = dirname(__DIR__, 3) . '/data/shopee_sample.json';
      $json = file_get_contents($path);
    }

    $data = json_decode($json, TRUE);

    if (!empty($data['data']['sections'])) {
      foreach ($data['data']['sections'] as $section) {
        foreach ($section['items'] ?? [] as $item) {
          if (!empty($item['item_basic'])) {
            $info = $item['item_basic'];

            $existing = \Drupal::entityTypeManager()
              ->getStorage('aff_product')
              ->loadByProperties(['field_external_id' => $info['itemid']]);

            if ($existing) {
              continue; // já existe
            }

            $affid = \Drupal::config('aff_trends.settings')->get('shopee_affid');

            AffProduct::create([
              'field_external_id' => $info['itemid'],
              'title' => substr($info['name'], 0, 255),
              'field_aff_source' => 'shopee',
              'field_price' => $info['price'] / 100000,
              'field_aff_url' => ['uri' => 'https://shopee.com.br/product/' . $info['shopid'] . '/' . $info['itemid'] . '?affid=' . $affid],
              'field_original_url' => ['uri' => 'https://shopee.com.br/product/' . $info['shopid'] . '/' . $info['itemid']],
              'field_image_url' => 'https://cf.shopee.com.br/file/' . $info['image'],
              'field_score' => $info['historical_sold'] ?? 0,
            ])->save();
          }
        }
      }
    }

    // Remover produtos antigos da mesma origem (Shopee) com mais de 7 dias
    $cutoff = \Drupal::time()->getRequestTime() - (7 * 24 * 3600);

    $ids = \Drupal::entityQuery('aff_product')
      ->condition('field_aff_source', 'shopee')
      ->condition('created', $cutoff, '<')
      ->execute();

    if (!empty($ids)) {
      $entities = \Drupal::entityTypeManager()->getStorage('aff_product')->loadMultiple($ids);
      foreach ($entities as $entity) {
        $entity->delete();
      }
      \Drupal::logger('aff_trends')->notice('Old Shopee products cleaned up: @count', ['@count' => count($ids)]);
    }
  }
}
