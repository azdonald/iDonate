<?php

namespace Drupal\iDonate\Form;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\iDonate\Model\iDonateStorage;
use Symfony\Component\HttpFoundation\RedirectResponse;

class iDonatePaymentForm extends FormBase {

    public function getFormId() {
        return 'payment_form';
    }
    public function buildForm(array $form, FormStateInterface $form_state) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST'){
            $field=$form_state->getValues();
            $data = array(
                'name' => $field['name'],
                'email' => $field['email'],
                'amount' => $field['amount'],
                'date_paid' => date('Y-m-d H:i:s'),
            );
            $db = new iDonateStorage();
            $db->savePaymentDetails($data);
            $form_state->setRedirect('iDonate.page');       
            if ($db) {
                drupal_set_message("Donation Successful");
                return;
            }
            drupal_set_message("Donation failed");
            return;     
    }
        $form['name'] = array(
            '#type'  => 'textfield',
            '#title' => t('Full Name:'),
            '#required' => TRUE,
            '#attributes' => array(
                'id' => 'name',
                ),
        );
        $form['email'] = array(
            '#type'  => 'email',
            '#title' => t('Email:'),
            '#required' => TRUE,
            '#attributes' => array(
                'id' => 'email',),           
                
        );        
        $form['amount'] = array(
            '#type'  => 'number',
            '#title' => t('Amount:'),
            '#required' => TRUE,
            '#attributes' => array(
                'id' => 'amount',
                ),
            
        );     
        $config = \Drupal::config('iDonate.settings');        
        $apiKey = $config->get('apikey');
        $form['container'] = array(
            '#type' => 'container',
            '#weight' => 5,
            '#states' => array(
                'visible' => array(
                  ':input[name="name"]' => array(
                    'filled' => TRUE,
                    ),
              ),
            ),           
          );       
        $form['container']['checkout'] = array(            
            '#type' => 'inline_template',
            '#template' => ' <script            
            src="https://checkout.stripe.com/checkout.js" class="stripe-button"
            data-key='.$apiKey.'
            data-name="Stripe.com"
            data-description="iDonate"
            data-image="https://stripe.com/img/documentation/checkout/marketplace.png"
            data-locale="auto"
            data-zip-code="true">
          </script>',
          
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
        $form_state->setRedirect('iDonate.page');       
        if ($db) {
            drupal_set_message("Donation Successful");
            return;
        }
        drupal_set_message("Donation failed");
        return;     
    
    }
}