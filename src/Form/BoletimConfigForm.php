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
    $form['boletim']['emailnewsletter'] = [
      '#type' => 'webform_email_multiple',
      '#title' => $this->t('E-mail para Newsletter'),
      '#required' => TRUE,
      '#size' => 100,
      '#description' => $this->t('Múltiplos emails devem ser separados por vírgula.'),
      '#default_value' => $config->get('emailnewsletter'),
    ];
    $form['boletim']['emailteste'] = [
      '#type' => 'webform_email_multiple',
      '#title' => $this->t('E-mail para Teste'),
      '#required' => TRUE,
      '#size' => 100,
      '#description' => $this->t('Múltiplos emails devem ser separados por vírgula.'),
      '#default_value' => $config->get('emailteste'),
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();
    $config = $this->config('boletim.settings');
    $config->set('header', $values['header']);
    $config->set('footer', $values['footer']);
    $config->set('emailnewsletter', $values['emailnewsletter']);
    $config->set('emailteste', $values['emailteste']);
    $config->save();
    parent::submitForm($form, $form_state);
  }

}
