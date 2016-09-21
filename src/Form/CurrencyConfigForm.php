<?php
/**
 * @file
 * Contains \Drupal\lesson3\Form\CurrencyConfigForm.
 */

namespace Drupal\lesson3\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Component\Utility\Bytes;

/**
 * Form for configuration lesson3 module.
 */
class CurrencyConfigForm extends ConfigFormBase {
  /**
   * The file storage service.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $fileStorage;

  /**
   * Constructs a form object for image dialog.
   *
   * @param \Drupal\Core\Entity\EntityStorageInterface $file_storage
   *   The file storage service.
   */
  public function __construct(EntityStorageInterface $file_storage) {
    $this->fileStorage = $file_storage;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity.manager')->getStorage('file')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'currency_config_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'lesson3.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $max_filesize = Bytes::toInt('1MB');

    $form['fid'] = array(
      '#type' => 'managed_file',
      '#title' => $this->t('Upload file'),
      '#upload_location' => 'public://',
      '#upload_validators' => array(
        'file_validate_extensions' => array('gif png jpg jpeg txt pdf'),
        'file_validate_size' => array($max_filesize),
      ),
    );
    $form['upload'] = array(
      '#type' => 'submit',
      '#value' => t('upload'),
      '#submit' => array('::handleUploadFile'),
    );
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);
  }

  /**
   * Submit handler to upload file.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public function handleUploadFile(array &$form, FormStateInterface $form_state) {

    $fid = $form_state->getValue(array('fid', 0));
    if (!empty($fid)) {
      $file = $this->fileStorage->load($fid);
      $file->status = FILE_STATUS_PERMANENT;
      //Applying patch Gets the transliterated filename https://www.drupal.org/files/issues/drupal-use_new_transliteration-2492171-28.patch
      $file->getTransliteratedFilename();
      // Save.
      $file->save();
    }
  }
}