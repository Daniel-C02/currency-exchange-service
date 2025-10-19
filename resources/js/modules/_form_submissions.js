/*
 * Global Form Submission Handler
 */
import { fireSweetalert } from "../plugins/_sweetalert.js"; // Ensure this path is correct

export const init = () => {
    // Select all forms intended for AJAX submission
    const submissionForms = document.querySelectorAll('form.submission_form');
    if (submissionForms.length === 0) {
        return; // No forms to initialize
    }

    submissionForms.forEach((form) => {
        const $form = $(form); // jQuery instance of the form
        const formElements = []; // To store form field info

        // Find all input and textarea elements within this specific form
        form.querySelectorAll('input, textarea').forEach((el) => {
            const inputElName = el.name;
            // Only process elements with a name attribute
            if (!inputElName) return;

            // Find the corresponding error element (assuming ID format like 'name_error')
            const escapedName = inputElName.replace(/\[/g, '\\[').replace(/\]/g, '\\]');
            const errorEl = form.querySelector(`#${escapedName}_error`);

            formElements.push({
                name: inputElName,
                el: el, // The input/textarea element itself
                errEl: errorEl ? $(errorEl) : null // jQuery instance of the error element if found
            });

            // Clear error on input
            if (errorEl) {
                $(el).on('input', () => {
                    $(errorEl).removeClass('show');
                });
            }
        });

        // Attach the submit event listener to the form
        $form.on("submit", function (e) {
            e.preventDefault(); // Prevent traditional form submission

            const currentForm = this; // Native form element
            const $currentForm = $(this); // jQuery form instance
            const postTo = currentForm.dataset.postTo; // Get submission URL from data attribute
            const needsReset = currentForm.dataset.reset ? currentForm.dataset.reset === 'true' : true;

            // exit if the element that triggered form submission was not a submit button
            if ($(document.activeElement).attr('type') !== 'submit') return;

            // Find the submit button that triggered the event (or the first submit button)
            let $submitButton = $(document.activeElement).is(':submit')
                ? $(document.activeElement)
                : $currentForm.find('button[type="submit"]').first();

            if (!$submitButton.length || !postTo) {
                console.error("Form submission failed: No submit button found or post-to URL missing.", currentForm);
                fireSweetalert('error', 'Configuration Error', 'Could not submit the form due to a setup issue.');
                return;
            }

            // Disable button and show loader
            $submitButton.prop('disabled', true).addClass('loader');

            // Clear previous errors
            formElements.forEach(item => {
                if (item.errEl) {
                    item.errEl.removeClass('show');
                }
            });

            // Get the form data
            const formData = new FormData(this);

            // method used to reset the form
            const resetForm = () => {
                if(needsReset) {
                    // Reset the form fields
                    currentForm.reset();
                    // Dispatch a custom event - other parts of our JS need to know
                    window.dispatchEvent(new CustomEvent('form-reset'));
                }
            }

            // Perform AJAX request
            $.ajax({
                url: postTo,
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json', // Expect JSON response from server
                success: function(response) {
                    // Display success message
                    fireSweetalert(
                        'success',
                        response.title || 'Thank you!',
                        response.message || 'Your form has been submitted successfully.'
                    );
                    // Reset the form
                    resetForm();
                },
                error: function(xhr) {
                    // Network Error Check (0) (offline, DNS error, CORS pre-flight failure, etc.)
                    if (xhr.status === 0) {
                        fireSweetalert(
                            'error',
                            'Network Error',
                            'Could not connect to the server. Please check your internet connection and try again.'
                        );
                    }
                    // Handle validation errors (422)
                    else if (xhr.status === 422 && xhr.responseJSON?.errors) {
                        const errors = xhr.responseJSON.errors;
                        Object.keys(errors).forEach(key => {
                            const elementInfo = formElements.find(el => el.name === key);
                            if (elementInfo && elementInfo.errEl) {
                                elementInfo.errEl.text(errors[key][0]).addClass('show'); // Display the first error message for the field
                            }
                        });
                        // if the request has a title and message, then dispatch an alert
                        if(xhr.responseJSON?.message && xhr.responseJSON?.title) {
                            fireSweetalert(
                                'info',
                                xhr.responseJSON?.title,
                                xhr.responseJSON?.message,
                            );
                        }
                    }
                    // Handle Rate Limiting errors (429)
                    else if (xhr.status === 429) {
                        fireSweetalert(
                            'warning',
                            'Too Many Requests',
                            xhr.responseJSON?.message || 'You have submitted this form too many times. Please wait a moment and try again.'
                        );
                    }
                    // Handle server errors (500)
                    else if (xhr.status === 500) {
                        fireSweetalert(
                            'error',
                            'Something went wrong on our end.',
                            xhr.responseJSON?.message || 'An unexpected error occurred on our server. Please try again later. If the issue persists, contact support.'
                        );
                        // Optionally reset the form even on server error, or leave data for retry
                        // resetForm();
                    }
                    // Handle Not Implemented (501)
                    else if (xhr.status === 501) {
                        fireSweetalert(
                            'info',
                            'Not Implemented.',
                            xhr.responseJSON?.message || 'The feature has not been implemented yet.'
                        );
                        // Optionally reset the form even on server error, or leave data for retry
                        // resetForm();
                    }
                    // Handle other errors
                    else {
                        fireSweetalert(
                            'info',
                            xhr.responseJSON?.title || 'Oops! Something went wrong.',
                            xhr.responseJSON?.message || 'An unexpected error occurred. Please try again or refresh the page.'
                        );
                        // Optionally reset form
                        // resetForm();
                    }
                },
                complete: function() {
                    // This block runs whether the request succeeds or fails
                    // Re-enable button and remove loader
                    $submitButton.prop('disabled', false).removeClass('loader');
                }
            });
        });
    });
};
