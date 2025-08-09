<?php
/*
Plugin Name: Amazon Sample Button
Description: Adds Amazon "Read a Sample" modal using WooCommerce SKU (ASIN). Use with any button linking to #read-sample-modal.
Author: Byron Iniotakis
Version: 2.0
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

// Output modal HTML and JS automatically on product pages
add_action( 'wp_footer', function() {
    if ( ! is_product() ) {
        return;
    }

    global $product;
    if ( ! $product ) {
        return;
    }

    $asin = $product->get_sku();

    if ( ! $asin ) {
        return;
    }
    ?>

    <!-- Modal Structure -->
    <div id="sampleModal" style="display:none; position:fixed; z-index:1000; left:0; top:0; width:100%; height:100%; overflow:auto; background-color:rgba(0,0,0,0.5);">
      <div id="sampleModalContent" style="background:#fff; margin:5% auto; padding:10px; border-radius:10px; width:90%; max-width:1024px; position:relative;">
        <span onclick="closeSampleModal()" style="position:absolute; top:10px; right:20px; font-size:30px; cursor:pointer;">&times;</span>
        <iframe id="sampleIframe" src="" width="100%" height="600" frameborder="0" allowfullscreen style="border:0;">
        </iframe>
      </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('a[href="#read-sample-modal"]').forEach(function(button) {
            button.addEventListener('click', function(e) {
                e.preventDefault();

                const iframe = document.getElementById('sampleIframe');
                if (iframe) {
                    iframe.src = "https://read.amazon.com/kp/embed?linkCode=kpe&asin=<?php echo esc_js( $asin ); ?>&preview=newtab"; 
                }

                const modal = document.getElementById('sampleModal');
                if (modal) {
                    modal.style.display = 'block';
                    adjustModalWidth();
                }
            });
        });
    });

    function closeSampleModal() {
        document.getElementById('sampleModal').style.display = 'none';
        document.getElementById('sampleIframe').src = "";
    }

    function adjustModalWidth() {
        const modalContent = document.getElementById('sampleModalContent');
        if (window.innerWidth <= 480) {
            modalContent.style.width = '360px';
        } else {
            modalContent.style.width = '90%';
            if (window.innerWidth * 0.9 > 1024) {
                modalContent.style.width = '1024px';
            }
        }
    }

    window.addEventListener('resize', adjustModalWidth);
    </script>

    <?php
});
