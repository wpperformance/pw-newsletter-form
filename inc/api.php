<?php

/** Connect to API */

namespace PWNewsletterForm;

require_once dirname(__FILE__) . '/manager.php';

class ApiForm
{
  const API_BASE_URL = 'https://api.brevo.com/v3';

  const HTTP_METHOD_GET = 'GET';

  const HTTP_METHOD_POST = 'POST';

  const HTTP_METHOD_PUT = 'PUT';

  const HTTP_METHOD_DELETE = 'DELETE';

  const CAMPAIGN_TYPE_EMAIL = 'email';

  const CAMPAIGN_TYPE_SMS = 'sms';

  const RESPONSE_CODE_OK = 200;

  const RESPONSE_CODE_CREATED = 201;

  const RESPONSE_CODE_ACCEPTED = 202;

  const RESPONSE_CODE_UNAUTHORIZED = 401;

  const PLUGIN_VERSION = '0.0.1';

  const USER_AGENT = 'private_plugin/wordpress';

  private $apiKey;

  private $lastResponseCode;

  /**
   * SendinblueApiClient constructor.
   */
  public function __construct()
  {
    $this->apiKey = Manager::decode_field(get_option(Manager::API_KEY_V3_OPTION_NAME));
  }

  /**
   * @return mixed
   */
  public function sendEmail($data)
  {
    return $this->post('/smtp/email', $data);
  }

  /**
   * @return mixed
   */
  public function getUser($email)
  {
    return $this->get('/contacts/' . urlencode($email));
  }

  /**
   * @return mixed
   */
  public function createUser($data)
  {
    return $this->post('/contacts', $data);
  }

  /**
   * @param $email ,$data
   * @return mixed
   */
  public function updateUser($email, $data)
  {
    return $this->put('/contacts/' . $email, $data);
  }

  /**
   * @return mixed
   */
  public function getLists($data)
  {
    return $this->get('/contacts/lists', $data);
  }

  /**
   * @param $data
   * @return mixed
   */
  public function getAllLists()
  {
    $lists = ['lists' => [], 'count' => 0];
    $offset = 0;
    $limit = 50;
    do {
      $list_data = $this->getLists(['limit' => $limit, 'offset' => $offset]);
      if (isset($list_data['lists']) && is_array($list_data['lists'])) {
        $lists['lists'] = array_merge($lists['lists'], $list_data['lists']);
        $offset += 50;
        $lists['count'] = $list_data['count'];
      }
    } while (! empty($lists['lists']) && count($lists['lists']) < $list_data['count']);

    return $lists;
  }

  /**
   * @param  array  $parameters
   * @return mixed
   */
  public function get($endpoint, $parameters = [])
  {
    if ($parameters) {
      foreach ($parameters as $key => $parameter) {
        if (is_bool($parameter)) {
          // http_build_query converts bool to int
          $parameters[$key] = $parameter ? 'true' : 'false';
        }
      }
      $endpoint .= '?' . http_build_query($parameters);
    }

    return $this->makeHttpRequest(self::HTTP_METHOD_GET, $endpoint);
  }

  /**
   * @param  array  $data
   * @return mixed
   */
  public function post($endpoint, $data = [])
  {
    return $this->makeHttpRequest(self::HTTP_METHOD_POST, $endpoint, $data);
  }

  /**
   * @param  array  $data
   * @return mixed
   */
  public function put($endpoint, $data = [])
  {
    return $this->makeHttpRequest(self::HTTP_METHOD_PUT, $endpoint, $data);
  }

  /**
   * @param  array  $body
   * @return mixed
   */
  private function makeHttpRequest($method, $endpoint, $body = [])
  {
    $url = self::API_BASE_URL . $endpoint;

    $args = [
      'timeout' => 10000,
      'method' => $method,
      'headers' => [
        'api-key' => $this->apiKey,
        'Content-Type' => 'application/json',
        'User-Agent' => self::USER_AGENT,
      ],
    ];

    if ($method != self::HTTP_METHOD_GET && $method != self::HTTP_METHOD_DELETE) {
      if (isset($body['listIds'])) {
        $body['listIds'] = $this->getListsIds($body['listIds']);
      }
      if (isset($body['unlinkListIds'])) {
        $body['unlinkListIds'] = $this->getListsIds($body['unlinkListIds']);
      }
      if (is_array($body)) {
        foreach ($body as $key => $val) {
          if (empty($val) && $val !== false && $val !== 0) {
            unset($body[$key]);
          }
        }
      }
      $args['body'] = wp_json_encode($body);
    }

    $response = wp_remote_request($url, $args);
    $this->lastResponseCode = wp_remote_retrieve_response_code($response);

    if (is_wp_error($response)) {
      $data = [
        'code' => $response->get_error_code(),
        'message' => $response->get_error_message(),
      ];
    } else {
      $data = json_decode(wp_remote_retrieve_body($response), true);
    }

    return $data;
  }

  private function getListsIds($listIds)
  {
    return array_unique(array_values(array_map('intval', (array) $listIds)));
  }

  /**
   * @return int
   */
  public function getLastResponseCode()
  {
    return $this->lastResponseCode;
  }
}
