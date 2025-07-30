<?php
$ctx = [
  "success_message" =>  __('email_address_saved', 'pw-newsletter-form'),
  "error_message" =>  __('email_address_exist', 'pw-newsletter-form'),
  "error_status_message" =>  __('error_status', 'pw-newsletter-form'),
];

?>


<div class="pw-newsletter-form" data-wp-interactive="pw-newsletter-form" <?php echo wp_interactivity_data_wp_context($ctx); ?>>
  <form action=" #" id="pw-newsletter-form" data-wp-on--submit="actions.submitForm">
    <input type="hidden" name="token" data-wp-bind--value="state.token" />
    <fieldset class="pw-form-details">
      <label for="email"><span><?php _e('Email', 'pw-newsletter-form'); ?></span>
        <input type="email" name="email" id="email-newsletter-form" value="" placeholder="<?php _e('Email', 'pw-newsletter-form'); ?>" required data-wp-on-async--focus="actions.getToken" />
      </label>
    </fieldset>
    <fieldset>
      <label for=" beer" tabindex="-1" class="inline-field beer-field" aria-hidden="true"><?php _e("Don't check this box, it's for robot", 'pw-newsletter-form'); ?>
        <input type="checkbox" name="beer" id="beer" />
      </label>
      <div class="wp-block-buttons is-layout-flex wp-block-buttons-is-layout-flex" style="margin-top:0;margin-bottom:0">
        <div class=" wp-block-button" style="margin-top:0;margin-bottom:0">
          <button
            type="submit"
            data-wp-bind--disabled="state.submitted"
            class="wp-block-button__link has-primary-color has-white-background-color has-text-color has-background has-link-color has-small-font-size has-custom-font-size wp-element-button"
            id="pw-newsletter-form-submit"><span class="goodmotion-loader" data-wp-bind--hidden="!state.submitted">
              <?php include(dirname(__FILE__) . "/../../assets/loader.svg"); ?></span><span><?php _e('send_email_address', 'pw-newsletter-form'); ?></span></button>
        </div>
      </div>
    </fieldset>
    <div id="pw-newsletter-form-status" class="pw-newsletter-form-status" data-wp-bind--hidden="!state.showMessage">
      <span class="pw-message" data-wp-text="state.message"></span>
    </div>
  </form>
</div>