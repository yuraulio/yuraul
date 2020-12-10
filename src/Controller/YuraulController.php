<?php

namespace Drupal\yuraul\Controller;

use Drupal\file\Entity\File;
use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;
use Drupal\yuraul\Utility\PostStorageTrait;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Drupal\Core\File\FileSystemInterface;
use Drupal\Core\Config\ConfigFactory;

/**
 * Constructs a guestbook page and admin panel.
 */
class YuraulController extends ControllerBase {

  use PostStorageTrait;

  /**
   * Return the module name.
   */
  protected function getModuleName() {
    return 'yuraul';
  }

  /**
   * Change avatar, post picture and timestamp values to eligible to render.
   *
   * If user did not add avatar set default picture instead.
   * Convert the ID of entity to the URL.
   * Convert timestamp to formatted date and time.
   *
   * @param array|bool $posts
   *   Array with posts or FALSE if there are no posts to display.
   *
   * @return array|bool
   *   Array with prepared posts or FALSE if there are not so.
   */
  protected function prepareForRender($posts) {
    if ($posts) {
      // Setting default user avatar if not exist.
      foreach ($posts as $post) {
        if ($post->avatar == '0') {
          $post->avatar = "sites/default/files/{$this->getModuleName()}/user/default.png";
        }
        else {
          // Converting avatar file ID to URL.
          $post->avatar = File::load($post->avatar)->createFileUrl();
        }

        // Converting post picture file ID to URL.
        if ($post->picture !== '0') {
          $post->picture = File::load($post->picture)->createFileUrl();
        }
        // And converting timestamp to human readable string.
        $post->timestamp = date('F/d/Y H:i:s', $post->timestamp);
      }
    }
    return $posts ?? FALSE;
  }

  /**
   * Build a render array with posts and template to render.
   *
   * @return array
   *   A render array.
   */
  public function feedback() {
    // Getting path to page template.
    $template = file_get_contents($this->getModulePath() . '/templates/feedback.html.twig');

    // Check if user has permissions to edit and add edit buttons if has.
    $permission = \Drupal::currentUser()->hasPermission('administer site configuration');

    // Add list of posts, template and permission value to array to be returned.
    $page['posts'] = [
      '#type' => 'inline_template',
      '#template' => $template,
      '#context' => [
        'posts' => $this->prepareForRender($this->getPosts()),
        'can_edit' => $permission,
      ],
      '#attached' => [
        'library' => [
          'yuraul/guestbook',
        ],
      ],
    ];

    return $page;
  }

  /**
   * Build a guestbook main page.
   *
   * Add a form to add new posts and list of existing feedbacks.
   *
   * @return mixed
   *   A render array.
   */
  public function show() {
    // Adding form for sending post to the page.
//    $page[] = ['form' => \Drupal::formBuilder()->getForm('Drupal\yuraul\Form\AddFeedback')];
//
//    // Add a view with the feedback posts list.
//    $page[] = [
//      'view' => [
//        '#type' => 'view',
//        '#name' => 'feedback',
//        '#display_id' => 'default',
//      ],
//    ];
//
//    $view = \Drupal\views\Views::getView('feedback');
//    $view->setDisplay('default');
//    $view->execute();
//
//    // Get the results of the view.
//    $view_result = $view->result;

    $files = \Drupal::entityQuery('file')->execute();
    // Delete if used by our module.
    foreach ($files as $fid) {
      $file = File::load($fid);
      $usage = \Drupal::service('file.usage')->listUsage($file);
      if (array_key_exists('yuraul', $usage)) {
        \Drupal::service('file.usage')->delete($file, 'yuraul0', 'file', $fid, 0);
        $file->delete();
      }
    }

    $source = $this->getModulePath() . '/images/default.png';
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
      ->add($image, $this->getModuleName(), 'file', $image->id());
    $config = \Drupal::service('config.factory')->getEditable('yuraul.settings');
    $config->set('default_avatar.uuid', $image->uuid())->save();

    $page = [
      '#type' => 'markup',
      '#markup' => \Drupal::config('yuraul.settings')->get('default_avatar.uuid'),
    ];
    return $page;
}

  /**
   * Biuld a page to edit a post.
   *
   * Just form filled with a post to edit data.
   *
   * @param string $postID
   *   ID of the post to edit.
   *
   * @return array|mixed
   *   A render array.
   */
  public function edit(string $postID) {
    // Check permissions.
    if (\Drupal::currentUser()
      ->hasPermission('administer site configuration')) {
      $post = $this->getPosts($postID);
      // If post with the ID exists, build a form with its data filled.
      if ($post) {
        return [
          'form' => \Drupal::formBuilder()->getForm(
            'Drupal\yuraul\Form\AddFeedback',
            $post),
        ];
      }
      // Setting the error message if post with post ID was not found.
      else {
        \Drupal::messenger()->addError($this
          ->t('Post @postID not found!',
          ['@postID' => $postID]));
        return $this->show();
      }
    }
    // If user has no permissions throw exeption.
    else {
      throw new AccessDeniedHttpException();
    }
  }

  /**
   * A plug gor admin page.
   *
   * Not implemented. Just a little bit of markup.
   *
   * @return array
   *   A render array.
   */
  public function admin() {
    return [
      '#type' => 'markup',
      '#markup' => t('<div style="color: red;">There should be configuretion page but it is nothing to configure.</div>'),
    ];
  }

}
