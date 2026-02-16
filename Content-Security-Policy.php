<?php 

// ============================================
// |    |    To Add nonce Dynamically    |    |
// ============================================
 
// 1️⃣ Generate a single nonce per request
function my_csp_nonce() {
    static $nonce = null;
    if ($nonce === null) {
        $nonce = base64_encode(random_bytes(16));
    }
    return $nonce;
}
 
// 2️⃣ Send CSP header with the nonce (front-end only)
add_action('send_headers', function () {
    if (!is_admin()) { // skip admin pages
        $nonce = my_csp_nonce();
 
        header(
            "Content-Security-Policy: " .
            "default-src 'self'; " .
            "script-src 'self' 'nonce-{$nonce}' 'unsafe-inline' 'strict-dynamic' https://www.googletagmanager.com https://www.google-analytics.com https:; " .
            "style-src 'self' 'unsafe-inline' https:; " .
            "font-src 'self' data: https:; " .
            "connect-src 'self' https://www.googletagmanager.com https://www.google-analytics.com https: wss:; " .
            "img-src 'self' data: https://www.googletagmanager.com https://www.google-analytics.com https:; " .
            "child-src data:; " .
            "frame-src 'self' https://*.google.com https:; " .
            "base-uri 'self'; " .
            "worker-src 'self' blob:;"
        );
    }
});
 
 
function wporg_my_wp_script_attributes( $attr ) {
    if ( ! isset( $attr['nonce'] ) ) {
        $attr['nonce'] = get_my_custom_nonce();
    }
    return $attr;
} // ✅
 
// 3️⃣ Add nonce to enqueued scripts (front-end only)
add_filter('script_loader_tag', function ($tag, $handle, $src) {
    if (!is_admin()) {
        return str_replace('<script ', '<script nonce="' . esc_attr(my_csp_nonce()) . '" ', $tag);
    }
    return $tag;
}, 10, 3);
 
// 4️⃣ Add nonce to inline scripts (front-end only)
add_filter('print_inline_script_tag', function ($tag) {
    if (!is_admin()) {
        return str_replace('<script ', '<script nonce="' . esc_attr(my_csp_nonce()) . '" ', $tag);
    }
    return $tag;
});
 
// 5️⃣ Apply nonce to any remaining <script> tags in output buffer (front-end only)
add_action('template_redirect', function () {
    if (!is_admin()) {
        ob_start(function ($buffer) {
            return preg_replace('/<script(?![^>]*\bnonce=)/', '<script nonce="' . esc_attr(my_csp_nonce()) . '"', $buffer);
        });
    }
});
 
// 5️⃣ Apply nonce to all <script> tags in output buffer (front-end only)
add_action('wp', function () {
	if (is_admin()) return;
	ob_start(function ($buffer) {
		$nonce = esc_attr(my_csp_nonce());
		return preg_replace('/<script(?![^>]*\bnonce=)/i', '<script nonce="' . $nonce . '"', $buffer);
	});
});