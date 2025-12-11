# 📂 Cipit Custom Accordion

[![GitHub Repository](https://img.shields.io/badge/GitHub-View%20Source-blue?style=flat&logo=github)](https://github.com/Muchwat/cipit-custom-accordion)  
[![WordPress Plugin](https://img.shields.io/badge/WordPress-Plugin-D54E21?style=flat&logo=wordpress)](https://developer.wordpress.org/plugins/)  
[![Version](https://img.shields.io/badge/Version-1.5.2-green.svg)](https://github.com/Muchwat/cipit-custom-accordion)

A custom-built, lightweight WordPress accordion component designed specifically for the CIPIT website theme. It is styled to seamlessly integrate with the site's design system, respecting the defined CSS variables for colors, spacing, and the Golden Ratio (`--gr`).

## ✨ Features

- **Theme Integration:** Automatically inherits colors (`--primary-color`, `--secondary-color`) and visual styles (`--border-radius`, `--card-shadow`) from the site's CSS `:root`.
- **Single-File Plugin:** Simple installation and activation via one PHP file (`cipit-custom-accordion.php`).
- **Exclusive Open:** When one accordion item opens, all others close automatically.
- **Auto-Open Support:** Includes an option to keep a specific item open on page load.
- **Robust Animation Fix:** Correctly handles dynamic padding during the open/close animation, preventing content cut-off.
- **Accessibility:** Includes `aria-expanded` and `aria-controls` attributes for improved screen reader compatibility.

## 🚀 Installation

1. **Download:** Download the `cipit-custom-accordion.php` file (or clone this repository).
2. **Upload:** Upload the `cipit-custom-accordion.php` file directly into your WordPress plugins directory (`/wp-content/plugins/`).
3. **Activate:** Navigate to the WordPress dashboard (Plugins → Installed Plugins) and activate **Cipit Custom Accordion**.

> ⚠️ **Note**: As of the latest check, [the GitHub repository](https://github.com/Muchwat/cipit-custom-accordion) appears to be empty. Please verify the source before downloading.

## 💻 Usage (Shortcodes)

Place the following shortcode structure into any post, page, or widget that supports shortcodes (e.g., using the WordPress "Shortcode" block).

### Main Accordion Wrapper

Use the main shortcode to wrap all accordion items:

```html
[cipit_accordion]
    <!-- Accordion items go here -->
[/cipit_accordion]
```


### Individual Accordion Item

Use the item shortcode for each question/answer pair.

| Attribute | Description                                              | Values                     |
|-----------|----------------------------------------------------------|----------------------------|
| `title`   | Required. The heading text for the accordion button.      | Any string                 |
| `open`    | Optional. Set to `true` to keep the item open on load.   | `true` or `false` (default) |


Example:

[cipit_accordion]
    [cipit_accordion_item title="What are the core functions of CIPIT?"]
        <p>The content area dynamically expands to reveal the answer. The max-height calculation ensures the bottom padding (1.2rem) is never cut off during the transition.</p>
        
        <ul>
            <li>Research</li>
            <li>Policy Advocacy</li>
        </ul>
    [/cipit_accordion_item]
    
    [cipit_accordion_item title="View Our Latest Report" open="true"]
        <p>This item is set to open by default using the <code>open="true"</code> attribute.</p>
    [/cipit_accordion_item]
[/cipit_accordion]
