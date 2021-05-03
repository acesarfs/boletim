<?php

namespace Drupal\boletim\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\node\Entity\Node;
use Drupal\boletim\Utils;
use Drupal\Core\Datetime\DrupalDateTime;

class BoletimForm extends FormBase {

   const bundles = [
    ['noticias','Destaques','changed','now'],
    ['eventos','Eventos','field_inicio','now'],
    ['defesas','Próximas defesas','field_data_horario','now'],
    ['clipping','FFLCH na mídia','field_data_de_publicacao_clippin','-7 days']
  ];

  public function getFormId() {
    return 'boletim_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    $date = new DrupalDateTime();
    $form['boletim']['title'] = array(
      '#type' => 'textfield',
      '#title' => t('Título'),
      '#default_value' => 'Boletim Acontece na FFLCH USP nº DATA (' . $date->format('d/m/Y') . ')',
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
      $body .= "<div><b>$arr[1]</b><hr></div>";
      $nids = array_filter($form_state->getValue($arr[0]));
      $nodes = Node::loadMultiple(array_keys($nids));
      foreach ($nodes as $node) {
        if($arr[0] == 'noticias' && !empty($node->get('field_imagem')->getValue())) {
          $uri = $node->field_imagem->entity->getFileUri();
	  $img_path = file_create_url($uri);
          $body .= "<img width='400' src='{$img_path}'><br>";
        }

        $body .= "<a href='{$node->nid->value}'>{$node->title->value}</a><br>";
      }
    }

    $body .= 'Este boletim é produzido pelo Serviço de Comunicação Social da Faculdade de Filosofia, Letras e Ciências Humanas da Universidade de São Paulo.<br>
Todos os conteúdos podem ser reproduzidos mediante citação dos créditos como:<br> 
Serviço de Comunicação Social da FFLCH-USP.<br><br>
comunicacaofflch@usp.br | (11) 3091-4612
<p><a href="https://www.facebook.com/fflch"><img alt="FACEBOOK DA FFLCH" height="34" src="https://www.fflch.usp.br/sites/fflch.usp.br/files/inline-images/Facebook.png" width="34" /></a> <a href="https://www.youtube.com/c/uspfflch1"><img alt="Youtube da FFLCH" height="34" src="https://www.fflch.usp.br/sites/fflch.usp.br/files/inline-images/Youtube.jpg" width="34" /></a> <a href="https://twitter.com/uspfflch"><img alt="Twitter da FFLCH" height="34" src="https://www.fflch.usp.br/sites/fflch.usp.br/files/inline-images/Twitter.png" width="34" /></a> <a href="https://www.instagram.com/uspfflch/"><img alt="Instagram" height="34" src="https://www.fflch.usp.br/sites/fflch.usp.br/files/inline-images/Instagram.jpg" width="34" /></a></p>';

    $values = [
      'type' => 'boletim',
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

  }

}
