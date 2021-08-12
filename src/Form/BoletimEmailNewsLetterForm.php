<?php

namespace Drupal\boletim\Form;

use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\node\NodeInterface;

class BoletimEmailNewsLetterForm extends ConfirmFormBase {

  /**
   * ID of the item to delete.
   *
   * @var int
   */
  protected $node;

  /**
   * {@inheritdoc}
   */
  public function getFormId() : string {
    return "emailnewsletter_form";
  }

  public function buildForm(array $form, FormStateInterface $form_state,
                            NodeInterface $node = NULL ) {
    $this->node = $node;
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl() {
    return new Url('entity.node.edit_form', ['node' => $this->node->id()]);
  }

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return $this->t('Você confirma o envio do (%titulo) para o email newsletter?',
           ['%titulo' => $this->node->title->value]);
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {

    $config = $this->config('boletim.settings');
    $email = trim($config->get('emailnewsletter'));
    if(empty($email)) {
      drupal_set_message(t('Não há e-mails cadastrados.'), 'error'); 
    }    
    else {
      $form_state->setRedirect('boletim.boletim_send_mail', 
                   ['node' => $this->node->id(), 'email' => $email]);
    }

  }

}
