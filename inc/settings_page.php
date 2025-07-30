<?php

/** page admin option for this plugin  */

namespace PWNewsletterForm;

require_once dirname(__FILE__) . '/manager.php';
require_once dirname(__FILE__) . '/api.php';

class SettingsPage
{
  public function __construct()
  {
    // Register the settings page.
    add_action('admin_menu', [$this, 'register_settings']);

    // Register the sections.
    add_action('admin_init', [$this, 'register_sections']);

    // Register the fields.
    add_action('admin_init', [$this, 'register_fields']);
  }

  // Register settings.
  public function register_settings(): void
  {
    add_options_page(
      'Newsletter Form Settings', // The title of your settings
      // page.
      'Newsletter Form', // The name of the menu item.
      'manage_options', // The capability required for this menu to be displayed to the user.
      'pw-newsletter-form', // The slug name to refer to this menu by
      // (should be unique for this menu).
      [$this, 'render_settings_page'], // The callback function used to render the settings page.
    );
  }

  // Register sections.
  public function register_sections(): void
  {
    add_settings_section('pw-newsletter-form', '', [], 'pw-newsletter-form');
  }

  // Register fields.
  public function register_fields(): void
  {
    $api = new ApiForm;
    $lists = $api->getAllLists();
    if (array_key_exists('lists', $lists)) {
      $listOptions = [];
      foreach ($lists['lists'] as $list) {
        $listOptions[strval($list['id'])] = $list['name'];
      }
    }

    $fields = [
      Manager::API_KEY_V3_OPTION_NAME => [
        'section' => 'pw-newsletter-form',
        'label' => 'Brevo API Key',
        'description' => 'Enter your Brevo API key',
        'type' => 'text',
        'encode' => true,
      ],
      Manager::LIST_NAME => [
        'section' => 'pw-newsletter-form',
        'label' => 'List for new subscribers',
        'description' => 'Select the list to add new subscribers to',
        'type' => 'select',
        'options' => $listOptions,
      ],

    ];
    foreach ($fields as $id => $field) {
      $field['id'] = $id;
      add_settings_field(
        $id,
        $field['label'],
        [$this, 'render_field'],
        'pw-newsletter-form',
        $field['section'],
        $field
      );
      register_setting('pw-newsletter-form-group', $id, ['sanitize_callback' => function ($value) use ($field) {
        if ($field['encode'] === true) {
          return Manager::encode_field($value);
        }

        return $value;
      }]);
    }
  }

  // Render individual fields.
  public function render_field($field): void
  {
    $value = get_option($field['id']);
    if ($field['id'] === Manager::API_KEY_V3_OPTION_NAME) {
      $value = Manager::decode_field($value);
    }
    switch ($field['type']) {
      case 'textarea':
        echo "<textarea rows=\"7\" cols=\"40\" name='{$field['id']}' id='{$field['id']}'>{$value}</textarea>";
        break;

      case 'checkbox':
        echo "<input type='checkbox' name='{$field['id']}' id='{$field['id']}' " . ($value === '1' ? 'checked' : '') . ' />';
        break;

      case 'wysiwyg':
        wp_editor($value, $field['id']);
        break;

      case 'select':
        if (is_array($field['options']) && ! empty($field['options'])) {
          echo "<select name='{$field['id']}' id='{$field['id']}'>";
          foreach ($field['options'] as $key => $option) {
            echo "<option value='{$key}' " . ($value == $key ? 'selected' : '') . ">{$option}</option>";
          }
          echo '</select>';
        }
        break;

      default:
        echo "<input name='{$field['id']}' id='{$field['id']}' type='{$field['type']}' value='{$value}' style=\"min-width:300px;\" />";
        break;
    }

    if (isset($field['description'])) {
      echo "<p class='description'>{$field['description']}</p>";
    }
  }

  // Render the settings page.
  public function render_settings_page(): void
  {
    echo "<div class='wrap'>";
    echo '<h1>Newsletter Form Settings</h1>';
    settings_errors();
    echo "<form method='POST' action='options.php'>";
    settings_fields('pw-newsletter-form-group');
    do_settings_sections('pw-newsletter-form');
    submit_button();
    echo '</form>';
    echo '</div>';
  }
}
