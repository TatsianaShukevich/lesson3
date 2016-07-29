<?php

namespace Drupal\lesson3\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\user\UserInterface;

/**
 * Defines the Currency rate entity.
 *
 * @ingroup lesson3
 *
 * @ContentEntityType(
 *   id = "currency_rate",
 *   label = @Translation("Currency rate"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\lesson3\CurrencyRateListBuilder",
 *     "views_data" = "Drupal\lesson3\Entity\CurrencyRateViewsData",
 *     "translation" = "Drupal\lesson3\CurrencyRateTranslationHandler",
 *
 *     "form" = {
 *       "default" = "Drupal\lesson3\Form\CurrencyRateForm",
 *       "add" = "Drupal\lesson3\Form\CurrencyRateForm",
 *       "edit" = "Drupal\lesson3\Form\CurrencyRateForm",
 *       "delete" = "Drupal\lesson3\Form\CurrencyRateDeleteForm",
 *     },
 *     "access" = "Drupal\lesson3\CurrencyRateAccessControlHandler",
 *     "route_provider" = {
 *       "html" = "Drupal\lesson3\CurrencyRateHtmlRouteProvider",
 *     },
 *   },
 *   base_table = "currency_rate",
 *   data_table = "currency_rate_field_data",
 *   translatable = TRUE,
  *   admin_permission = "administer currency rate entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "uid" = "user_id",
 *     "langcode" = "langcode",
 *     "status" = "status",
 *     "date" = "date",
 *     "currency" = "currency",
 *     "rate" = "rate",
 *     "diff_rate" = "diff_rate",
 *     "currency_settings_id" = "currency_settings_id",
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/currency-rate/currency_rate/{currency_rate}",
 *     "add-form" = "/admin/structure/currency-rate/currency_rate/add",
 *     "edit-form" = "/admin/structure/currency-rate/currency_rate/{currency_rate}/edit",
 *     "delete-form" = "/admin/structure/currency-rate/currency_rate/{currency_rate}/delete",
 *     "collection" = "/admin/structure/currency-rate/currency_rate",
 *   },
 *   field_ui_base_route = "currency_rate.settings"
 * )
 */
class CurrencyRate extends ContentEntityBase implements CurrencyRateInterface {

    use EntityChangedTrait;

    /**
    * {@inheritdoc}
    */
    public static function preCreate(EntityStorageInterface $storage_controller, array &$values) {
        parent::preCreate($storage_controller, $values);
        $values += array(
            'user_id' => \Drupal::currentUser()->id(),
        );
    }

    /**
    * {@inheritdoc}
    */
    public function getName() {
        return $this->get('name')->value;
    }

    /**
    * {@inheritdoc}
    */
    public function setName($name) {
        $this->set('name', $name);
        return $this;
    }

    /**
    * {@inheritdoc}
    */
    public function getCreatedTime() {
        return $this->get('created')->value;
    }

    /**
    * {@inheritdoc}
    */
    public function setCreatedTime($timestamp) {
        $this->set('created', $timestamp);
        return $this;
    }

    /**
    * {@inheritdoc}
    */
    public function getOwner() {
        return $this->get('user_id')->entity;
    }

    /**
    * {@inheritdoc}
    */
    public function getOwnerId() {
        return $this->get('user_id')->target_id;
    }

    /**
    * {@inheritdoc}
    */
    public function setOwnerId($uid) {
        $this->set('user_id', $uid);
        return $this;
    }

    /**
    * {@inheritdoc}
    */
    public function setOwner(UserInterface $account) {
        $this->set('user_id', $account->id());
        return $this;
    }

    /**
    * {@inheritdoc}
    */
    public function isPublished() {
        return (bool) $this->getEntityKey('status');
    }

    /**
    * {@inheritdoc}
    */
    public function setPublished($published) {
        $this->set('status', $published ? NODE_PUBLISHED : NODE_NOT_PUBLISHED);
        return $this;
    }

  /**
   * {@inheritdoc}
   */
    public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
        $fields = parent::baseFieldDefinitions($entity_type);

        $fields['name'] = BaseFieldDefinition::create('string')
            ->setLabel(t('Name'))
            ->setDescription(t('The name of the Currency rate entity.'))
            ->setSettings(array(
                'max_length' => 50,
                'text_processing' => 0,
            ))
            ->setDefaultValue('')
            ->setDisplayOptions('view', array(
                'label' => 'above',
                'type' => 'string',
                'weight' => 1,
            ))
            ->setDisplayOptions('form', array(
                'type' => 'string_textfield',
                'weight' => 1,
            ))
            ->setDisplayConfigurable('form', TRUE)
            ->setDisplayConfigurable('view', TRUE);

        $fields['date'] = BaseFieldDefinition::create('string')
            ->setLabel(t('Date'))
            ->setDescription(t('Date'))
            ->setSettings(array(
                'default_value' => '',
                'max_length' => 255,
                'text_processing' => 0,
            ))
            ->setDisplayOptions('view', array(
                'label' => 'above',
                'type' => 'string',
                'weight' => 2,
            ))
            ->setDisplayOptions('form', array(
                'type' => 'string_textfield',
                'weight' => 2,
            ))
            ->setDisplayConfigurable('form', TRUE)
            ->setDisplayConfigurable('view', TRUE);

        $fields['currency'] = BaseFieldDefinition::create('string')
            ->setLabel(t('Currency'))
            ->setDescription(t('The currency name.'))
            ->setSettings(array(
                'default_value' => '',
                'max_length' => 255,
                'text_processing' => 0,
            ))
            ->setDisplayOptions('view', array(
                'label' => 'above',
                'type' => 'string',
                'weight' => 3,
            ))
            ->setDisplayOptions('form', array(
                'type' => 'string_textfield',
                'weight' => 3,
            ))
            ->setDisplayConfigurable('form', TRUE)
            ->setDisplayConfigurable('view', TRUE);

        $fields['rate'] = BaseFieldDefinition::create('float')
            ->setLabel(t('Rate'))
            ->setDescription(t('Rate.'))
            ->setDisplayOptions('view', array(
                'label' => 'above',
                'type' => 'string',
                'weight' => 4,
            ))
            ->setDisplayOptions('form', array(
                'type' => 'string_textfield',
                'weight' => 4,
            ))
            ->setDisplayConfigurable('form', TRUE)
            ->setDisplayConfigurable('view', TRUE);

        $fields['diff_rate'] = BaseFieldDefinition::create('float')
            ->setLabel(t('Rates difference'))
            ->setDescription(t('Difference with the previous day.'))
            ->setDefaultValue(0)
            ->setDisplayOptions('view', array(
                'label' => 'above',
                'type' => 'string',
                'weight' => 5,
            ))
            ->setDisplayOptions('form', array(
                'type' => 'string_textfield',
                'weight' => 5,
            ));

        $fields['currency_settings_id'] = BaseFieldDefinition::create('entity_reference')
            ->setLabel(t('Currency settings'))
            ->setDescription(t('The Currency settings.'))
            ->setSetting('target_type', 'currencies')
            ->setSetting('handler', 'default')
            ->setDisplayOptions('form', array(
                'type' => 'entity_reference_autocomplete',
                'weight' => 6,
                'settings' => array(
                    'match_operator' => 'CONTAINS',
                    'size' => '60',
                    'autocomplete_type' => 'tags',
                    'placeholder' => '',
                ),
            ))
            ->setDisplayConfigurable('form', TRUE)
            ->setDisplayConfigurable('view', TRUE);

        $fields['timestamp_date'] = BaseFieldDefinition::create('timestamp')
            ->setDefaultValue(REQUEST_TIME);

        $fields['status'] = BaseFieldDefinition::create('boolean')
            ->setLabel(t('Publishing status'))
            ->setDescription(t('A boolean indicating whether the Currency rate is published.'))
            ->setDefaultValue(TRUE);

        $fields['created'] = BaseFieldDefinition::create('created')
            ->setLabel(t('Created'))
            ->setDescription(t('The time that the entity was created.'));

        $fields['changed'] = BaseFieldDefinition::create('changed')
            ->setLabel(t('Changed'))
            ->setDescription(t('The time that the entity was last edited.'));
        
        return $fields;
    }

}
