<?php

namespace Drupal\boletim\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\node\Entity\Node;
use Drupal\boletim\Utils;
use Drupal\Core\Datetime\DrupalDateTime;
use \Drupal\datetime\Plugin\Field\FieldType\DateTimeItemInterface;

class BoletimForm extends FormBase {

   const bundles = [
    ['noticias','Destaques','field_data_de_publicacao','last week saturday','now'],
    ['eventos','Eventos','field_inicio','now', 'Friday next week'],
    ['defesas','Próximas defesas','field_data_horario','now','Friday next week'],
    ['clipping','FFLCH na mídia','changed','last week saturday', 'now']
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
      $form['boletim'][] = Utils::getNodes($arr[0],$arr[1],$arr[2],$arr[3],$arr[4]);
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

    $timezone = drupal_get_user_timezone();
    $body = '<div><img width="700" src="https://www.fflch.usp.br/sites/fflch.usp.br/files/boletim.png"></div>';

    foreach(self::bundles as $arr){
      $body .= "<div><b>$arr[1]</b><hr></div>";
      $nids = array_filter($form_state->getValue($arr[0]));
      $nodes = Node::loadMultiple(array_keys($nids));
      foreach ($nodes as $node) {
        $options = ['absolute' => TRUE];
        $url = \Drupal\Core\Url::fromRoute('entity.node.canonical', 
                                 ['node' => $node->nid->value], $options);
        $url = $url->toString();
        $link = "<a href='{$url}'>{$node->title->value}</a><br>";
        if($arr[0] == 'noticias') { 
          if(!empty($node->get('field_imagem')->getValue())) {
            $uri = $node->field_imagem->entity->getFileUri();
	    $img_path = file_create_url($uri);
            $body .= "<img width='400' src='{$img_path}'><br>";
          }
          $linha_fina = $node->field_linha_fina->value;
          $body .= "$link<br>$linha_fina<br>";
        }
        if($arr[0] == 'clipping'){
          $date = DrupalDateTime::createFromTimestamp($node->changed->value);
	  $artigo_uri = "<a href='{$node->field_link_da_materia_artigo->uri}'>{$node->title->value}</a>";
          $veiculo = $node->field_nome_do_veiculo->value;
          $body .= "<span>" . $date->format('d/m') . "</span><br>" . $artigo_uri . " (" . $veiculo . ")<br>";          
          $body .= "<span>" . $node->field_resumo->value . "<span></br>";
        }
	if($arr[0] == 'eventos'){
          $field_inicio = $node->field_inicio->value;
          $field_inicio = new DrupalDateTime($field_inicio, new \DateTimeZone(DateTimeItemInterface::STORAGE_TIMEZONE));
          $field_inicio->setTimezone(new \DateTimeZone($timezone));
          $field_inicio = $field_inicio->format('d/m/Y \- H:i');
          $body .= $link . "<span>" . $field_inicio . "</span><br>";
          if ($node->field_inscricao->value == 'cominscricao') {
            $field_inicio_inscricao = $node->field_inicio_inscricao->value;
            $field_inicio_inscricao = new DrupalDateTime($field_inicio_inscricao, new \DateTimeZone(DateTimeItemInterface::STORAGE_TIMEZONE));
            $field_inicio_inscricao = $field_inicio_inscricao->format('d/m/Y');
            $field_fim_inscricao = $node->field_fim_inscricao->value;
            $field_fim_inscricao = new DrupalDateTime($field_fim_inscricao, new \DateTimeZone(DateTimeItemInterface::STORAGE_TIMEZONE));
            $field_fim_inscricao = $field_fim_inscricao->format('d/m/Y');
            $body .= "<span>Inscrições: $field_inicio_inscricao - $field_fim_inscricao</span><br>";
          }
        }
        if($arr[0] == 'defesas'){
          $body .= $link . "<span>Programa: " . $node->field_programa->value . "</span><br>";
        }
        $body .= "<br>";
      }

    }

    $body .= '<hr>Este boletim é produzido pelo Serviço de Comunicação Social da Faculdade de Filosofia, Letras e Ciências Humanas da Universidade de São Paulo.<br>
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
