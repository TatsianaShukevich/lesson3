<?php

namespace Drupal\lesson3\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class CurrenciesForm.
 *
 * @package Drupal\lesson3\Form
 */
class CurrenciesForm extends EntityForm {
    /**
     * {@inheritdoc}
     */
    public function form(array $form, FormStateInterface $form_state) {
        $form = parent::form($form, $form_state);

        $currencies = $this->entity;
        $form['label'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Currency name'),
            '#maxlength' => 255,
            '#default_value' => $currencies->label(),
            '#description' => $this->t("Currency name."),
        ];

        $form['id'] = [
            '#type' => 'machine_name',
            '#default_value' => $currencies->id(),
            '#machine_name' => [
                'exists' => '\Drupal\lesson3\Entity\Currencies::load',
            ],
            '#disabled' => !$currencies->isNew(),
        ];

        $form['code'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Currency code'),
            '#maxlength' => 255,
            '#description' => $this->t("Currency code."),
            '#default_value' => $currencies->code(),

        ];

        $form['in_block'] = [
            '#type' => 'checkbox',
            '#title' => $this->t('Display in block'),
            '#default_value' => $currencies->in_block(),
        ];

        $form['on_page'] = [
            '#type' => 'checkbox',
            '#title' => $this->t('Display on currencies page'),
            '#default_value' => $currencies->on_page(),
        ];

        /* You will need additional form elements for your custom properties. */

        return $form;
    }

    /**
     * {@inheritdoc}
     */
    public function save(array $form, FormStateInterface $form_state) {
        $currencies = $this->entity;
        $status = $currencies->save();

        switch ($status) {
            case SAVED_NEW:
                drupal_set_message($this->t('Created the %label Currencies.', [
                    '%label' => $currencies->label(),
                ]));
                break;

            default:
                drupal_set_message($this->t('Saved the %label Currencies.', [
                    '%label' => $currencies->label(),
                ]));
        }
        $form_state->setRedirectUrl($currencies->urlInfo('collection'));
    }

}
