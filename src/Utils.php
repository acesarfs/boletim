<?php

namespace Drupal\boletim;

use Drupal\node\Entity\Node;
use Drupal\Core\Datetime\DrupalDateTime;
use \Drupal\datetime\Plugin\Field\FieldType\DateTimeItemInterface;

class Utils{

  public static function getNodes($bundle, $title ,$after_field = 'changed', $from = 'now', $to = '+14 days'){

    if($after_field == 'changed') {
      $nids = \Drupal::entityQuery('node')
             ->condition('type',$bundle)
             ->condition($after_field, strtotime('-8 days'), '>=')
             ->execute();
    } else {
      $timezone = drupal_get_user_timezone();
      $start = new \DateTime($from, new \DateTimeZone($timezone));
      $start->setTime(0,0);
      $start->setTimezone(new \DateTimeZone(DateTimeItemInterface::STORAGE_TIMEZONE));
      $start = DrupalDateTime::createFromDateTime($start);

      $end = new \DateTime($to, new \DateTimezone($timezone));
      $end->setTime(23, 0);
      $end->setTimezone(new \DateTimeZone(DateTimeItemInterface::STORAGE_TIMEZONE));
      $end = DrupalDateTime::createFromDateTime($end);

      $nids = \Drupal::entityQuery('node')
             ->condition('type',$bundle)
             ->condition($after_field, $start->format(DateTimeItemInterface::DATETIME_STORAGE_FORMAT), '>=')
             ->condition($after_field, $end->format(DateTimeItemInterface::DATETIME_STORAGE_FORMAT), '<=')
             ->execute();
    }
    
    $nodes = \Drupal\node\Entity\Node::loadMultiple($nids);

    $aux = [];
    foreach ($nodes as $node) {
      if($after_field == 'changed') {
        $aux[$node->nid->value] =  array('title' => $node->title->value);
      } else {
        $Ymd = substr($node->{$after_field}->value,0,10);
        $date = implode('/',array_reverse(explode('-',$Ymd)));
        $aux[$node->nid->value] =  array('title' => $node->title->value . ' - ' . $date);
       }
    }

    $form = [];

    $form[$bundle .'-title'] = [
      '#markup' => "<br><div><h2>{$title}</h2></div>",
    ];

    $form[$bundle] = [
      '#type' => 'tableselect',
      '#header' => array('title' => t('Título')),
      '#options' => $aux,
      '#empty' => t('Nenhum conteúdo encontrado!'),
      '#attributes' => array('id' => 'sortable'),
    ];
    return $form;

  }
}

