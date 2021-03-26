<?php

namespace Drupal\boletim\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Class BoletimController.
 */
class BoletimController extends ControllerBase {

  /**
   * Index.
   *
   * @return string
   *   Return Hello string.
   */
  public function index() {

    $content = \Drupal::formBuilder()->getForm('Drupal\boletim\Form\BoletimForm');

    return [
      '#theme'   => 'boletim',
      '#content' => $content,
      '#cache' => [
        'max-age' => 8600,
      ],  
      '#attached' => [
        'library' => [
          'boletim/boletim',
        ],
      ],       
    ];

  }
}



