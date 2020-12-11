<?php

namespace Drupal\yuraul\Entity;

use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityPublishedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\File\FileSystemInterface;
use Drupal\file\Entity\File;

/**
 * Defines the Feedback entity.
 *
 * @ingroup yuraul
 *
 * @ContentEntityType(
 *   id = "feedback_entity",
 *   label = @Translation("Feedback"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\yuraul\FeedbackEntityListBuilder",
 *     "views_data" = "Drupal\yuraul\Entity\FeedbackEntityViewsData",
 *
 *     "form" = {
 *       "default" = "Drupal\yuraul\Form\FeedbackEntityForm",
 *       "add" = "Drupal\yuraul\Form\FeedbackEntityAddForm",
 *       "edit" = "Drupal\yuraul\Form\FeedbackEntityForm",
 *       "delete" = "Drupal\yuraul\Form\FeedbackEntityDeleteForm",
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\yuraul\FeedbackEntityHtmlRouteProvider",
 *     },
 *     "access" = "Drupal\yuraul\FeedbackEntityAccessControlHandler",
 *   },
 *   base_table = "feedback_entity",
 *   translatable = FALSE,
 *   admin_permission = "administer feedback entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "langcode" = "langcode",
 *     "published" = "status",
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/feedback_entity/{feedback_entity}",
 *     "add-form" = "/admin/structure/feedback_entity/add",
 *     "edit-form" = "/admin/structure/feedback_entity/{feedback_entity}/edit",
 *     "delete-form" = "/admin/structure/feedback_entity/{feedback_entity}/delete",
 *     "collection" = "/admin/structure/feedback_entity",
 *   },
 *   field_ui_base_route = "feedback_entity.settings"
 * )
 */
class FeedbackEntity extends ContentEntityBase implements FeedbackEntityInterface {

  use EntityChangedTrait;
  use EntityPublishedTrait;

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
  public function getEmail() {
    return $this->get('email')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setEmail($email) {
    $this->set('email', $email);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getPhone() {
    return $this->get('phone')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setPhone($phone) {
    $this->set('phone', $phone);
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

  private static function defaultImage() {
    $source = \drupal_get_path('module', 'yuraul') . '/images/default.png';
    $destination = 'public://yuraul/avatars';
    \Drupal::service('file_system')
      ->prepareDirectory($destination, FileSystemInterface::CREATE_DIRECTORY);
    $destination = \Drupal::service('file_system')
      ->copy($source, $destination, FileSystemInterface::EXISTS_REPLACE);
    $filename = \Drupal::service('file_system')->basename($destination);
    // Create file entity.
    $image = File::create();
    $image->setFileUri($destination);
    $image->setOwnerId(\Drupal::currentUser()->id());
    $image->setMimeType('image/' . pathinfo($destination, PATHINFO_EXTENSION));
    $image->setFileName($filename);
    $image->setPermanent();
    $image->save();
    \Drupal::service('file.usage')
      ->add($image, 'test', 'entity', $image->id());
    return $image->uuid();
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    // Add the published field.
    $fields += static::publishedBaseFieldDefinitions($entity_type);

    $fields['name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Name'))
      ->setDescription(t('The name of the user who leaved a message.'))
      ->setSettings([
        'max_length' => 100,
        'case_sensitive' => TRUE,
        'is_acsii' => TRUE,
      ])
      ->setDisplayOptions('view', [
        'label' => '',
        'type' => 'string',
        'weight' => -10,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -10,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(TRUE)
      ->addConstraint('ValidName');

    $fields['email'] = BaseFieldDefinition::create('email')
      ->setLabel(t('Email'))
      ->setDescription(t('The email address of the user.'))
      ->setDisplayOptions('view', [
        'label' => '',
        'type' => 'string',
        'weight' => -9,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -9,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(TRUE);

    $fields['phone'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Phone number'))
      ->setDescription(t('The phone number of the user.'))
      ->setSettings([
        'max_length' => 15,
        'is_acsii' => TRUE,
      ])
      ->setDisplayOptions('view', [
        'label' => '',
        'type' => 'string',
        'weight' => 1,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => 1,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(TRUE)
      ->addConstraint('ValidPhoneNumber');

    $fields['message'] = BaseFieldDefinition::create('string_long')
      ->setLabel(t('Message'))
      ->setDescription(t('The message user leaved.'))
      ->setSettings([
        'case_sensitive' => TRUE,
        'is_acsii' => FALSE,
      ])
      ->setDisplayOptions('view', [
        'label' => '',
        'type' => 'string',
        'weight' => 2,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textarea',
        'weight' => 2,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(TRUE);

    $fields['avatar'] = BaseFieldDefinition::create('image')
      ->setLabel(t('Avatar'))
      ->setDescription(t('Avatar of the user.'))
      ->setSettings([
        'file_extensions' => 'png jpg jpeg',
        'file_directory' => 'yuraul/avatars',
        'max_filesize' => '2 MB',
        'default_image' => [ // TODO: Add default image.
          'uuid' => self::defaultImage(),
          'alt' => 'Default user avatar',
          'title' => 'User avatar',
          'width' => NULL,
          'height' => NULL,
        ],
      ])
      ->setDisplayOptions('view', [
        'label' => '',
        'type' => 'image',
        'weight' => 3,
      ])
      ->setDisplayOptions('form', [
        'type' => 'image_image',
        'weight' => 3,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['picture'] = BaseFieldDefinition::create('image')
      ->setLabel(t('Picture'))
      ->setDescription(t('Picture added to the message.'))
      ->setSettings([
        'file_extensions' => 'png jpg jpeg',
        'file_directory' => 'yuraul/pictures',
        'max_filesize' => '5 MB',
      ])
      ->setDisplayOptions('view', [
        'label' => '',
        'type' => 'image',
        'weight' => 4,
      ])
      ->setDisplayOptions('form', [
        'type' => 'image_image',
        'weight' => 4,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['status']->setDescription(t('A boolean indicating whether the Test entity is published.'))
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
        'weight' => 10,
      ]);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entity was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the entity was last edited.'));

    return $fields;
  }

}
