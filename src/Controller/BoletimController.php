<?php

namespace Drupal\boletim\Controller;

use Drupal\Core\Controller\ControllerBase;

use Drupal\Core\Mail\MailManagerInterface;
use Drupal\Component\Utility\SafeMarkup;
use Drupal\Component\Utility\Html;

use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\node\Entity\Node;

class BoletimController extends ControllerBase {

  public function index() {

    $form = \Drupal::formBuilder()->getForm('Drupal\boletim\Form\BoletimForm');

    return [
      '#theme'   => 'boletim',
      '#form' => $form,
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

  public function email($node) {

    $tos = ['eventosdf_fflch@listas.usp.br','fflch@listas.usp.br'];

    foreach($tos as $to) {
      $mailManager = \Drupal::service('plugin.manager.mail');
      $module = 'boletim';
      $key = 'boletim_key';
      $params['subject'] = $node->title->value;
      $params['message'] = $node->body->value;
      $langcode = \Drupal::currentUser()->getPreferredLangcode();
      $reply = \Drupal::config('system.site')->get('mail');
      $send = true;

      $result = $mailManager->mail($module, $key, $to, $langcode, $params, $reply , $send);
    
      if ($result['result'] != true) {
        $message = t('There was a problem sending your email notification to @email.', array('@email' => $to));
        drupal_set_message($message, 'error');
        \Drupal::logger('boletim')->error($message);
      } else {
        $message = t('An email notification has been sent to @email ', array('@email' => $to));
        drupal_set_message($message);
        \Drupal::logger('boletim')->notice($message);
      }
  
      $url = Url::fromRoute('entity.node.edit_form', ['node' => $node->nid->value])->toString();
      $response = new RedirectResponse($url);
      $response->send();
    }  
  }
}



