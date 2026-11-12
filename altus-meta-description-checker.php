<?php
/**
 * Plugin Name: Altus Meta Description Checker
 * Description: Free tool. Check any page's meta description — length, pixel-width fit, keyword use, call to action — with a live Google SERP preview. Fetch from URL or paste text.
 * Version: 1.0.0
 * Author: Altus Web Solutions
 * Text Domain: altus-meta-description-checker
 * Requires at least: 5.8
 * Requires PHP: 7.4
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 *
 * No third-party libraries. Width estimates use Arial glyph metrics in JS.
 *
 * Stripped open-source copy: no email, no lead storage, no database table —
 * the report is shown on screen and can be copied or downloaded. Fetching a
 * public URL you enter is guarded (private/reserved hosts blocked, 5 checks
 * per IP per hour) and nothing fetched is stored.
 */

if (!defined('ABSPATH')) exit;

define('MDC_VERSION', '1.0.0');
define('MDC_PATH', plugin_dir_path(__FILE__));
define('MDC_URL', plugin_dir_url(__FILE__));
/* ------------------------------------------------------------------ */
/* Frontend assets (only loaded when the shortcode is present)        */
/* ------------------------------------------------------------------ */
add_action('wp_enqueue_scripts', 'mdc_enqueue');
function mdc_enqueue() {
    if (is_admin()) return;
    global $post;
    if (!($post instanceof WP_Post) || !has_shortcode($post->post_content, 'altus_meta_checker')) return;

    wp_enqueue_style('mdc-gen', MDC_URL . 'assets/generator.css', array(), MDC_VERSION);
    wp_enqueue_script('mdc-logic', MDC_URL . 'assets/meta-logic.js', array(), MDC_VERSION, true);
    wp_enqueue_script('mdc-gen', MDC_URL . 'assets/generator.js', array('mdc-logic'), MDC_VERSION, true);
    wp_localize_script('mdc-gen', 'MDC_AJAX', array(
        'ajax'  => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('mdc_gen'),
    ));
}

/* ------------------------------------------------------------------ */
/* Shortcode: renders the checker UI                                  */
/* ------------------------------------------------------------------ */
add_shortcode('altus_meta_checker', 'mdc_shortcode');
function mdc_shortcode() {
    ob_start();
    ?>
    <div id="mdc-root" class="mdc-root">
        <div class="mdc-card">
            <div class="mdc-badge">Free tool</div>
            <h2 class="mdc-title">Check your meta description</h2>
            <p class="mdc-sub">Fetch a live page or paste your title and meta description — see exactly how Google would show it, and get a plain-English score with fixes.</p>

            <div class="mdc-tabs" role="tablist">
                <button type="button" class="mdc-tab mdc-tab-active" id="mdc-tab-url">Check a live URL</button>
                <button type="button" class="mdc-tab" id="mdc-tab-text">Write your own</button>
            </div>

            <div id="mdc-pane-url">
                <div class="mdc-field">
                    <label class="mdc-label" for="mdc-url">Page URL</label>
                    <div class="mdc-row">
                        <input type="url" id="mdc-url" class="mdc-input" placeholder="https://example.com/your-page" autocomplete="off" spellcheck="false">
                        <button type="button" class="mdc-btn mdc-btn-secondary" id="mdc-fetch">Fetch page</button>
                    </div>
                    <p class="mdc-fetch-msg" id="mdc-fetch-msg" hidden></p>
                </div>
            </div>
            <div id="mdc-pane-text" hidden>
                <p class="mdc-hint" style="margin-bottom:14px">Paste the title and meta description you are planning to use — the check runs on your text, nothing is fetched.</p>
            </div>

            <p class="mdc-error" id="mdc-error" role="alert" hidden></p>

            <div class="mdc-field">
                <label class="mdc-label" for="mdc-title">Page title</label>
                <input type="text" id="mdc-title" class="mdc-input" placeholder="e.g. Affordable Web Design for Small Businesses | Altus" autocomplete="off">
                <p class="mdc-count" id="mdc-title-count">0 characters</p>
            </div>

            <div class="mdc-field">
                <label class="mdc-label" for="mdc-desc">Meta description</label>
                <textarea id="mdc-desc" class="mdc-input" placeholder="e.g. We build fast, beautiful websites for UK small businesses that win customers. Book your free consultation today."></textarea>
                <p class="mdc-count" id="mdc-desc-count">0 / 155 characters</p>
            </div>

            <div class="mdc-field">
                <label class="mdc-label" for="mdc-keyword">Target keyword <span style="font-weight:400;color:#7b8ba0">(optional)</span></label>
                <input type="text" id="mdc-keyword" class="mdc-input" placeholder="e.g. web design" autocomplete="off">
            </div>

            <button type="button" class="mdc-btn" id="mdc-check">Check my snippet</button>

            <div id="mdc-results" hidden>
                <h3 class="mdc-checks-title" style="margin-top:28px">Your score</h3>
                <div class="mdc-score">
                    <div class="mdc-score-ring">
                        <div class="mdc-score-num" id="mdc-score-num">0</div>
                        <div class="mdc-score-max">/ 100</div>
                    </div>
                    <div class="mdc-score-meta">
                        <div class="mdc-grade" id="mdc-score-grade">–</div>
                        <div class="mdc-score-track"><div class="mdc-score-fill" id="mdc-score-bar"></div></div>
                    </div>
                </div>

                <div class="mdc-preview-head">
                    <h3>Google preview</h3>
                    <div class="mdc-tabs" style="margin:0">
                        <button type="button" class="mdc-tab mdc-tab-active" id="mdc-view-desktop">Desktop</button>
                        <button type="button" class="mdc-tab" id="mdc-view-mobile">Mobile</button>
                    </div>
                </div>
                <div class="mdc-serp" id="mdc-prev-desktop">
                    <div class="mdc-serp-url" id="mdc-prev-url">yourwebsite.com › your-page</div>
                    <div class="mdc-serp-title" id="mdc-prev-title"></div>
                    <p class="mdc-serp-desc" id="mdc-prev-desc"></p>
                </div>
                <div class="mdc-serp mdc-serp-mobile" id="mdc-prev-mobile" hidden>
                    <div class="mdc-serp-url" id="mdc-prev-url-m">yourwebsite.com › your-page</div>
                    <div class="mdc-serp-title" id="mdc-prev-title-m"></div>
                    <p class="mdc-serp-desc" id="mdc-prev-desc-m"></p>
                </div>
                <p class="mdc-prev-note" id="mdc-prev-note"></p>

                <h3 class="mdc-checks-title">Meta description checks</h3>
                <ul class="mdc-checks" id="mdc-checks-desc"></ul>

                <h3 class="mdc-checks-title">Title checks</h3>
                <ul class="mdc-checks" id="mdc-checks-title"></ul>

                <div class="mdc-btn-row">
                    <button type="button" class="mdc-btn mdc-btn-ghost" id="mdc-copy">Copy my report</button>
                    <button type="button" class="mdc-btn mdc-btn-ghost" id="mdc-download">Download report (.txt)</button>
                </div>
                <p class="mdc-copy-msg" id="mdc-copy-msg" hidden></p>

            <p class="mdc-fineprint">Pixel widths are estimates based on Arial glyph metrics — Google renders snippets dynamically and often rewrites descriptions, so treat the preview as a close approximation, not a guarantee.</p>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

/* ------------------------------------------------------------------ */
/* Meta extraction (pure function — unit-tested)                      */
/* ------------------------------------------------------------------ */
function mdc_extract_meta($html) {
    $out = array('title' => '', 'description' => '', 'source' => 'none');
    if (!is_string($html) || $html === '') return $out;

    if (preg_match('/<title[^>]*>(.*?)<\/title>/is', $html, $m)) {
        $out['title'] = mdc_clean_text($m[1]);
    }

    // Collect meta tags; prefer name="description", fall back to og:description.
    $og = '';
    if (preg_match_all('/<meta\b[^>]*>/i', $html, $tags)) {
        foreach ($tags[0] as $tag) {
            $attrs = array();
            if (preg_match_all('/([\w:-]+)\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/', $tag, $pairs, PREG_SET_ORDER)) {
                foreach ($pairs as $p) {
                    $attrs[strtolower($p[1])] = trim($p[2], "\"'");
                }
            }
            $key = isset($attrs['name']) ? strtolower($attrs['name'])
                 : (isset($attrs['property']) ? strtolower($attrs['property']) : '');
            if ($key === 'description' && isset($attrs['content']) && $out['description'] === '') {
                $out['description'] = mdc_clean_text($attrs['content']);
                $out['source'] = 'meta';
            } elseif ($key === 'og:description' && isset($attrs['content']) && $og === '') {
                $og = mdc_clean_text($attrs['content']);
            }
        }
    }
    if ($out['description'] === '' && $og !== '') {
        $out['description'] = $og;
        $out['source'] = 'og';
    }
    return $out;
}

function mdc_clean_text($s) {
    $s = wp_strip_all_tags(html_entity_decode((string) $s, ENT_QUOTES, 'UTF-8'));
    $s = preg_replace('/\s+/u', ' ', $s);
    return trim($s);
}

/* ------------------------------------------------------------------ */
/* AJAX: fetch a page and extract title + meta description            */
/* ------------------------------------------------------------------ */
add_action('wp_ajax_mdc_fetch_page', 'mdc_fetch_page');
add_action('wp_ajax_nopriv_mdc_fetch_page', 'mdc_fetch_page');
function mdc_fetch_page() {
    if (!check_ajax_referer('mdc_gen', 'nonce', false)) {
        wp_send_json_error('Security check failed. Please reload the page and try again.');
    }

    // Rate limit: 5 fetches per IP per hour (public-URL fetch is the only
    // server-side action this stripped version performs).
    $ip  = mdc_client_ip();
    $key = 'mdc_rl_' . md5($ip);
    $hits = (int) get_transient($key);
    if ($hits >= 5) {
        wp_send_json_error('Too many checks from this address. Please try again later.');
    }
    set_transient($key, $hits + 1, HOUR_IN_SECONDS);

    $url = isset($_POST['url']) ? trim(sanitize_text_field(wp_unslash($_POST['url']))) : '';
    $url = esc_url_raw($url, array('http', 'https'));
    if (!$url || !wp_http_validate_url($url)) {
        wp_send_json_error('That does not look like a valid URL.');
    }

    // Refuse non-public hosts (SSRF guard): no localhost, no IPs, no ports,
    // no private suffixes — and the resolved IP must be public too.
    $host = strtolower((string) wp_parse_url($url, PHP_URL_HOST));
    if (!$host || $host === 'localhost' || filter_var($host, FILTER_VALIDATE_IP)
        || preg_match('/\.(local|test|internal|lan)$/', $host)
        || wp_parse_url($url, PHP_URL_PORT)) {
        wp_send_json_error('Sorry, that address cannot be checked.');
    }
    if (!mdc_url_allowed($url)) {
        wp_send_json_error('Sorry, that address cannot be checked.');
    }

    $res = wp_remote_get($url, array(
        'timeout'             => 12,
        'redirection'         => 3,
        'limit_response_size' => 200000,
        'user-agent'          => 'AltusMetaChecker/1.0 (+https://altuswebsolutions.com/tools/meta-description-checker/)',
        'headers'             => array('Accept' => 'text/html'),
    ));
    if (is_wp_error($res)) {
        wp_send_json_error('Could not reach that page. Check the URL and try again.');
    }
    $code = (int) wp_remote_retrieve_response_code($res);
    if ($code < 200 || $code >= 400) {
        wp_send_json_error('That page returned an error (' . $code . '). Try another URL.');
    }
    $type = (string) wp_remote_retrieve_header($res, 'content-type');
    if ($type !== '' && stripos($type, 'html') === false) {
        wp_send_json_error('That URL is not a web page (it looks like a file download).');
    }

    $meta = mdc_extract_meta((string) wp_remote_retrieve_body($res));
    wp_send_json_success(array(
        'url'         => esc_url_raw($url),
        'title'       => $meta['title'],
        'description' => $meta['description'],
        'source'      => $meta['source'],
    ));
}

/** SSRF guard: block private/reserved IP ranges after DNS resolution. */
function mdc_client_ip() {
    foreach (array('HTTP_X_FORWARDED_FOR', 'HTTP_CLIENT_IP', 'REMOTE_ADDR') as $k) {
        if (!empty($_SERVER[$k])) {
            $raw = sanitize_text_field(wp_unslash($_SERVER[$k]));
            $ip = trim(explode(',', $raw)[0]);
            if (filter_var($ip, FILTER_VALIDATE_IP)) return $ip;
        }
    }
    return '0.0.0.0';
}

function mdc_url_allowed($url) {
    $host = wp_parse_url($url, PHP_URL_HOST);
    if (!$host) return false;
    if (filter_var($host, FILTER_VALIDATE_IP)) {
        return mdc_ip_public($host);
    }
    $ip = gethostbyname($host);
    if ($ip === $host) return false; // DNS failed
    return mdc_ip_public($ip);
}

function mdc_ip_public($ip) {
    if (!filter_var($ip, FILTER_VALIDATE_IP)) return false;
    // Reject private, reserved, loopback, link-local, multicast.
    return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false;
}
