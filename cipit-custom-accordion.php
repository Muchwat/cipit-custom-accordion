<?php
/**
 * Plugin Name: Cipit Custom Accordion
 * Description: Accordion component for CIPIT website. Styled using the theme's Golden Ratio and Primary Color variables. Includes auto-open support and robust content padding fix. Source Code: <a href="https://github.com/Muchwat/cipit-custom-accordion" target="_blank">GitHub Repository</a>
 * Version: 1.6.1
 * Author: Kevin Muchwat
 * Author URI: https://github.com/Muchwat
 */

if (!defined('ABSPATH'))
    exit;

// Global counters for unique IDs
$cipit_accordion_counter = 0;
$cipit_acc_item_counter = 0;

// ---------------------------------------------------------
// 1. Enqueue Styles and Scripts
// ---------------------------------------------------------
function cipit_acc_enqueue_assets()
{
    // Font Awesome
    wp_enqueue_style(
        'cipit-acc-fontawesome',
        'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css',
        array(),
        '5.15.4'
    );

    // Inline CSS
    wp_register_style('cipit-acc-styles', false);
    wp_enqueue_style('cipit-acc-styles');
    wp_add_inline_style('cipit-acc-styles', cipit_acc_get_css());

    // Inline JS (depends on jQuery)
    wp_register_script('cipit-acc-script', false, array('jquery'), '1.6.1', true);
    wp_enqueue_script('cipit-acc-script');
    wp_add_inline_script('cipit-acc-script', cipit_acc_get_js());
}
add_action('wp_enqueue_scripts', 'cipit_acc_enqueue_assets');


// ---------------------------------------------------------
// 2. Shortcodes
// ---------------------------------------------------------
function cipit_acc_accordion_shortcode($atts, $content = null)
{
    global $cipit_accordion_counter;
    $cipit_accordion_counter++;

    $atts = shortcode_atts(array(
        'id' => 'cipit-accordion-' . $cipit_accordion_counter
    ), $atts);

    // Sanitize ID for use in HTML
    $id = sanitize_html_class($atts['id']);

    return '<div id="' . esc_attr($id) . '" class="custom-accordion">' . do_shortcode($content) . '</div>';
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

    $is_open = ($atts['open'] === 'true') ? ' active default-open' : '';
    $content_id = 'cipit-acc-content-' . $cipit_acc_item_counter;

    ob_start();
    ?>
    <div class="accordion-item<?php echo $is_open; ?>">
        <button class="accordion-header" aria-expanded="<?php echo ($atts['open'] === 'true') ? 'true' : 'false'; ?>"
            aria-controls="<?php echo $content_id; ?>">
            <span class="accordion-title"><?php echo esc_html($atts['title']); ?></span>
            <i class="fas fa-chevron-down accordion-icon"></i>
        </button>

        <div id="<?php echo $content_id; ?>" class="accordion-content">
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
// 4. JavaScript (with tab/visibility fix)
// ---------------------------------------------------------
function cipit_acc_get_js()
{
    return "
jQuery(function($){

    const PADDING_BUFFER = 50;

    // Initialize all accordions on page load
    function initAccordions(container) {
        const accordions = container ? $(container).find('.custom-accordion') : $('.custom-accordion');

        accordions.each(function(){
            const accordion = $(this);

            accordion.find('.accordion-item').each(function(){
                const item = $(this);
                const header = item.find('.accordion-header');
                const content = item.find('.accordion-content');

                // Initialize default-open items
                if(item.hasClass('default-open')){
                    const h = content.prop('scrollHeight');
                    content.css('max-height', h + PADDING_BUFFER + 'px');
                }

                // Click handler
                header.off('click.cipitAccordion').on('click.cipitAccordion', function(){
                    const isOpen = item.hasClass('active');

                    // Close others in same accordion
                    accordion.find('.accordion-item.active').not(item).each(function(){
                        $(this).removeClass('active')
                               .find('.accordion-content').css('max-height', '0');
                    });

                    item.toggleClass('active');
                    header.attr('aria-expanded', item.hasClass('active'));

                    if(isOpen){
                        content.css('max-height', '0');
                    } else {
                        const fullHeight = content.prop('scrollHeight');
                        content.css('max-height', fullHeight + PADDING_BUFFER + 'px');
                    }
                });
            });
        });
    }

    // Run on initial page load
    initAccordions();

    // Expose public refresh method for tabs, modals, etc.
    window.CipitAccordions = window.CipitAccordions || {};
    window.CipitAccordions.refresh = function(container) {
        // Re-initialize height for default-open items in visible state
        const targets = container ? $(container).find('.accordion-item.default-open.active') : $('.accordion-item.default-open.active');
        targets.each(function(){
            const content = $(this).find('.accordion-content');
            const h = content.prop('scrollHeight');
            content.css('max-height', h + PADDING_BUFFER + 'px');
        });
    };

    // Optional: auto-refresh if MutationObserver or tab events are used elsewhere
});
";
}