import { getContext, store, withScope } from '@wordpress/interactivity';

interface ContextType {
  error_message: string;
  success_message: string;
  error_status_message: string;
}


/**
 * check email
 * @param {string} text
 * @returns boolean
 */
const isEmail = (text) => {
  const re =
    /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
  return re.test(text);
};


const { state, actions } = store('pw-newsletter-form', {
  state: {
    token: '',
    submitted: false,
    message: '',
    showMessage: false,
  },
  actions: {
    hideMessage: () => {
      setTimeout(() => {
        state.showMessage = false;
      }, 5000);
    },
    getToken: () => {
      fetch("/wp-json/pw_newsletter_form/getToken")
        .then((res) => res.json())
        .then((res) => {
          state.token = res.token;
        }).catch((err) => {
          console.error('Error fetching token:', err);
        });
    },
    submitForm: (event) => {
      const ctx = getContext() as ContextType;
      event.preventDefault();
      const form = event.target;
      const formData = new FormData(form);

      if (form.email.value !== "" && isEmail(form.email.value)) {
        state.submitted = true;

        fetch("/wp-json/pw_newsletter_form/action", {
          method: 'POST',
          body: formData,
        })
          .then((res) => res.json())
          .then((res) => {
            const { data } = res;

            // error before action
            if (data.status !== 200) {
              state.message = ctx.error_status_message || res.message || 'An error occurred';
              state.showMessage = true;
              state.submitted = false;
              actions.hideMessage();
              return;
            }

            // error from brevo
            if (data.error) {
              state.message = ctx.error_message || res.error;
              state.showMessage = true;
              state.submitted = false;
              actions.hideMessage();
              return;
            }

            // success
            state.message = ctx.success_message || res.message || 'Success!';
            state.showMessage = true;
            form.reset();
            state.submitted = false;
            actions.hideMessage();

          }).catch((err) => {
            console.error('Error submitting form:', err);
          });
      }
    },
  }
});