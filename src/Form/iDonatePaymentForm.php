<?php

namespace Drupal\iDonate\Form;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\iDonate\Model\iDonateStorage;

class iDonatePaymentForm extends FormBase {

    public function getFormId() {
        return 'payment_form';
    }
    public function buildForm(array $form, FormStateInterface $form_state) {
        $form['name'] = array(
            '#type'  => 'textfield',
            '#title' => t('Full Name:'),
            '#required' => TRUE,
        );
        $form['email'] = array(
            '#type'  => 'email',
            '#title' => t('Email:'),
            '#required' => TRUE,
        );
        $form['amount'] = array(
            '#type'  => 'number',
            '#title' => t('Amount:'),
            '#required' => TRUE
        );
        $form['actions']['#type'] = 'actions';
        $form['actions']['submit'] = array(
          '#type' => 'submit',
          '#value' => $this->t('Donate'),
          '#button_type' => 'primary',
        );
        return $form;
    }
    public function submitForm(array &$form, FormStateInterface $form_state) {
        $field=$form_state->getValues();
        $data = array(
            'name' => $field['name'],
            'email' => $field['email'],
            'amount' => $field['amount'],
            'date_paid' => date('Y-m-d H:i:s'),
        );
        $db = new iDonateStorage();
        $db->savePaymentDetails($data);
        $form_state['redirect'] = 'admin/content/bd_contact';        
        if ($db) {
            drupal_set_message("succesfully saved");
            return;
        }
        drupal_set_message("Saving failed");
        return;     
    
    }
}