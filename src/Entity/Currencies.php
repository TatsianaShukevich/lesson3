<?php

namespace Drupal\lesson3\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;

/**
 * Defines the Currencies entity.
 *
 * @ConfigEntityType(
 *   id = "currencies",
 *   label = @Translation("Currencies"),
 *   handlers = {
 *     "list_builder" = "Drupal\lesson3\CurrenciesListBuilder",
 *     "form" = {
 *       "add" = "Drupal\lesson3\Form\CurrenciesForm",
 *       "edit" = "Drupal\lesson3\Form\CurrenciesForm",
 *       "delete" = "Drupal\lesson3\Form\CurrenciesDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\lesson3\CurrenciesHtmlRouteProvider",
 *     },
 *   },
 *   config_prefix = "currencies",
 *   admin_permission = "administer site configuration",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid",
 *     "currency" = "currency",
 *     "code" = "code",
 *     "in_block" = "in_block",
 *     "on_page" = "on_page",
 *     
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/currencies/{currencies}",
 *     "add-form" = "/admin/structure/currencies/add",
 *     "edit-form" = "/admin/structure/currencies/{currencies}/edit",
 *     "delete-form" = "/admin/structure/currencies/{currencies}/delete",
 *     "collection" = "/admin/structure/currencies"
 *   }
 * )
 */
class Currencies extends ConfigEntityBase implements CurrenciesInterface {

    /**
     * The Currencies ID.
     *
     * @var string
     */
    protected $id;

    /**
     * The Currencies label.
     *
     * @var string
     */
    protected $label;

    /**
     * The Currencies name.
     *
     * @var string
     */
    protected $currency;

    /**
     * The Currencies CharCode.
     *
     * @var string
     */
    protected $code;

    /**
     * Option to display a currency in block.
     *
     * @var boolean
     */
    protected $in_block;

    /**
     * Option to display a currency on currencies page.
     *
     * @var boolean
     */
    protected $on_page;

    /**
     * {@inheritdoc}
     */
    public function currency($currency = NULL) {
        if ($currency) {
            $this->currency = $currency;
        }
        return $this->currency;
    }

    /**
     * {@inheritdoc}
     */
    public function code($code = NULL) {
        if ($code) {
            $this->code = $code;
        }
        return $this->code;
    }

    /**
     * {@inheritdoc}
     */
    public function in_block($in_block = NULL) {
        if ($in_block) {
            $this->in_block = $in_block;
        }
        return $this->in_block;
    }

    /**
     * {@inheritdoc}
     */
    public function on_page($on_page = NULL) {
        if ($on_page) {
            $this->on_page = $on_page;
        }
        return $this->on_page;
    }

    /**
     * Checks should a currency display on page or/and in block.
     *
     * @param string $displayMode
     *  Two options are available 'in_block' or 'on_page'
     *
     * @return string|null
     */
    public function checkDisplayMode($displayMode) {
        switch ($displayMode) {
            case 'on_page':
                return $this->on_page();
                break;
            case 'in_block':
                return $this->in_block();
                break;
            default:
                return NULL;
        }
        
    }
}
