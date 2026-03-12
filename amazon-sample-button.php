<?php
/*
Plugin Name: Amazon Sample Button
Description: Opens Amazon "Read a Sample" using WooCommerce SKU (ASIN). Supports desktop popup window with mobile/new-tab fallback. Use with any link or button pointing to #read-sample-modal.
Author: Byron Iniotakis
Version: 2.2
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Build Amazon sample URL from WooCommerce product SKU.
 */
function asb_get_amazon_sample_url( $product ) {
	if ( ! $product || ! is_object( $product ) ) {
		return '';
	}

	$asin = $product->get_sku();

	if ( ! $asin ) {
		return '';
	}

	$asin = strtoupper( trim( $asin ) );
	$asin = preg_replace( '/[^A-Z0-9]/', '', $asin );

	if ( empty( $asin ) ) {
		return '';
	}

	return 'https://read.amazon.com/kp/embed?linkCode=kpe&asin=' . rawurlencode( $asin ) . '&preview=newtab';
}

/**
 * Inject JS on WooCommerce single product pages.
 */
add_action( 'wp_footer', function() {
	if ( ! function_exists( 'is_product' ) || ! is_product() ) {
		return;
	}

	global $product;

	if ( ! $product ) {
		return;
	}

	$sample_url = asb_get_amazon_sample_url( $product );

	if ( empty( $sample_url ) ) {
		return;
	}
	?>
	<script>
	document.addEventListener('DOMContentLoaded', function () {
		const sampleUrl = <?php echo wp_json_encode( $sample_url ); ?>;

		// Backward-compatible selector:
		// any link/button using href="#read-sample-modal"
		// plus optional class hooks if you ever want them.
		const triggers = document.querySelectorAll(
			'a[href="#read-sample-modal"], button[data-amazon-sample="1"], .amazon-sample-button'
		);

		if (!triggers.length) {
			return;
		}

		function isMobileLike() {
			return window.matchMedia('(max-width: 767px)').matches ||
				/mobile|android|iphone|ipad|ipod/i.test(navigator.userAgent);
		}

		function openAmazonSample(e) {
			if (e) {
				e.preventDefault();
			}

			// Mobile/tablet: open in new tab
			if (isMobileLike()) {
				window.open(sampleUrl, '_blank', 'noopener,noreferrer');
				return;
			}

			// Desktop: try popup window first
			const popupWidth  = Math.min(1100, Math.floor(window.screen.availWidth * 0.9));
			const popupHeight = Math.min(800, Math.floor(window.screen.availHeight * 0.9));
			const left = Math.max(0, Math.floor((window.screen.availWidth - popupWidth) / 2));
			const top  = Math.max(0, Math.floor((window.screen.availHeight - popupHeight) / 2));

			const features = [
				'popup=yes',
				'width=' + popupWidth,
				'height=' + popupHeight,
				'left=' + left,
				'top=' + top,
				'resizable=yes',
				'scrollbars=yes',
				'toolbar=no',
				'menubar=no',
				'location=yes',
				'status=no'
			].join(',');

			const popup = window.open(sampleUrl, 'amazonSamplePreview', features);

			// Fallback if popup blocked
			if (!popup || popup.closed || typeof popup.closed === 'undefined') {
				window.open(sampleUrl, '_blank', 'noopener,noreferrer');
				return;
			}

			try {
				popup.focus();
			} catch (err) {}
		}

		triggers.forEach(function (trigger) {
			// Keep markup usable even without JS
			if (trigger.tagName.toLowerCase() === 'a') {
				trigger.setAttribute('href', sampleUrl);
				trigger.setAttribute('target', '_blank');
				trigger.setAttribute('rel', 'noopener noreferrer');
			}

			trigger.addEventListener('click', openAmazonSample);
		});
	});
	</script>
	<?php
});
