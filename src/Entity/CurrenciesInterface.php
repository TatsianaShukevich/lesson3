<?php

namespace Drupal\lesson3\Entity;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

/**
 * Provides an interface for defining Currencies entities.
 */
interface CurrenciesInterface extends ConfigEntityInterface {

     /**
     * Gets/Sets the currency field of the entity.
     *
     * @param string|null $currency
     *   The value of currency field
     * 
     * @return string|null
     *   The currency field of the entity, or NULL if there is no currency field defined.
     */
    public function currency($currency = NULL);

    /**
     * Gets/Sets the code field of the entity.
     *
     * @param string|null $code
     *   The value of code field
     * 
     * @return string|null
     *   The code field of the entity, or NULL if there is no code field defined.
     */
    public function code($code = NULL);

    /**
     * Gets/Sets the in_block field of the entity.
     * 
     * @param string|null $in_block
     *   The value of in_block field
     * 
     * @return string|null
     *   The in_block field of the entity, or NULL if there is no in_block field defined.
     */
    public function in_block($in_block = NULL);

    /**
     * Gets/Sets the on_page field of the entity.
     * 
     * @param string|null $on_page
     *   The value of on_page field
     *
     * @return string|null
     *   The on_page field of the entity, or NULL if there is no on_page field defined.
     */
    public function on_page($on_page = NULL);
}
