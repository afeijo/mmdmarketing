<?php

namespace Drupal\aff_trends\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\user\UserInterface;

/**
 * Defines the Affiliate Product entity.
 *
 * @ContentEntityType(
 *   id = "aff_product",
 *   label = @Translation("Affiliate Product"),
 *   base_table = "aff_product",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "title",
 *     "uuid" = "uuid",
 *     "uid" = "user_id"
 *   },
 *   handlers = {
 *     "list_builder" = "Drupal\\Core\\Entity\\EntityListBuilder",
 *     "form" = {
 *       "default" = "Drupal\\Core\\Entity\\ContentEntityForm",
 *       "add" = "Drupal\\Core\\Entity\\ContentEntityForm",
 *       "edit" = "Drupal\\Core\\Entity\\ContentEntityForm",
 *       "delete" = "Drupal\\Core\\Entity\\ContentEntityDeleteForm"
 *     },
 *     "access" = "Drupal\\Core\\Entity\\EntityAccessControlHandler"
 *   },
 *   admin_permission = "administer aff product entities",
 *   links = {
 *     "canonical" = "/aff_product/{aff_product}",
 *     "edit-form" = "/aff_product/{aff_product}/edit",
 *     "delete-form" = "/aff_product/{aff_product}/delete",
 *     "collection" = "/admin/content/aff_product"
 *   }
 * )
 */
class AffProduct extends ContentEntityBase {

  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['user_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Author'))
      ->setSetting('target_type', 'user')
      ->setDefaultValueCallback(static::class . '::getCurrentUserId');

    $fields['title'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Product Title'))
      ->setRequired(TRUE)
      ->setSettings(['max_length' => 255]);

    $fields['field_aff_source'] = BaseFieldDefinition::create('list_string')
      ->setLabel(t('Source'))
      ->setSettings(['allowed_values' => ['shopee' => 'Shopee', 'ml' => 'Mercado Livre', 'amazon' => 'Amazon']]);

    $fields['field_aff_url'] = BaseFieldDefinition::create('link')
      ->setLabel(t('Affiliate URL'));

    $fields['field_original_url'] = BaseFieldDefinition::create('link')
      ->setLabel(t('Original URL'));

    $fields['field_price'] = BaseFieldDefinition::create('decimal')
      ->setLabel(t('Price'))
      ->setSetting('scale', 2);

    $fields['field_image_url'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Image URL'))
      ->setSettings(['max_length' => 1024]);

    $fields['field_score'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Trend Score'));

    $fields['created'] = BaseFieldDefinition::create('created')->setLabel(t('Created'));
    $fields['changed'] = BaseFieldDefinition::create('changed')->setLabel(t('Changed'));

    return $fields;
  }

  public static function getCurrentUserId() {
    return [\Drupal::currentUser()->id()];
  }
}
