<?php

namespace Drupal\iDonate\Controller;
use Drupal\Core\Controller\ControllerBase;
use Drupal\iDonate\Model\iDonateStorage;


class iDonateController extends ControllerBase {

    public function list() {
        $table_header = array(
            'id' => t('SrNo'),
            'name' => t('name'),
            'email' => t('email'),
            'amount' => t('amount'),
            'date' => t('date'),
            'status' => t('status'),
        );
        $db = new iDonateStorage();
        $results = $db->getPayments();
        $rows = array();

        foreach($results as $result) {
            $rows[] = array(
                'id'  => $result->id,
                'name' => $result->name,
                'email' => $result->email,
                'amount' => $result->amount,
                'date' => $result->date_paid,
                'status' => $result->status,
            );
        }
        $form['table'] = [
            '#type' => 'table',
            '#header' => $table_header,
            '#rows' => $rows,
            '#empty' => t('No payments found'),
        ];
        return $form;

    }
}