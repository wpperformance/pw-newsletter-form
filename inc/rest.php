<?php

/** Ajax Rest response */

namespace PWNewsletterForm;

require_once dirname(__FILE__) . '/api.php';
require_once dirname(__FILE__) . '/token.php';

use WP_Error;
use WP_REST_Request;

/**
 * Validation the email if it exist in contact list
 *
 * @param  string  $type - form type.
 * @param  string  $email - email.
 * @param  array  $list_id - list ids.
 * @return array
 */
function validation_email($res, $email, $list_id, $type = 'simple')
{

  $isDopted = false;

  $desired_lists = $list_id;

  // new user.
  if (isset($res['code']) && $res['code'] == 'document_not_found') {
    $ret = [
      'code' => 'new',
      'isDopted' => $isDopted,
      'listid' => $list_id,
    ];

    return $ret;
  }

  $listid = $res['listIds'];

  // update user when listid is empty.
  if (! isset($listid) || ! is_array($listid)) {
    $ret = [
      'code' => 'update',
      'isDopted' => $isDopted,
      'listid' => $list_id,
    ];

    return $ret;
  }

  $attrs = $res['attributes'];
  if (isset($attrs['DOUBLE_OPT-IN']) && $attrs['DOUBLE_OPT-IN'] == '1') {
    $isDopted = true;
  }

  $diff = array_diff($desired_lists, $listid);
  if (! empty($diff)) {
    $status = 'update';
  } else {
    if ($res['emailBlacklisted'] == '1') {
      $status = 'update';
    } else {
      $status = 'already_exist';
    }
  }

  $ret = [
    'code' => $status,
    'isDopted' => $isDopted,
    'listid' => $listid,
  ];

  return $ret;
}

/**
 * Signup process
 *
 * @param  string  $type - simple, confirm, double-optin / subscribe.
 * @param $email - subscriber email.
 * @param $list_id - desired list ids.
 * @param $info - user's attributes.
 * @return string
 */
function create_subscriber($email, $list_id, $info = [])
{
  $type = 'simple';
  $api = new ApiForm();
  $user = $api->getUser($email);

  $response = validation_email($user, $email, $list_id, $type);
  $exist = '';

  if ($response['code'] == 'already_exist') {
    $exist = 'already_exist';
  }

  $listid = $response['listid'];

  if ($api->getLastResponseCode() === ApiForm::RESPONSE_CODE_OK && isset($user['email'])) {
    unset($info['email']);
    $data = [
      'email' => $email,
      'attributes' => [],
      'emailBlacklisted' => false,
      'smsBlacklisted' => false,
      'listIds' => $listid,
      'updateEnabled' => true,
    ];

    $api->createUser($data);
    $exist = $api->getLastResponseCode() == 204 ? 'success' : '';
  } else {
    $data = [
      'email' => $email,
      'attributes' => [],
      'emailBlacklisted' => false,
      'smsBlacklisted' => false,
      'listIds' => $listid,
      'updateEnabled' => true,
    ];

    $created_user = $api->createUser($data);
  }

  if ($exist != '') {
    $response['code'] = $exist;
  } elseif (isset($created_user['id'])) {
    $response['code'] = 'success';
  }

  return $response['code'];
}

/**
 * REST route for form submission.
 */
function form_callback(WP_REST_Request $request): WP_Error|array
{

  $tokenReal = getToken();
  // get token from form
  $token = $request->get_param('token');
  // if robot, beer is not null
  $trap = $request->get_param('beer');
  if ($token !== $tokenReal || $trap !== null) {
    return new WP_Error('invalid_token', 'Invalid token', ['status' => 403]);
  }

  $email = sanitize_email($request->get_param('email'));
  $list_id = get_option(Manager::LIST_NAME);

  $res = create_subscriber($email, [$list_id]);

  if ($res == 'success') {
    // ok :)
    return [
      'data' => [
        'status' => 200,
        'error' => false,
      ],
      'code' => 'EMAIL_SAVED',
    ];
  }

  return [
    'message' => 'ERROR',
    'data' => [
      'status' => 200,
      'error' => true,
    ],
    'code' => 'ERROR',
  ];
}

function get_token(WP_REST_Request $request)
{
  $tokenReal = getToken();

  return ['token' => $tokenReal];
}
