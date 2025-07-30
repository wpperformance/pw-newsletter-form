<?php

/** generate token for front */

namespace PWNewsletterForm;

/**
 * get the name of the token
 */
function get_name_token(): string
{
  return 'pw-contact-token';
}

/**
 * generate a token
 */
function generate_token(): string
{
  return uniqid('pw-');
}

/**
 * generate, save and return a token
 */
function getToken()
{
  //Convert the binary data into hexadecimal representation.
  if (get_transient(get_name_token()) === false) {
    set_transient(get_name_token(), bin2hex(generate_token()), 60 * 60 * 24);
  }

  return get_transient(get_name_token());
}
