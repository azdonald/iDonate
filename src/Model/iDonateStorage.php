<?php

namespace Drupal\iDonate\Model;
use Drupal\Core\Database\Database;

class iDonateStorage {
    private $query;

    public function __construct() {
        $this->query = \Drupal::database();
    }

    public function savePaymentDetails(array $data) {
        try {
            $this->query->insert('idonate')->fields($data)->execute();
            return true;
        }catch(Exception $e) {
            error_log($e->getMessage());
            return false;
        }
    }
    public function getPayments() {
        $this->query->select('iDonate', 'i');
        $this->query-fields('i', ['id', 'name', 'email', 'amount', 'date_paid', 'transaction_ref','status']);
        $results = $this->query->execute()->fetchAll();
        return $results;
    }
}