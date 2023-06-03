

/**
 * Handle forms
 */
export default class Form {

    static init() {
        Form.validation();
        Form.togglePassword();
    }

    /**
     * Set custom validation on form with data-validation attribute
     * If attribute has value onInit, the form is validate on page load
     */
    static validation() {
        const formsToValidate = document.querySelectorAll('[data-validation]');

        if (formsToValidate.length > 0) {
            formsToValidate.forEach(form => {
                // validate on page load
                if (form.dataset.validation === 'onInit') {
                    Form.validateForm(form);
                }

                // set listeners on required inputs
                let requiredControls = form.querySelectorAll('[data-required]');
                requiredControls.forEach(control => {
                    Form.listenControl(control);
                });

                // add listener on submit
                form.addEventListener('submit', function (e) {
                    e.preventDefault();
                    Form.validateForm(form);
                    // based on errors submit form
                    const errors = document.querySelectorAll('.form-container--error');
                    if (errors.length === 0) {
                        form.submit();
                    }
                });
            });
        }
    }

    /**
     * Validate form (check required controls)
     * @param form
     */
    static validateForm(form) {
        let requiredControls = form.querySelectorAll('[data-required]');
        if (requiredControls.length > 0) {
            requiredControls.forEach(control => {
                Form.validateControl(control);
            });
        }
        const generalErr = document.querySelector('.form-login-general-err');
        if (generalErr) {
            generalErr.classList.remove('form-container--error');
        }
    }

    /**
     * E-mail validation
     * @param email
     * @returns {boolean}
     */
    static validateEmail(email) {
        const re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return re.test(email);
    }

    /**
     * Main validation logic
     * @param control
     */
    static validateControl(control) {
        let parent = control.closest('.form-container');

        // determinate control type
        let isEmail = control.dataset.required === 'email';
        let isCheckbox = control.type === 'checkbox';

        // checkbox
        if (isCheckbox) {
            if (control.checked) {
                parent.classList.remove('form-container--error');
                parent.classList.add('form-container--success');
            } else {
                parent.classList.add('form-container--error');
                parent.classList.remove('form-container--success');
            }
            // text, textarea
        } else {
            if (control.value === '') {
                parent.classList.add('form-container--error');
                parent.classList.remove('form-container--success');
            } else {
                // specific type
                if (isEmail) {
                    if (this.validateEmail(control.value)) {
                        parent.classList.remove('form-container--error');
                        parent.classList.add('form-container--success');
                    } else {
                        parent.classList.add('form-container--error');
                        parent.classList.remove('form-container--success');
                    }
                } else {
                    const regex = control.getAttribute('pattern');
                    const valid = regex ? new RegExp(regex).test(control.value) : true;

                    if (valid) {
                        parent.classList.remove('form-container--error');
                        parent.classList.add('form-container--success');
                    } else {
                        parent.classList.add('form-container--error');
                        parent.classList.remove('form-container--success');
                    }
                }
            }
        }
    }

    /**
     * Set listeners on required controls
     * @param control
     */
    static listenControl(control) {
        control.addEventListener('input', (e) => {
            Form.validateControl(control);
        });
    }

    static togglePassword() {
        const toggleList = document.querySelectorAll('.js-toggle-password');

        if (toggleList.length > 0) {
            toggleList.forEach(toggle => {
                toggle.addEventListener('click', () => {
                    const input = toggle.closest('.form-container').querySelector('input');
                    const isVisible = input.type === 'text';

                    if (!isVisible) {
                        input.type = 'text';
                        toggle.classList.add('visible');
                    } else {
                        input.type = 'password';
                        toggle.classList.remove('visible');
                    }
                })
            })
        }
    }
}
