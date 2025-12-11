<?php
/**
 * Plugin Name: Cipit Custom Accordion
 * Description: Accordion component for CIPIT website. Styled using the theme's Golden Ratio and Primary Color variables. Includes auto-open support and robust content padding fix. Source Code: <a href="https://github.com/Muchwat/cipit-custom-accordion" target="_blank">GitHub Repository</a>
 * Version: 1.5.2
 * Author: Kevin Muchwat
 * Author URI: https://github.com/Muchwat
 */

if (!defined('ABSPATH'))
    exit;


// ---------------------------------------------------------
// 1. Load CSS + JS
// ---------------------------------------------------------
function cipit_acc_enqueue_assets()
{

    // Enqueue Font Awesome for the chevron icon
    wp_enqueue_style(
        'cipit-acc-fontawesome',
        'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css',
        array(),
        '5.15.4'
    );

    // CSS: Added inline for single-file plugin simplicity
    wp_register_style('cipit-acc-styles', false);
    wp_enqueue_style('cipit-acc-styles');
    wp_add_inline_style('cipit-acc-styles', cipit_acc_get_css());

    // JS: Added inline for single-file plugin simplicity (relies on jQuery)
    wp_register_script('cipit-acc-script', false, array('jquery'), '1.0', true);
    wp_enqueue_script('cipit-acc-script');
    wp_add_inline_script('cipit-acc-script', cipit_acc_get_js());
}
add_action('wp_enqueue_scripts', 'cipit_acc_enqueue_assets');


// ---------------------------------------------------------
// 2. Shortcodes
// ---------------------------------------------------------
// Global counter ensures unique IDs for accessibility
$cipit_acc_item_counter = 0;

function cipit_acc_accordion_shortcode($atts, $content = null)
{
    return '<div class="custom-accordion">' . do_shortcode($content) . '</div>';
}
add_shortcode('cipit_accordion', 'cipit_acc_accordion_shortcode');


function cipit_acc_accordion_item_shortcode($atts, $content = null)
{
    global $cipit_acc_item_counter;
    $cipit_acc_item_counter++;

    $atts = shortcode_atts(array(
        'title' => 'Accordion Item ' . $cipit_acc_item_counter,
        'open' => 'false'
    ), $atts);

    // Add 'active' and 'default-open' classes if 'open' is true
    $is_open = ($atts['open'] === 'true') ? ' active default-open' : '';

    $id = 'cipit-acc-' . $cipit_acc_item_counter;

    ob_start();
    ?>
    <div class="accordion-item<?php echo $is_open; ?>">
        <button class="accordion-header" aria-expanded="<?php echo $atts['open']; ?>" aria-controls="<?php echo $id; ?>">
            <span class="accordion-title"><?php echo esc_html($atts['title']); ?></span>
            <i class="fas fa-chevron-down accordion-icon"></i>
        </button>

        <div id="<?php echo $id; ?>" class="accordion-content">
            <p><?php echo do_shortcode($content); ?></p>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('cipit_accordion_item', 'cipit_acc_accordion_item_shortcode');


// ---------------------------------------------------------
// 3. CSS Styles
// ---------------------------------------------------------
function cipit_acc_get_css()
{
    return "
    .custom-accordion {
        max-width: 800px;
        margin: 0 auto;
    }

    .accordion-item {
        background: var(--light-gray, #f8f9fa);
        border: 1px solid #eee;
        border-radius: var(--border-radius, 12px);

        /* SUPER-COMPACT GAP */
        margin-bottom: 0.1875rem;

        overflow: hidden;
        box-shadow: var(--card-shadow, 0 4px 12px rgba(0, 0, 0, 0.05));
        transition: box-shadow .25s ease;
    }

    .accordion-item:hover {
        box-shadow: 0 4px 16px rgba(0,0,0,.08);
    }

    .accordion-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        width: 100%;
        padding: 1rem 1.4rem;
        font-size: 1.05rem;
        font-weight: 500;
        cursor: pointer;
        background: #fff;
        color: var(--secondary-color);
        border: none;
        text-align: left;
        transition: background .25s ease;
    }

    .accordion-item.active .accordion-header {
        background: var(--primary-color);
        color: #fff;
    }

    .accordion-icon {
        transition: transform .3s ease;
    }

    .accordion-item.active .accordion-icon {
        transform: rotate(180deg);
        color: #fff;
    }

    .accordion-content {
        background: var(--light-gray);
        padding: 0 1.4rem;
        max-height: 0;
        overflow: hidden;
        transition: max-height .35s ease-out, padding .35s ease-out;
    }

    /* OPEN STATE - NOTE: JS handles max-height */
    .accordion-item.active .accordion-content {
        padding: 1rem 1.4rem 1.2rem;
    }

    .accordion-content p {
        margin: 0;
        line-height: 1.45;
        font-size: 1rem;
        color: #444;
    }
";
}


// ---------------------------------------------------------
// 4. JavaScript Logic (With max-height padding fix)
// ---------------------------------------------------------
function cipit_acc_get_js()
{
    return "
jQuery(function($){

    // Define a reliable buffer (in pixels) to compensate for the vertical padding 
    // applied by CSS when the 'active' class is added.
    const PADDING_BUFFER = 50;

    $('.accordion-item').each(function(){

        var item = $(this);
        var header = item.find('.accordion-header');
        var content = item.find('.accordion-content');

        // Initial check for default-open items
        if(item.hasClass('default-open')){
            // Apply buffer for default-open items
            let h = content.prop('scrollHeight');
            content.css('max-height', h + PADDING_BUFFER + 'px');
        }

        header.on('click', function(){

            var isOpen = item.hasClass('active');

            // close all except this
            $('.accordion-item.active').not(item).each(function(){
                $(this).removeClass('active')
                       .find('.accordion-content').css('max-height','0');
            });

            item.toggleClass('active');

            if(isOpen){
                // CLOSE
                content.css('max-height', '0');
            } else {
                // OPEN
                let fullHeight = content.prop('scrollHeight');
                // FIX: Add the buffer to ensure the CSS padding is fully visible
                content.css('max-height', fullHeight + PADDING_BUFFER + 'px');
            }
        });

    });
});
";
}
?>