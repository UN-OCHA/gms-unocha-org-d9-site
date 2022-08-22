<?php

namespace Drupal\gms_secure_role\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Controller routines for popup_after_login routes.
 */
class AssignRoleController extends ControllerBase {

  /**
   * Custom approve function.
   */
  public function approve($id) {
    $message_by_id = \Drupal::entityTypeManager()
      ->getStorage('contact_message')
      ->load($id);
    $email = $message_by_id->get('mail')->getValue()[0]['value'];
    $username = $message_by_id->get('name')->getValue()[0]['value'];
    $user = user_load_by_mail($email);
    $user_role = $user->get('roles')->getValue()[0]['target_id'];
    $body = 'Dear ' . $username . ', <br></br> We have approved your contact request. <br></br> Thanks & Regards, <br><br> GMS Team';
    if ($user_role == 'non_verified') {
      $user->addRole('gms_secure');
      $user->removeRole('non_verified');
      $user->save();
      /*Mail Functionality*/
      $mailManager = \Drupal::service('plugin.manager.mail');
      $module = 'gms_secure_role';
      $key = 'request_form';
      $to = $email;
      $params['message'] = $body;
      $langcode = \Drupal::currentUser()->getPreferredLangcode();
      $send = TRUE;

      $result = $mailManager->mail($module, $key, $to, $langcode, $params, NULL, $send);
      if ($result['result'] !== TRUE) {
        $this->messenger()
          ->addMessage($this->t('There was a problem sending your message and it was not sent.'), 'error');
      }
      else {
        $this->messenger()
          ->addMessage($this->t('Your message has been sent, The user role is changed'), 'success');
      }
      /*End*/
    }
    $dest_url = Url::fromUri('internal:/request_form_data')->toString();
    return new RedirectResponse($dest_url);
  }

  /**
   * Custom approve function.
   */
  public function reject($id) {
    $message_by_id = \Drupal::entityTypeManager()
      ->getStorage('contact_message')
      ->load($id);
    $email = $message_by_id->get('mail')->getValue()[0]['value'];
    $username = $message_by_id->get('name')->getValue()[0]['value'];
    $user = user_load_by_mail($email);
    $user_status = $user->get('status')->getValue()[0]['value'];
    $body = 'Dear ' . $username . ', <br></br> We have reject your contact request. <br></br> Thanks & Regards, <br><br> GMS Team';
    if ($user_status == '1') {
      $user->block();
      $user->save();
      /*Mail Functionality*/
      $mailManager = \Drupal::service('plugin.manager.mail');
      $module = 'gms_secure_role';
      $key = 'request_form';
      $to = $email;
      $params['message'] = $body;
      $langcode = \Drupal::currentUser()->getPreferredLangcode();
      $send = TRUE;

      $result = $mailManager->mail($module, $key, $to, $langcode, $params, NULL, $send);
      if ($result['result'] !== TRUE) {
        $this->messenger()
          ->addMessage($this->t('There was a problem sending your message and it was not sent.'), 'error');
      }
      else {
        $this->messenger()
          ->addMessage($this->t('Your message has been sent, The user has been blocked.'), 'success');
      }
      /*End*/
    }
    $dest_url = Url::fromUri('internal:/request_form_data')->toString();
    return new RedirectResponse($dest_url);
  }

}
