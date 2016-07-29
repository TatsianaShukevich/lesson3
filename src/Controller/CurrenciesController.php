<?php
/**
 * @file
 * Contains \Drupal\lesson3\Controller\CurrenciesController.
 */

namespace Drupal\lesson3\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\lesson3\Entity\CurrencyRate;
use Drupal\lesson3\Entity\Currencies;
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
        try {
            $currencyRateEntities = $this->currenciesService->getCurrencyRateEntitiesOndate('on_page');
            $header = array(
                $this->t('Currency'),
                $this->t('Rate'),
                $this->t('Rate difference'),
            );
            foreach ($currencyRateEntities as $entity) {
                $rows[] = array(
                    'data' => array(
                        $entity->currency->value,
                        $entity->rate->value,
                        $entity->diff_rate->value,
                    ),
                    'no_striping' => TRUE,
                );
            }
            return array(
                '#type' => 'table',
                '#header' => $header,
                '#rows' => $rows,
            );

        }
        catch (\Exception $e) {
            return array(
                '#markup' => $e->getMessage(),
            );
        }
    }

}
