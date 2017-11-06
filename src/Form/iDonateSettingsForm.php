<?php

namespace Drupal\iDonate\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class iDonateSettingsForm extends ConfigFormBase {
    protected function getEditableConfigNames() {
        return [
            'iDonate.settings',
        ];
    }
    public function getFormId() {
        return 'iDonate_admin_settings';
    }
    public function buildForm(array $form, FormStateInterface $form_state) {
        $form['provider'] = array(
            '#type'  => 'select',
            '#title' => ('Payment Provider'),
            '#options' => array(
                'Stripe' => t('Stripe'),
            ),
            '#required' => TRUE,
        );
        $form['apikey'] = array(
            '#type'  => 'textfield',
            '#title' => t('Api Key:'),
            '#required' => TRUE,
        );
        $form['actions']['#type'] = 'actions';
        $form['actions']['submit'] = array(
          '#type' => 'submit',
          '#value' => $this->t('Save'),
          '#button_type' => 'primary',
        );
        return $form;
    }
}