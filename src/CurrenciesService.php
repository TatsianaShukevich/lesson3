<?php
/**
 * @file
 * Contains \Drupal\lesson3\CurrenciesService.
 */

namespace Drupal\lesson3;

use Drupal\Core\Config;
use Drupal\lesson3\Entity\CurrencyRate;
use Drupal\lesson3\Entity\Currencies;

/**
 * Gets currencies from NBRB web services.
 */
class CurrenciesService {

    const LESSON3_RATES_SERVICE = 'http://www.nbrb.by/Services/XmlExRates.aspx';

    /**
     * Constructs a CurrenciesService object.
     */
    public function __construct() {
        $this->config = \Drupal::config('lesson3.settings');
    }

    /**
     * Gets currencies from NBRB web services.
     * 
     * @param string/NULL $ondate
     *  If date wasn't pass the $date is current date.
     * 
     * @return array
     *  Return array with list of currencies and date
     * 
     * @throws \Exception
     */
    public function getCurrenciesOnDate($ondate = NULL) {
        
        if(!$ondate) {
            $ondate = date('m/d/Y');
        }

        $currencies= array();
        $date = '';
        
        $client = \Drupal::httpClient();

        $response = $client->request('GET', self::LESSON3_RATES_SERVICE, array(
            'query' => array(
                'ondate' => $ondate,
            )
        ));

        if($response->getStatusCode() == 200) {
            $responseXML = $response->getBody();

            if (!empty($response) && isset($responseXML)) {

                $data = new \SimpleXMLElement($responseXML);
                foreach ($data->Currency as $value) {
                    $currencies[(string)$value->Name] = array(
                        'CharCode' => (string)$value->CharCode,
                        'Name' => (string)$value->Name,
                        'Rate' => (float)$value->Rate,
                    );

                     //save configuration entity Currency if not exist
                    if(!Currencies::load(strtolower((string)$value->CharCode))) {
                        $this->saveCurrencyEntities($currencies[(string)$value->Name]);
                    }                    
                }

                foreach ($data->attributes() as $key => $val) {
                    $date .= (string) $val;
                }
            }
            return array('currencies' => $currencies, 'date' => $date);
        }
        else {
            throw  new \Exception("The service is unavailable");
        }        
    }

    /**
     * Saves currency to CurrencyRate entity.
     *
     * @param array $currencies
     *  Array of currencies.
     * @param string $date
     *  Date.
     */
    public function saveCurrencyRateEntities($currencies, $date) {
        //Calculates a rate difference
        $this->calculateRatesDifference($currencies, $date);
        
        foreach($currencies as $currency) {
            $values = array(
                'name' => $currency['Name'] . '_' . $date,
                'created' => REQUEST_TIME,
                'date' => $date,
                'currency' => $currency['Name'],
                'rate' => $currency['Rate'],
                'diff_rate' => $currency['diff_rate'],
                'timestamp_date' => strtotime($date),
                'currency_settings_id' => strtolower($currency['CharCode']),
            );
           
            $entity = CurrencyRate::create($values);
            $entity->save();

        }
    }

    /**
     * Calculates rate difference with previous day.
     *
     * @param array $currencies
     *  Array of currencies.
     * @param string $date
     *  Date.
     *
     * @return array
     *  Return array with list of currencies and date
     *
     * @throws \Exception
     */
    protected function calculateRatesDifference(&$currencies, $date) {

        $timestampPreviousDay = strtotime($date) - (24 * 60 * 60);

        $datePreviousDay = date('m/d/Y', $timestampPreviousDay);

        $query = \Drupal::entityQuery('currency_rate')
           ->condition('date', $datePreviousDay, '=');

        $currencyIds = $query->execute();
        
        if($currencyIds) {
            $entities = CurrencyRate::loadMultiple($currencyIds);
            foreach ($entities as $entity) {
                $currencyName = $entity->currency->value;
                $currencies[$currencyName]['diff_rate'] = $currencies[$currencyName]['Rate'] - $entity->rate->value;
            }
        }
//        else {
//            throw new \Exception('There are not currencies for previous day.');
//        }
    }
    
    /**
     * Deletes CurrencyRate entities.
     */
    public function deleteCurrencyRateEntities() {

        $query = \Drupal::entityQuery('currency_rate');
        $currencyIds = $query->execute();
     
        $entities = CurrencyRate::loadMultiple($currencyIds);
        foreach ($entities as $entity) {
            $entity->delete();
        }
    }

    /**
     * Deletes Currencies entities.
     */
    public function deleteCurrenciesEntities() {

        $query = \Drupal::entityQuery('currency_rate');
        $currencyIds = $query->execute();

        $entities = CurrencyRate::loadMultiple($currencyIds);
        if($entities) {
            foreach ($entities as $entity) {
                $currenciesID = $entity->currency_settings_id->getValue()[0]['target_id'];
                $currenciesEntity = Currencies::load($currenciesID);
                if($currenciesEntity) {
                    $currenciesEntity->delete();
                }
            }
        }
        else {
            throw new \Exception('There are not any CurrencyRate entities needed to delete Currencies entities.');
        }

    }

    /**
     * Gets CurrencyRate entities for displaying on page or in block.
     *
     * @param string $displayMode
     *  Two options are available 'in_block' or 'on_page'
     * @param string/NULL $date
     *  If $date wasn't pass the $date is current date.
     *
     * @return array
     *  Return array with CurrencyRate entities.
     *
     * @throws \Exception
     */
    public function getCurrencyRateEntitiesOndate($displayMode, $date = NULL) {
        
        if(!$date) {
            $date = date('m/d/Y');
        }

        $query = \Drupal::entityQuery('currency_rate')
            ->condition('date', $date, '=');
        
        $currencyRateIds = $query->execute();

        $entities = CurrencyRate::loadMultiple($currencyRateIds);
        
        foreach ($entities as $key => &$entity) {
            $currenciesEntity = Currencies::load($entity->currency_settings_id->getValue()[0]['target_id']);

            if (!$currenciesEntity->checkDisplayMode($displayMode)) {
                unset($entities[$key]);
            }
        }
    
        if($entities) {           
            return $entities;
        }
        else {
            throw  new \Exception("No entities for displaying $displayMode");
        }
    }

    /**
     * Saves currency to CurrencyRate entity.
     *
     * @param array $currency
     *  Array of options for currency.
     */
    protected function saveCurrencyEntities($currency) {
        $values = array(
            'label' => $currency['Name'],
            'id' => strtolower($currency['CharCode']),
            'currency' => $currency['Name'],
            'code' => $currency['CharCode'],
            'in_block' => FALSE,
            'on_page' => TRUE, 
        );

        $entity = Currencies::create($values);
        $entity->save();
    }
}