<?php

namespace Drupal\boletim\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\node\Entity\Node;

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

    $options = [];
    foreach ($nodes as $node) {
      $options[$node->nid->value] =  array('title' => $node->title->value);
    }

    $form['table'] = array(
      '#type' => 'tableselect',
      '#header' => array('title' => t('Título')),
      '#options' => $options,
      '#empty' => t('Nenhum conteúdo encontrado!'),
      '#attributes' => array('id' => 'sortable'),
    );

    return parent::buildForm($form, $form_state);
  }

  public function validateForm(array &$form, FormStateInterface $form_state) {
    /*$x = $form_state->getValue('um_texto_qualquer');
    if($x == 'José'){
      $form_state->setErrorByName('um_texto_qualquer',$this->t('José não vai...'));
    }*/
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {

    $results = array_filter($form_state->getValue('table'));

    $body = '';
    $nodes = Node::loadMultiple(array_keys($results));
    foreach ($nodes as $node) {
      $body .= "<h1>{$node->title->value}</h1> <br><br>";
    }

    $values = [
      'type' => 'page',
      'title' => 'Teste',
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
