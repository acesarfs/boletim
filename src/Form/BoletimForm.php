<?php

namespace Drupal\boletim\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\node\Entity\Node;
use Drupal\boletim\Utils;

class BoletimForm extends FormBase {

   const bundles = [
    ['noticias','Destaques','changed','now'],
    ['eventos','Eventos','field_inicio','now'],
    ['clipping','FFLCH na mídia','field_data_de_publicacao_clippin','-7 days']
  ];

  public function getFormId() {
    return 'boletim_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {

    $form['boletim']['title'] = array(
      '#type' => 'textfield',
      '#title' => t('Título'),
      '#default_value' => 'Boletim Acontece na FFLCH USP nº',
    );
    
    foreach(self::bundles as $arr){
      $form['boletim'][] = Utils::getNodes($arr[0],$arr[1],$arr[2],$arr[3]);
    }

    $form['submit'] = [
      '#type' => 'submit',
      '#prefix' => '<br><br>',
      '#value' => $this->t('Criar boletim'),
    ];

    return $form;
  }

  public function validateForm(array &$form, FormStateInterface $form_state) {

    /*$x = $form_state->getValue('um_texto_qualquer');
    if($x == 'José'){
      $form_state->setErrorByName('um_texto_qualquer',$this->t('José não vai...'));
    }*/
    
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {

    $body = '<div><img width="700" src="https://www.fflch.usp.br/sites/fflch.usp.br/files/boletim.png"></div>';

    foreach(self::bundles as $arr){
      $body .= "<div><h1>$arr[1]</h1></div>";
      $nids = array_filter($form_state->getValue($arr[0]));
      $nodes = Node::loadMultiple(array_keys($nids));
      foreach ($nodes as $node) {
        $body .= "<h3>{$node->title->value}</h3><br>";
      }
    }

    $values = [
      'type' => 'page',
      'title' => $form_state->getValue('title'),
      'moderation_state' => 'published',
      'langcode' => 'pt-br',
      'body' => [['value' => $body, 'format' => 'full_html']],
      'uid' => \Drupal::currentUser()->id(),
    ];

    $node = \Drupal::service('entity_type.manager')->getStorage('node')->create($values);
    $node->save();

    $url = Url::fromRoute('entity.node.edit_form', ['node' => $node->nid->value])->toString();
    $response = new RedirectResponse($url);
    $response->send();

    #dd($form_state->getValue('nides'));
    #dd($form_state->getValue('nodes'));
    #foreach($form_state->getValue('noticias') as $k=>$v){
    #  dsm($k .'  ' . $v );
    #}
    #dd($form_state->getValues());


    #dd($form_state->getValue('noticias'));

    /*$this->config('boletim.settings')
      ->set('um_texto_qualquer', $form_state->getValue('um_texto_qualquer'))
      ->save();*/
    #parent::submitForm($form, $form_state);
  }

}
