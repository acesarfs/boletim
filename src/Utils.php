<?php

namespace Drupal\boletim;

use Drupal\node\Entity\Node;
use Drupal\Core\Datetime\DrupalDateTime;
use \Drupal\datetime\Plugin\Field\FieldType\DateTimeItemInterface;

class Utils{

  public static function getNodes($bundle, $title ,$after_field = 'changed', $from = 'now', $to = '+14 days'){

    $timezone = drupal_get_user_timezone();
    $start = new \DateTime($from, new \DateTimeZone($timezone));
    $start->setTimezone(new \DateTimeZone(DateTimeItemInterface::STORAGE_TIMEZONE));
    $start->setTime(21,0);
    $start = DrupalDateTime::createFromDateTime($start);
    $end = new \DateTime($to, new \DateTimeZone($timezone));
    $end->setTimezone(new \DateTimeZone(DateTimeItemInterface::STORAGE_TIMEZONE));
    $end->setTime(23, 59);
    $end = DrupalDateTime::createFromDateTime($end);

    if ($after_field == 'field_data_de_publicacao') {
        $start = $start->format('Y-m-d');
        $end = $end->format('Y-m-d');
    }
    elseif ($after_field == 'changed') {
        $start = strtotime($start);
        $end = strtotime($end);
    }
    else {
        $start = $start->format(DateTimeItemInterface::DATETIME_STORAGE_FORMAT);
        $end = $end->format(DateTimeItemInterface::DATETIME_STORAGE_FORMAT);
    }
    $nids = \Drupal::entityQuery('node')
           ->condition('type',$bundle)
           ->condition($after_field, $start, '>=')
           ->condition($after_field, $end, '<=')
           ->execute();
    
    $nodes = \Drupal\node\Entity\Node::loadMultiple($nids);

    $aux = [];
    foreach ($nodes as $node) {
      if($after_field == 'field_data_de_publicacao') {
        $data = $node->field_data_de_publicacao->value;
        $data = new DrupalDateTime($data, new \DateTimeZone(DateTimeItemInterface::STORAGE_TIMEZONE));
        $aux[$node->nid->value] =  array('title' => $node->title->value, 'field_data_de_publicacao' => $data->format('d/m/Y'));
      }
      elseif($after_field == 'changed') {
        $date = DrupalDateTime::createFromTimestamp($node->changed->value);
        $aux[$node->nid->value] =  array('title' => $node->title->value, 'changed' => $date->format('d/m/Y'));
      }
      else {
        $data = $node->{$after_field}->value;
        $data = new DrupalDateTime($data, new \DateTimeZone(DateTimeItemInterface::STORAGE_TIMEZONE));
        $data->setTimezone(new \DateTimeZone($timezone));
        $data = $data->format('d/m/Y');
        $aux[$node->nid->value] =  array('title' => $node->title->value, $after_field => $data);
       }
    }

    $header_fields = [
	'field_data_de_publicacao' => 'Publicação',
	'field_inicio' => 'Início',
	'field_data_horario' => 'Date',
	'changed' => 'Alteração',
    ];

    $form = [];

    $form[$bundle .'-title'] = [
      '#markup' => "<br><div><h2>{$title}</h2></div>",
    ];

    $form[$bundle] = [
      '#type' => 'tableselect',
      '#header' => array('title' => t('Título'), $after_field => t($header_fields[$after_field])),
      '#options' => $aux,
      '#empty' => t('Nenhum conteúdo encontrado!'),
      '#attributes' => array('id' => 'sortable'),
    ];
    return $form;

  }

}
