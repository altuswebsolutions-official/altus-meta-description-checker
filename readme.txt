=== Altus Meta Description Checker ===
Contributors: altuswebsolutions-official
Tags: meta description, meta description checker, seo, serp preview, google snippet
Requires at least: 5.8
Tested up to: 7.1
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Check any page's meta description — length, pixel-width fit, keyword use and call to action, scored out of 100 with a live Google preview.

== Description ==

Your meta description is the 155 characters under your page title in Google — and it decides whether anyone clicks. This tool scores any description out of 100 and shows a live Google snippet preview, cut exactly where Google would cut it.

Use the shortcode `[altus_meta_checker]` on any page.

Paste a URL and the tool fetches the live page and reads its title and meta description for you, or paste your own draft title and description. You get a score out of 100 with a plain-English grade (Excellent, Good, Needs work, Poor), a desktop and mobile Google preview, eight description checks (present, length, pixel-width fit, keyword placement, unique vs title, call to action, no shouting, clean ending) and four title checks — plus Copy and Download (.txt) report buttons.

Pixel widths are honest estimates from Arial glyph metrics, calibrated so a typical 155-character description lands near Google's ~920 px desktop cut-off. Google renders snippets dynamically and sometimes rewrites descriptions, so the preview is a close approximation, not a guarantee.

The public-URL fetch is guarded: private and reserved hosts are blocked (checked before the fetch and again after DNS resolution), it is rate-limited to 5 fetches per IP per hour, and nothing fetched is stored. The scoring itself runs entirely in the visitor's browser.

This open-source version contains NO lead capture, NO email sending, and NO data storage — results are shown on screen only. The version hosted on altuswebsolutions.com may offer email delivery; this open-source version does not.

== Installation ==

1. Upload the plugin zip via Plugins > Add New > Upload Plugin, or extract it into `/wp-content/plugins/`.
2. Activate the plugin.
3. Add `[altus_meta_checker]` to any page and publish it.

== Frequently Asked Questions ==

= Is it really free? =

Yes. Every check, every preview and every report download is free, with no account and no email address required.

= Does the meta description affect my Google ranking? =

Not directly — Google has said it isn't a ranking factor. But it strongly affects your click-through rate: the words searchers see decide whether they choose your listing or the one below it. More clicks on the same ranking means more business from the traffic you already have.

= Why does Google rewrite my meta description? =

Google rewrites descriptions it considers unhelpful — usually because they are missing, very short, stuffed with keywords, or identical across many pages. Writing a unique, honest 120–155 character description for each important page is the best way to keep control of what searchers see.

= How long should a meta description be? =

Aim for 120–155 characters, and keep the important words in the first 120. Google cuts by pixel width (about 920 pixels on desktop), not characters — wide letters eat more space. The tool measures both and shows you exactly where the cut lands.

= Are the pixel widths exact? =

They are close estimates based on Arial's font metrics, calibrated to Google's typical cut-off. Google renders snippets dynamically and sometimes shows different text, so treat the preview as a faithful approximation — not a pixel-perfect guarantee.

= Can I check a competitor's page? =

Yes — paste any public URL. It is a handy way to see how your snippet stacks up against the listings around you.

= Is the page I fetch stored anywhere? =

No. The fetch reads the page's title and meta description, returns them to your browser, and stores nothing on the server. Fetches are limited to 5 per IP per hour and private/internal addresses are blocked.

== Changelog ==

= 1.0.0 =

First public release (stripped open-source version). Fetch a live URL (SSRF-guarded, rate-limited) or paste your own title, description and keyword; score out of 100 with grade bands; live desktop/mobile Google SERP preview cut at Google's pixel width; 8 description checks and 4 title checks; copy and download report; pixel-width estimates from Arial glyph metrics. Email delivery, lead storage and email sending removed. The scoring logic passed a 57-test Node harness and the PHP meta extraction passed an 11-test harness in the original build.
