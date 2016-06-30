<?php
/**
 * @file
 * Contains \Drupal\lesson3\CurrenciesService.
 */

namespace Drupal\lesson3;

use Drupal\Core\Config;

/**
 * Gets currencies from NBRB web services.
 */
class CurrenciesService {

    const LESSON3_RATES_SERVICE = 'http://www.nbrb.by/Services/XmlExRates.aspx?ondate=01/31/2011';


    /**
     * Constructs a CurrenciesService object.
     */
    public function __construct() {
        $this->config = \Drupal::config('lesson3.settings');
    }


    /**
     * Gets and writes to configs currencies from NBRB web services.
     */
    public function getCurrencies() {

        $currencies= array();
        $date = '';
        
        $client = \Drupal::httpClient();
        $request = $client->get(self::LESSON3_RATES_SERVICE);
        $responseXML = $request->getBody();


        if (!empty($request) && isset($responseXML)) {

            $data = new \SimpleXMLElement($responseXML);
            foreach ($data->Currency as $value) {
                $currencies[] = array(
                    'CharCode' => (string)$value->CharCode,
                    'Name' => (string)$value->Name,
                    'Rate' => (string)$value->Rate,
                );
            }

            foreach ($data->attributes() as $key => $val) {
                $date .= (string) $val;
            }
           
            \Drupal::configFactory()
                ->getEditable('lesson3.settings')
                ->set('lesson3.currencies', $currencies)
                ->set('lesson3.date', $date)
                ->save();
        }        

        return $currencies;
    }
}