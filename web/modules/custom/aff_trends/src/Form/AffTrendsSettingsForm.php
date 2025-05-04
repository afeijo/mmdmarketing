<?php

namespace Drupal\aff_trends\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class AffTrendsSettingsForm extends ConfigFormBase {

  protected function getEditableConfigNames() {
    return ['aff_trends.settings'];
  }

  public function getFormId() {
    return 'aff_trends_settings_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('aff_trends.settings');

    $form['shopee_affid'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Shopee Affiliate ID'),
      '#default_value' => $config->get('shopee_affid'),
      '#required' => TRUE,
    ];

    return parent::buildForm($form, $form_state) + $form;
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('aff_trends.settings')
      ->set('shopee_affid', $form_state->getValue('shopee_affid'))
      ->save();
    parent::submitForm($form, $form_state);
  }
}
