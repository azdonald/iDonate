<?php

namespace Drupal\iDonate\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class iDonateSettingsForm extends ConfigFormBase {

    private $mode;
    private $secretKey;
    private $publishableKey;
    private $provider;
    private $currency;

    protected function getEditableConfigNames() {
        return [
            'iDonate.settings',
        ];
    }
    public function getFormId() {
        return 'iDonate_admin_settings';
    }
    public function buildForm(array $form, FormStateInterface $form_state) {
        $config = \Drupal::config('iDonate.settings');
        $mode = $config->get('mode');
        $provider = $config->get('provider');
        $currency = $config->get('currency');
        $secretKey = $config->get('secret key');
        $publishableKey = $config->get('publishable key');
        $testsecretKey = $config->get('test secret key');
        $testpublishableKey = $config->get('test publishable key');

        $form['provider'] = array(
            '#type'  => 'select',
            '#title' => ('Payment Provider'),
            '#options' => array(
                'Stripe' => t('Stripe'),
            ),
            '#required' => TRUE,
        );
        $form['containerTest'] = array(
            '#type' => 'container',
            '#weight' => 5,
            '#states' => array(
                'invisible' => array(
                  ':input[name="mode"]' => array('checked' => TRUE,),  
              ),
            ),           
        );
        $form['containerTest']['publishableTest'] = array(
            '#type'  => 'textfield',
            '#title' => t('Test Publishable Key:'),
            '#required' => ($mode == 'PROD' ? FALSE : TRUE),
            '#default_value' => $testpublishableKey,
        );
        $form['containerTest']['secretTest'] = array(
            '#type'  => 'textfield',
            '#title' => t('Test Secret Key:'),
            '#required' => ($mode == 'PROD' ? FALSE : TRUE),
            '#default_value' => $testsecretKey,
        ); 
        $form['container'] = array(
            '#type' => 'container',
            '#weight' => 5,
            '#states' => array(
                'visible' => array(
                  ':input[name="mode"]' => array('checked' => TRUE),
              ),
            ),           
        ); 
        $form['container']['publishable'] = array(
            '#type'  => 'textfield',
            '#title' => t('Publishable Key:'),
            '#required' => ($mode == 'PROD' ? TRUE : FALSE),
            '#default_value' => $secretKey,
        );
        $form['container']['secret'] = array(
            '#type'  => 'textfield',
            '#title' => t('Secret Key:'),
            '#required' => ($mode == 'PROD' ? TRUE : FALSE),
            '#default_value' => $secretKey,
        );
        
        $form['currency'] = array(
            '#type'  => 'select',
            '#title' => ('Currency'),
            '#options' => array(
                'CAD' => t('CAD'),
                'USD' => t('USD'),
                'GBP' => t('GBP'),
                'EUR' => t('EUR'),
            ),
            '#required' => TRUE,
        );
        $form['mode'] = array(
            '#type' => 'checkbox',
            '#title' => t('Production'),
            '#default_value' => ($mode == 'PROD' ? TRUE : FALSE)
        );
        $form['actions']['#type'] = 'actions';
        $form['actions']['submit'] = array(
          '#type' => 'submit',
          '#value' => $this->t('Save'),
          '#button_type' => 'primary',
        );
        return $form;
    }
    public function submitForm(array &$form, FormStateInterface $form_state) {
        $config = \Drupal::service('config.factory')->getEditable('iDonate.settings');
        $config->set('provider', $form_state->getValue('provider'));
        $config->set('mode', ($form_state->getValue('mode') == 1 ? 'PROD': 'TEST'));
        $config->set('currency', $form_state->getValue('currency'));
        if ($form_state->getValue('mode') == 1 ) {
            $config->set('publishable key', $form_state->getValue('publishable'));
            $config->set('secret key', $form_state->getValue('secret'));
        }
        if ($form_state->getValue('mode') == 0 ) {
            $config->set('test publishable key', $form_state->getValue('publishableTest'));
            $config->set('test secret key', $form_state->getValue('secretTest'));
        }
        $config->save();  

        drupal_set_message("Settings Saved");
        return;
    
    }
}