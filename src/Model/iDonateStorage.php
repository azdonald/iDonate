<?php

namespace Drupal\iDonate\Model;
use Drupal\Core\Database\Database;

class iDonateStorage {
    private $connection;

    public function __construct() {
        $this->connection = \Drupal::database();
    }

    public function savePaymentDetails(array $data) {
        try {
            $this->connection->insert('idonate')->fields($data)->execute();
            return true;
        }catch(Exception $e) {
            error_log($e->getMessage());
            return false;
        }
    }
    public function getPayments() {
        $con = $this->connection->query('SELECT * FROM {idonate}');
        $results = $con->fetchAll();
        return $results;
    }
}