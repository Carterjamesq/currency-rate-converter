jQuery(document).ready(function ($) {
    // Function to update exchange rates
    function updateExchangeRates() {
        // Get the selected currencies from the dropdowns
        var fromCurrency = $('.currency-selector[data-type="from"]').val();
        var toCurrency = $('.currency-selector[data-type="to"]').val();

        // AJAX request to update exchange rates
        $.ajax({
            type: 'POST',
            url: currency_rate_object.ajax_url,
            data: {
                action: 'currency_rate_update',
                from: fromCurrency,
                to: toCurrency,
            },
            success: function (response) {
                // Update the displayed exchange rate on success
                $('.exchange-rate').text(response);
            },
            error: function (error) {
                console.log('Error updating exchange rates: ' + error.responseText);
            },
        });
    }

    // Update exchange rates when the page loads
    updateExchangeRates();

    // Update exchange rates when the currency selection changes
    $('.currency-selector').change(updateExchangeRates);

    // Update exchange rates every 60 seconds (adjust as needed)
    setInterval(updateExchangeRates, 60000);
});
