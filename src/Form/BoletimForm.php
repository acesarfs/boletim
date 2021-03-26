<?php 

namespace Drupal\boletim\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class BoletimForm extends ConfigFormBase {

  public function getFormId() {
    return 'boletim_form';
  }

  protected function getEditableConfigNames() {
    return [
      'boletim.settings',
    ];
  }

  public function buildForm(array $form, FormStateInterface $form_state) {

    $config = $this->config('boletim.settings');

    $form['um_texto_qualquer'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Digite um texto qualquer'),
      '#default_value' => $config->get('um_texto_qualquer'),
    ];

    $form['high_school'] = array(
        '#type' => 'checkboxes',
        '#options' => array(
            'SAT' => $this->t('SAT'), 
            'ACT' => $this->t('ACT')
        ),
        '#title' => $this->t('What standardized tests did you take?'),
        '#attributes' => array('class' => array('sortable')),
      );

      $form['submit'] = [
        '#type' => 'submit',
        '#value' => $this->t('Submit'),
        ];

    return parent::buildForm($form, $form_state);
  }

  public function validateForm(array &$form, FormStateInterface $form_state) {
    /*$x = $form_state->getValue('um_texto_qualquer');
    if($x == 'José'){
      $form_state->setErrorByName('um_texto_qualquer',$this->t('José não vai...'));
    }*/
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    dsm($form_state->getValue('high_school'));

    $form_state->getValue('um_texto_qualquer');

    /*$this->config('boletim.settings')
      ->set('um_texto_qualquer', $form_state->getValue('um_texto_qualquer'))
      ->save();*/
    parent::submitForm($form, $form_state);
  }

}