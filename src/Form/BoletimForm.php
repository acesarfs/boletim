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

    $nids = \Drupal::entityQuery('node')->condition('type','page')->execute();
    $nodes = \Drupal\node\Entity\Node::loadMultiple($nids);

    $titles = [];
    foreach ($nodes as $node) {
      $node = \Drupal::entityTypeManager()->getStorage('node')->load($node->nid->value);
      $titles[$node->nid->value] =  $node->title->value ;
    }

    $form['um_texto_qualquer'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Digite um texto qualquer'),
      '#default_value' => $config->get('um_texto_qualquer'),
    ];

    $form['nodes'] = array(
      '#type' => 'checkboxes',
      '#options' => $titles,
      '#title' => $this->t('What standardized tests did you take?'),
    );


      $form['noticias'] = array(
        '#type' => 'empty_value',
        '#prefix' => '<ul id="sortable">',
        '#suffix' => '</ul>'
      );

    foreach ($titles as $key => $title) {
      $form['noticias'][] = [
          '#theme' => 'input__checkbox',
          '#prefix' => '<li class="ui-state-default"><span class="ui-icon ui-icon-arrowthick-2-n-s"></span>',
          '#suffix' => '</li>',
          #'#data-drupal-selector' => 'noticias',
          '#attributes' => [
            // You can change name attribute.
            'name' => 'noticias['.$key.']',
            'value' => $key,
            // 'class' => 'filter',
            'type' => 'checkbox',

          ],
          '#children' => [
            '#type' => 'label',
            '#title' => $title,
            '#title_display' => 'after',
          ]
        ];
      }
/*
      foreach ($titles as $key => $title) {
      $form['noticias'][] = [
          '#type' => 'checkbox',
          '#prefix' => '<li class="ui-state-default"><span class="ui-icon ui-icon-arrowthick-2-n-s"></span>',
          '#suffix' => '</li>',
          '#title'   => $title,
          '#title_display' => 'after',
          '#attributes' => [
            // You can change name attribute.
            'name' => 'noticias['.$key.']',
            #'value' => $key,
            #'value' => 0,
            // 'class' => 'filter',
          ],
        ];
      }
*/
    return parent::buildForm($form, $form_state);
  }

  public function validateForm(array &$form, FormStateInterface $form_state) {
    /*$x = $form_state->getValue('um_texto_qualquer');
    if($x == 'José'){
      $form_state->setErrorByName('um_texto_qualquer',$this->t('José não vai...'));
    }*/
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {

    dsm($form_state->getValue('nodes'));
    #foreach($form_state->getValue('noticias') as $k=>$v){
    #  dsm($k .'  ' . $v );
    #}
    dsm($form_state->getValues());


    $form_state->getValue('noticias');

    /*$this->config('boletim.settings')
      ->set('um_texto_qualquer', $form_state->getValue('um_texto_qualquer'))
      ->save();*/
    #parent::submitForm($form, $form_state);
  }

}