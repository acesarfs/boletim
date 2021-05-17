<?php

namespace Drupal\boletim\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class BoletimConfigForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'boletim_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'boletim.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('boletim.settings');
    $form['boletim'] = [
      '#type' => 'details',
      '#title' => $this->t('Boletim'),
      '#description' => $this->t(''),
      '#open' => TRUE,
    ];
    $form['boletim']['header'] = [
      '#type' => 'text_format',
      '#title' => $this->t('Cabeçalho'),
      '#required' => TRUE,
      '#rows' => 10,
      '#default_value' => $config->get('header.value'),
    ];
    $form['boletim']['footer'] = [
      '#type' => 'text_format',
      '#title' => $this->t('Rodapé'),
      '#required' => TRUE,
      '#rows' => 10,
      '#default_value' => $config->get('footer.value'),
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();
    $this->config('boletim.settings')
      ->set('header', $values['header'])
      ->set('footer', $values['footer'])
      ->save();
    parent::submitForm($form, $form_state);
  }

}
