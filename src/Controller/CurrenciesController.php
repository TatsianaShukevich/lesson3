<?php
/**
 * @file
 * Contains \Drupal\lesson3\Controller\CurrenciesController.
 */

namespace Drupal\lesson3\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;


/**
 * Controller routines for lesson3 module routes.
 */
class CurrenciesController extends ControllerBase {

    /**
     * @var $currenciesService \Drupal\lesson3\CurrenciesService
     */
    protected $currenciesService;

    /**
     * {@inheritdoc}
     */
    public static function create(ContainerInterface $container) {
        return new static(
            $container->get('lesson3.currencies_service')
        );
    }

    /**
     * Constructs a CurrenciesController object.
     *
     * @param \Drupal\lesson3\CurrenciesService $currenciesService
     *   The service for currencies.
     */
    public function __construct($currenciesService) {
        $this->currenciesService = $currenciesService;
    }

    /**
     * Shows page with currencies.
     *
     * @return array
     */
    public function showCurrenciesPage() {

        $currencies = $this->config('lesson3.settings')->get('lesson3.currencies');
        $date = $this->config('lesson3.settings')->get('lesson3.date');

        $listCurrencies = '';
        if (!isset($currencies) || empty($currencies)) {
            $currencies = $this->currenciesService->getCurrencies();
        }

        foreach ($currencies as $key => $value) {
            $listCurrencies .= $value['Name'] . ': ' . $value['Rate'] . '</br>';
        }

        return array(
            '#markup' => $date . '</br>'. $listCurrencies
        );

  
    }
}
