Amazon Sample Button
Drop-in Kindle “Read a Sample” modal for WooCommerce.
When a product’s SKU matches its Amazon ASIN, the plugin auto-outputs a modal and JS on single product pages. Any link or button pointing to #read-sample-modal will open an embedded Kindle preview for that ASIN.

How it works
Runs on single product pages only (is_product()).

Reads the product SKU and treats it as the ASIN.

Listens for clicks on a[href="#read-sample-modal"].

Opens a responsive modal with an iframe to:
https://read.amazon.com/kp/embed?linkCode=kpe&asin=<SKU>&preview=newtab

Close via the “×” button; modal adapts width (max 1024px, 360px on very small screens).

Usage
Ensure your WooCommerce product SKU = Amazon ASIN.

Add a button/link anywhere on the product page, e.g. in the short description, template, or builder:

html
Αντιγραφή
Επεξεργασία
<a href="#read-sample-modal" class="button">Read a sample</a>
That’s it—clicking opens the Kindle preview modal.

Requirements
WordPress + WooCommerce

Product SKU set to the correct Amazon ASIN

Notes
No admin UI, settings, or shortcodes.

Works with most themes/page builders.

If your site uses a strict Content Security Policy, allow read.amazon.com for iframes.
