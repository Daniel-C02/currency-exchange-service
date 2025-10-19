
export const init = () => {

    // -----------------------------------------------------------------------------------------------------------------
    // --- DOM ELEMENT SELECTION
    // -----------------------------------------------------------------------------------------------------------------

    const form = document.getElementById('currency_exchange_form');
    if (!form) return; // Exit if the form isn't on this page

    const currencySelect = document.getElementById('currency_option');
    const foreignAmountInput = document.getElementById('foreign_currency_amount');
    const localAmountInput = document.getElementById('local_currency_amount');
    const payableInput = document.getElementById('amount_payable');
    const receivableInput = document.getElementById('amount_receivable');
    const surchargeInput = document.getElementById('surcharge_amount');
    const receivableLabel = document.getElementById('receivable_label');
    const submitButton = document.getElementById('submit_button');

    let currencies = {}; // Currencies populated by the API call

    /**
     * Fetches currency data from the API and initializes the form.
     */
    async function initializeApp() {
        try {
            // Use Axios to make a GET request to the API endpoint
            const response = await window.axios.get('/api/currencies');
            const currenciesData = response.data;

            // Re-key the array into an object for quick lookups
            currencies = currenciesData.reduce((acc, currency) => {
                acc[currency.code] = {
                    exchange_rate: parseFloat(currency.exchange_rate),
                    surcharge_percentage: parseFloat(currency.surcharge_percentage)
                };
                return acc;
            }, {});

            // Now that we have the data, attach the event listeners
            attachEventListeners();

        } catch (error) {
            console.error('Failed to fetch currency data:', error);
            // Optionally, display an error message to the user on the page
        }
    }

    /**
     * Attaches all necessary event listeners to the form inputs.
     */
    function attachEventListeners() {
        // Calculate change upon user select
        currencySelect.addEventListener('change', calculateExchange);
        // Upon user input, clear the localAmountInput
        foreignAmountInput.addEventListener('input', () => {
            if (foreignAmountInput.value) {
                localAmountInput.value = ''; // Clear the other amount field
            }
            calculateExchange();
        });
        // Upon user input, clear the foreignAmountInput
        localAmountInput.addEventListener('input', () => {
            if (localAmountInput.value) {
                foreignAmountInput.value = ''; // Clear the other amount field
            }
            calculateExchange();
        });
    }

    /**
     * Main function to perform all currency calculations and update the UI.
     */
    function calculateExchange() {
        // Chosen foreign currency to be purchased
        const selectedCurrencyCode = currencySelect.value;
        // Amount of foreign currency to be purchased
        const foreignAmount = parseFloat(foreignAmountInput.value);
        // Amount of local currency (ZAR) to pay with
        const localAmount = parseFloat(localAmountInput.value);

        // If both the foreignAmount AND localAmount are empty OR the selectedCurrencyCode
        // has not been chosen, then clear all totals and exit.
        const hasNoValidAmount = (isNaN(foreignAmount) || foreignAmount <= 0) && (isNaN(localAmount) || localAmount <= 0);
        if (!selectedCurrencyCode || hasNoValidAmount) {
            clearFields();
            return;
        }

        // Get the required to currency to perform calculations on.
        const currency = currencies[selectedCurrencyCode];
        if (!currency) return;

        // Set the receivable label to display the desired currency
        receivableLabel.innerText = `Receivable (${selectedCurrencyCode})`;

        // Set initial values
        let baseZarAmount = 0;
        let foreignAmountToReceive = 0;

        // If the foreign amount has been set -> calculate the local amount to pay
        if (!isNaN(foreignAmount) && foreignAmount > 0) {
            foreignAmountToReceive = foreignAmount;
            baseZarAmount = foreignAmountToReceive / currency.exchange_rate;
        }
        // If the local amount has been set -> calculate the foreign amount to receive
        else if (!isNaN(localAmount) && localAmount > 0) {
            baseZarAmount = localAmount / (1 + (currency.surcharge_percentage / 100));
            foreignAmountToReceive = baseZarAmount * currency.exchange_rate;
        }

        // Calculate the surcharge amount (ZAR)
        const surchargeAmount = baseZarAmount * (currency.surcharge_percentage / 100);
        // Calculate the total payable amount (ZAR)
        const totalPayable = baseZarAmount + surchargeAmount;

        // Set all calculated fields and enable the submit button
        payableInput.value = totalPayable.toFixed(2);
        receivableInput.value = foreignAmountToReceive.toFixed(2);
        surchargeInput.value = surchargeAmount.toFixed(2);
        submitButton.disabled = false;
    }

    /**
     * Clears all calculated fields and disables the submit button.
     */
    function clearFields() {
        payableInput.value = '';
        receivableInput.value = '';
        surchargeInput.value = '';
        submitButton.disabled = true;
        if (!currencySelect.value) {
            receivableLabel.innerText = 'Receivable (N/A)';
        }
    }

    // Start the application
    initializeApp().then(r => null);
}
