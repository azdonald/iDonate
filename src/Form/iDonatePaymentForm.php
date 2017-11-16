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
        $config = \Drupal::config('iDonate.settings');
        $mode = $config->get('mode');
        $currency = $config->get('currency');
        $publishableKey = $config->get('test publishable key');
        $secretKey = $config->get('test secret key');        
        if ($mode == 'PROD') {
            $publishableKey = $config->get('publishable key');
            $secretKey = $config->get('secret key'); 
        }
        \Stripe\Stripe::setApiKey($secretKey);
        if ($_SERVER['REQUEST_METHOD'] === 'POST'){
            $token = $_POST['stripeToken'];
            $data = array(
                'name' => $_POST['name'],
                'email' => $_POST['stripeEmail'],
                'amount' => $_POST['amount'],
                'date_paid' => date('Y-m-d H:i:s'),
            );
            $customer = \Stripe\Customer::create(array(
                "source" => $token,
                "description" => "Donation from ".$data['name'])
              );
            $charge = \Stripe\Charge::create(array(
                "amount" => 1000,
                "currency" => $currency,
                "description" => "Donation from ".$data['name'],
                "source" => $customer->id,
              ));
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
        $form[]['#attached']['library'][] = 'iDonate/iDonate';  
        $form['container'] = array(
            '#type' => 'container',
            '#weight' => 5,
            '#states' => array(
                'visible' => array(
                  ':input[name="name"]' => array('filled' => TRUE,),                  
                  ':input[name="email"]' => array('filled' => TRUE,),                  
                  ':input[name="amount"]' => array('filled' => TRUE,),
              ),
            ),           
        );    
        $form['container']['checkout'] = array(            
            '#type' => 'inline_template',
            '#template' => ' <script            
            src="https://checkout.stripe.com/checkout.js" class="stripe-button"
            data-key='.$publishableKey.'
            data-email=""
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