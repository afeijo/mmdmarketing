<?php

namespace Drupal\marktng\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides the Marketing Block.
 *
 * @Block(
 *   id = "marktng_block",
 *   admin_label = @Translation("Marketing block"),
 *   category = @Translation("Marketing"),
 * )
 */
class MarktngBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $node = \Drupal::routeMatch()->getParameter('node');
    if ($node instanceof \Drupal\node\NodeInterface) {
      // You can get nid and anything else you need from the node object.
      $nid = $node->id();
      $p = $node->field_marktng->entity;
      $body = $p->field_body->value;
      // $image = file_create_url($node->field_foto->entity->field_media_image->entity->getFileUri());
      $public = \Drupal::service('file_system')->realpath('public://');
      $filename = \Drupal::service('file_system')->realpath($node->field_foto->entity->field_media_image->entity->getFileUri());
      // dump($p, $body);

      $yourname = "Aadarsh Senapati";
      $date = "09 Dec 2013";
      $pos = "2nd";

      $image = imagecreatefromjpeg($filename);
      imagealphablending($image, true);

      $red = imagecolorallocate($image, 150, 0, 0);

      // imagefttext("Image", "Font Size", "Rotate Text", "Left Position",
      // "Top Position", "Font Color", "Font Name", "Text To Print");

      imagefttext($image, 30, 0, 40, 154, $red, 'Helvetica', $yourname);
      imagefttext($image, 20, 0, 312, 206, $red, 'Helvetica', $date);
      imagefttext($image, 20, 0, 82, 256, $red, 'Helvetica', $pos);

/* If you want to display the file in browser */

/*
header('Content-type: image/png');
ImagePng($image);
imagedestroy($image);
 */

/* if you want to save the file in the web server */

/*
$filename = 'certificate_aadarsh.png';
ImagePng($image, $filename);
imagedestroy($image);

 */

/* If you want the user to download the file */

/*
$filename = 'certificate_aadarsh.png';
ImagePng($image,$filename);

header('Pragma: public');
header('Cache-Control: public, no-cache');
header('Content-Type: application/octet-stream');
header('Content-Length: ' . filesize($filename));
header('Content-Disposition: attachment; filename="' .
basename($filename) . '"');
header('Content-Transfer-Encoding: binary');
readfile($filename);

imagedestroy($image);

 */

      $filename = $public . '/teste.png';
      ImagePng($image, $filename);
      imagedestroy($image);

    }
    return [
      '#markup' => $this->t('Hello, World!'),
    ];
  }

}
