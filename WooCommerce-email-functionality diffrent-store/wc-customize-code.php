<?php 

// SS :- https://i.imgur.com/07rMdqv.png

// store custom email has been code start here.
    // Function to get store email based on SKU
    function get_store_email($sku) {
        $store_mapping = [];
        $repeater_field = get_field('email_options_with_sku', 'option');

        if ($repeater_field) {
            for ($i = 0; $i < count($repeater_field); $i++) {
                $sku_unit = $repeater_field[$i]['sku_unit'];
                $email = $repeater_field[$i]['email'];

                if ($sku_unit && $email) {
                    $store_mapping[$sku_unit] = $email;
                }
            }
        }

        $store_code = substr($sku, 0, 3);
        return isset($store_mapping[$store_code]) ? $store_mapping[$store_code] : null;
    }

    // Add CC store emails to WooCommerce "New Order" email
    add_filter('woocommerce_email_headers', 'cc_store_email_on_new_order', 10, 3);
    function cc_store_email_on_new_order($headers, $email_id, $order) {
        if ($email_id !== 'new_order' || !$order instanceof WC_Order) {
            return $headers;
        }

        $cc_emails = [];

        foreach ($order->get_items() as $item) {
            $product = $item->get_product();
            if (!$product) continue;

            $sku = $product->get_sku();
            if (!$sku) continue;

            $email = get_store_email($sku);
            if ($email && !in_array($email, $cc_emails)) {
                $cc_emails[] = $email;
            }
        }

        if (!empty($cc_emails)) {
            $headers .= 'Cc: ' . implode(',', $cc_emails) . "\r\n";
        }

        return $headers;
    }
// store custom email has been code end here.