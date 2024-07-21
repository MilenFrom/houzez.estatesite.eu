<?php
// Load WordPress environment
require_once('wp-load.php');

// Function to fetch JSON data from a URL
function fetch_json_data($url) {
    $response = file_get_contents($url);
    if ($response === FALSE) {
        die("Error: Unable to fetch data from $url\n");
    }
    $data = json_decode($response, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        die("Error: Invalid JSON format in $url\n");
    }
    return $data;
}

// Function to get the Houzez theme options from the database
function get_houzez_options() {
    return get_option('houzez_options');
}

// Function to update the Houzez theme options in the database
function update_houzez_options($options) {
    return update_option('houzez_options', $options);
}

// Function to merge translations with Houzez options and save to database
function translate_options() {
    $translations_url = 'https://houzez.estatesite.eu/translations.json';
    $theme_translations = fetch_json_data($translations_url);

    // Get existing Houzez options
    $houzez_opts = get_houzez_options();

    // Check if the translations are valid
    if (is_array($theme_translations)) {
        // Merge translations with existing options
        $updated_options = array_merge($houzez_opts, $theme_translations);

        // Update the options in the database
        if (update_houzez_options($updated_options)) {
            echo "Translations have been applied successfully.\n";
        } else {
            echo "Error: Failed to update options in the database.\n";
        }
    } else {
        echo "Error: Invalid JSON format in the translations.\n";
    }
}

// Function to handle custom fields
function handle_custom_fields() {
    $custom_fields_url = 'https://houzez.estatesite.eu/custom_fields.json';
    $custom_fields = fetch_json_data($custom_fields_url);

    // Assume that you have custom fields logic here
    // You can update the custom fields in the database or perform any other necessary operations

    foreach ($custom_fields as $key => $field) {
        update_option("houzez_custom_field_$key", $field);
    }

    echo "Custom fields have been updated successfully.\n";
}

// Main script
if ($argc > 1) {
    switch ($argv[1]) {
        case '-translate':
            translate_options();
            break;
        case '-custom-fields':
            handle_custom_fields();
            break;
        default:
            echo "Usage: php hzd.php -translate | -custom-fields\n";
            break;
    }
} else {
    echo "Usage: php hzd.php -translate | -custom-fields\n";
}
?>