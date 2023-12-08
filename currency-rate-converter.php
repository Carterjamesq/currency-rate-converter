<?php
/*
Plugin Name: Currency Rate Converter
Description: WordPress plugin for currency rate conversion.
Version: 1.0
Author: Vitalyi Rabchevskyi
*/

function currency_rate_shortcode($atts) {
    // API token from fxratesapi.com
    $api_key = 'fxr_live_9ac3cd555827b1f129ac84500c5c4bafd87a';

    // Get shortcode parameters
    $atts = shortcode_atts(
        array(
            'from' => 'USD',
            'to' => 'UAH',
        ),
        $atts,
        'currency_rate'
    );

    $from_currency = strtoupper($atts['from']);
    $to_currency = strtoupper($atts['to']);

    // Construct API URL with your API key
    $api_url = "https://api.fxratesapi.com/latest?api_key={$api_key}&base_currency={$from_currency}&symbols={$to_currency}";
    $response = wp_remote_get($api_url);

    if (is_wp_error($response)) {
        return 'Error fetching currency rate: ' . $response->get_error_message();
    }

    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body);

    // Check for errors in the API response
    if (isset($data->error)) {
        return 'Error from API: ' . $data->error->info;
    }

    // Check if data is available
    if (isset($data->rates->{$to_currency})) {
        $rate = $data->rates->{$to_currency};

        // Limit the decimal places to 4
        $rate = number_format($rate, 2);

    // Display the result in HTML with currency dropdowns
    $output = "<div class='currency-rate'>
        <p><strong>Exchange Rate:</strong></p>
        <p>
          1 
          <select class='currency-selector' data-type='from'>
              <option value='USD'>USD</option>
              <option value='EUR'>EUR</option>
              <option value='GBP'>GBP</option>
              <option value='JPY'>JPY</option>
              <option value='UAH'>UAH</option>
              <!-- Add more currencies as needed -->
          </select>
          =
          <span class='exchange-rate'>{$rate}</span>
          <select class='currency-selector' data-type='to'>
              <option value='USD'>USD</option>
              <option value='EUR'>EUR</option>
              <option value='GBP'>GBP</option>
              <option value='JPY'>JPY</option>
              <option value='UAH'>UAH</option>
              <!-- Add more currencies as needed -->
          </select>
        </p>
     </div>";

    return $output;
    } else {
        return 'Currency rate not available.';
    }
}

// Enqueue the custom CSS and JavaScript files
function currency_rate_enqueue_scripts() {
    wp_enqueue_style('currency-rate-styles', plugin_dir_url(__FILE__) . 'assets/css/style.css');
    wp_enqueue_script('currency-rate-script', plugin_dir_url(__FILE__) . 'assets/js/script.js', array('jquery'), null, true);

    // Pass the ajax_url to script.js
    wp_localize_script('currency-rate-script', 'currency_rate_object', array('ajax_url' => admin_url('admin-ajax.php')));
}
add_action('wp_enqueue_scripts', 'currency_rate_enqueue_scripts');

// Add the shortcode
add_shortcode('currency_rate', 'currency_rate_shortcode');

// Ajax handler for updating exchange rates
function currency_rate_ajax_update() {
    $from_currency = strtoupper($_POST['from']);
    $to_currency = strtoupper($_POST['to']);

    // Construct API URL with your API key
    $api_url = "https://api.fxratesapi.com/latest?api_key=YOUR_API_KEY&base_currency={$from_currency}&symbols={$to_currency}";
    $response = wp_remote_get($api_url);

    if (!is_wp_error($response)) {
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body);

        if (isset($data->rates->{$to_currency})) {
            $rate = number_format($data->rates->{$to_currency}, 2);
            echo $rate;
        } else {
            echo 'Currency rate not available.';
        }
    } else {
        echo 'Error fetching currency rate: ' . $response->get_error_message();
    }

    wp_die();
}
add_action('wp_ajax_currency_rate_update', 'currency_rate_ajax_update');
add_action('wp_ajax_nopriv_currency_rate_update', 'currency_rate_ajax_update');
?>