<?php
/**
 * @file
 * Contains \Drupal\lesson3\Plugin\Block\CurrenciesBlock.
 */

namespace Drupal\lesson3\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\lesson3\CurrenciesService;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;

/**
 * Provides a block with currencies.
 *
 * @Block(
 *   id = "currencies_block",
 *   admin_label = @Translation("Currencies block"),
 * )
 */

class CurrenciesBlock extends BlockBase implements ContainerFactoryPluginInterface {

    /**
     * @var $currenciesService \Drupal\lesson3\CurrenciesService
     */
    protected $currenciesService;
    
    /**
     * The config.
     *
     * @var \Drupal\Core\Config\Config
     */
    private $config;    
    
    /**
     * Constructs a CurrenciesBlock object
     * 
     * @param array $configuration
     * @param string $plugin_id
     * @param mixed $plugin_definition
     * @param \Drupal\lesson3\CurrenciesService $currenciesService
     */
    public function __construct(array $configuration, $plugin_id, $plugin_definition, CurrenciesService $currenciesService) {
        parent::__construct($configuration, $plugin_id, $plugin_definition);

        $this->currenciesService = $currenciesService;

        $this->config = \Drupal::config('lesson3.settings');

    }

    /**
     * {@inheritdoc}
     */
    public static function create(ContainerInterface $container,  array $configuration, $plugin_id, $plugin_definition) {
        return new static(
            $configuration,
            $plugin_id,
            $plugin_definition,
            $container->get('lesson3.currencies_service')
        );
    }

    /**
     * {@inheritdoc}
     */
    public function build() {
        try {
            $currencyRateEntities = $this->currenciesService->getCurrencyRateEntitiesOndate('in_block');

            foreach ($currencyRateEntities as $entity) {
                $rows[] = array(
                    'data' => array(
                        $entity->currency->value,
                        $entity->rate->value,
                    ),
                    'no_striping' => TRUE,
                );
            }
            return array(
                '#type' => 'table',
                '#rows' => $rows,
            );

        }
        catch (\Exception $e) {
            return array(
                '#markup' => $e->getMessage(),
            );
        }
    }

    /**
     * {@inheritdoc}
     */

//    public function blockForm($form, FormStateInterface $form_state) {
//
//        $configBlock = $this->getConfiguration();
//        $currencies = $this->config->get('lesson3.currencies');
//
////        if (!isset($currencies) || empty($currencies)) {
////            $currencies = $this->currenciesService->getCurrencies();
////        }
//
//        $form = parent::blockForm($form, $form_state);
//
//        foreach ($currencies as $currency) {
//            $arrayCharCode = $configBlock['check' . $currency['CharCode']];
//            $form['check' . $currency['CharCode']] = array(
//                '#type' => 'checkbox',
//                '#title' => $currency['Name'],
//                '#default_value' => $arrayCharCode['Checked'],
//            );
//        }
//
//        return $form;
//    }
//
//    /**
//     * {@inheritdoc}
//     */
//    public function blockSubmit($form, FormStateInterface $form_state) {
//        $currencies = $this->config->get('lesson3.currencies');
//
////        if (!isset($currencies) || empty($currencies)) {
////            $currencies = $this->currenciesService->getCurrencies();
////        }
//
//        foreach ($currencies as $currency) {
//            $this->setConfigurationValue('check' . $currency['CharCode'], array(
//                'Checked' => $form_state->getValue('check' . $currency['CharCode']),
//                'Name' => $currency['Name'],
//                'Rate' => $currency['Rate'],
//            ));
//        }
//    }
}