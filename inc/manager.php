<?php

namespace PWNewsletterForm;

class Manager
{
  const API_KEY_V3_OPTION_NAME = 'pw_newsletter_form_api_key_v3';

  const LIST_NAME = 'pw_newsletter_form_list_name';

  const SALT = 'pw_newsletter_form_salt';

  const BIT = 'e77a03f8932c259e45e894a2123df954';

  public static function encode_field($value)
  {
    return openssl_encrypt(
      $value,
      'aes-256-cbc',
      Manager::SALT,
      0,
      hex2bin(Manager::BIT)
    );
  }

  public static function decode_field($value)
  {
    return openssl_decrypt(
      $value,
      'aes-256-cbc',
      Manager::SALT,
      0,
      hex2bin(Manager::BIT)
    );
  }
}
