# CF7 Visual Styler - Hooks Documentation

This document describes the hooks available in CF7 Visual Styler that allow developers to extend the plugin with custom themes and features.

## Overview

CF7 Visual Styler provides two main hooks that enable third-party plugins (like CF7 Visual Styler Pro) to add custom themes and functionality:

1. **Filter Hook**: `cf7_styler_register_themes` - Register custom themes
2. **Action Hook**: `cf7_styler_enqueue_theme_styles` - Load custom theme styles

---

## Hook 1: `cf7_styler_register_themes` (Filter)

### Description
This filter allows you to add custom themes to the theme selection list in the admin panel.

### Usage

```php
add_filter( 'cf7_styler_register_themes', 'my_custom_themes' );

function my_custom_themes( $themes ) {
    $my_themes = array(
        'my-custom-theme' => array(
            'label' => __( 'My Custom Theme', 'my-text-domain' ),
            'desc'  => __( 'Description of my custom theme', 'my-text-domain' ),
            'is_pro' => false, // Optional: mark as pro theme
        ),
    );
    
    return array_merge( $themes, $my_themes );
}
```

### Parameters

- **`$themes`** (array): The existing array of registered themes

### Return Value

- **(array)**: The modified array of themes with your custom themes added

### Theme Array Structure

Each theme should be an associative array with the following keys:

| Key | Type | Required | Description |
|-----|------|----------|-------------|
| `label` | string | Yes | The display name of the theme |
| `desc` | string | Yes | A short description of the theme |
| `is_pro` | boolean | No | Whether this is a premium theme (default: false) |

### Example: Adding Multiple Themes

```php
add_filter( 'cf7_styler_register_themes', 'register_my_premium_themes' );

function register_my_premium_themes( $themes ) {
    $premium_themes = array(
        'neon-glow' => array(
            'label' => __( 'Neon Glow (Pro)', 'cf7-visual-styler-pro' ),
            'desc'  => __( 'Futuristic neon effects with vibrant colors.', 'cf7-visual-styler-pro' ),
            'is_pro' => true,
        ),
        'gradient-flow' => array(
            'label' => __( 'Gradient Flow (Pro)', 'cf7-visual-styler-pro' ),
            'desc'  => __( 'Smooth gradient backgrounds and modern aesthetics.', 'cf7-visual-styler-pro' ),
            'is_pro' => true,
        ),
    );
    
    return array_merge( $themes, $premium_themes );
}
```

---

## Hook 2: `cf7_styler_enqueue_theme_styles` (Action)

### Description
This action is triggered when a theme is selected on the frontend. Use this hook to load your custom theme's CSS file.

### Usage

```php
add_action( 'cf7_styler_enqueue_theme_styles', 'load_my_theme_styles' );

function load_my_theme_styles( $theme ) {
    // Check if this is your custom theme
    if ( 'my-custom-theme' === $theme ) {
        $theme_url = plugin_dir_url( __FILE__ ) . 'assets/css/my-custom-theme.css';
        $theme_path = plugin_dir_path( __FILE__ ) . 'assets/css/my-custom-theme.css';
        
        if ( file_exists( $theme_path ) ) {
            wp_enqueue_style( 'cf7-styler-my-custom-theme', $theme_url, array(), '1.0.0' );
        }
    }
}
```

### Parameters

- **`$theme`** (string): The slug of the currently selected theme

### Example: Loading Multiple Theme Styles

```php
add_action( 'cf7_styler_enqueue_theme_styles', 'load_pro_theme_styles' );

function load_pro_theme_styles( $theme ) {
    $pro_themes = array( 'neon-glow', 'gradient-flow', 'cyberpunk' );
    
    if ( in_array( $theme, $pro_themes ) ) {
        $theme_url = plugin_dir_url( __FILE__ ) . 'assets/css/' . $theme . '.css';
        $theme_path = plugin_dir_path( __FILE__ ) . 'assets/css/' . $theme . '.css';
        
        if ( file_exists( $theme_path ) ) {
            wp_enqueue_style( 'cf7-styler-pro-' . $theme, $theme_url, array(), '1.0.0' );
        }
    }
}
```

---

## Complete Example: Creating a Custom Theme Plugin

Here's a complete example of how to create a plugin that adds custom themes to CF7 Visual Styler:

```php
<?php
/**
 * Plugin Name: My Custom CF7 Themes
 * Description: Adds custom themes to CF7 Visual Styler
 * Version: 1.0.0
 * Author: Your Name
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class My_Custom_CF7_Themes {

    public static function get_instance() {
        static $instance = null;
        if ( null === $instance ) {
            $instance = new self();
        }
        return $instance;
    }

    private function __construct() {
        add_filter( 'cf7_styler_register_themes', array( $this, 'register_themes' ) );
        add_action( 'cf7_styler_enqueue_theme_styles', array( $this, 'enqueue_styles' ) );
    }

    public function register_themes( $themes ) {
        $my_themes = array(
            'my-awesome-theme' => array(
                'label' => __( 'My Awesome Theme', 'my-cf7-themes' ),
                'desc'  => __( 'A beautiful custom theme for your forms', 'my-cf7-themes' ),
            ),
        );
        
        return array_merge( $themes, $my_themes );
    }

    public function enqueue_styles( $theme ) {
        if ( 'my-awesome-theme' === $theme ) {
            $theme_url = plugin_dir_url( __FILE__ ) . 'assets/css/my-awesome-theme.css';
            $theme_path = plugin_dir_path( __FILE__ ) . 'assets/css/my-awesome-theme.css';
            
            if ( file_exists( $theme_path ) ) {
                wp_enqueue_style( 'cf7-styler-my-awesome-theme', $theme_url, array(), '1.0.0' );
            }
        }
    }
}

My_Custom_CF7_Themes::get_instance();
```

---

## Best Practices

1. **Use Unique Theme Slugs**: Always use unique, descriptive slugs for your themes to avoid conflicts.

2. **Prefix Your CSS Handles**: When enqueuing styles, use a unique prefix to avoid conflicts with other plugins.

3. **Check File Existence**: Always check if the CSS file exists before enqueuing it.

4. **Use Version Numbers**: Include version numbers when enqueuing styles for cache busting.

5. **Internationalization**: Use WordPress translation functions like `__()` and `_e()` for all user-facing strings.

6. **Conditional Loading**: Only load styles when your specific theme is selected to improve performance.

---

## Theme CSS Guidelines

When creating custom theme CSS, follow these guidelines:

1. **Target the Form Container**: Use `.wpcf7-form` as your main selector.

2. **Style All Form Elements**: Include styles for:
   - Labels: `.wpcf7-form label`
   - Inputs: `.wpcf7-form input[type="text"]`, `.wpcf7-form input[type="email"]`, etc.
   - Textareas: `.wpcf7-form textarea`
   - Selects: `.wpcf7-form select`
   - Submit Button: `.wpcf7-form input[type="submit"]`
   - Response Messages: `.wpcf7-form .wpcf7-response-output`
   - Validation Errors: `.wpcf7-form .wpcf7-not-valid-tip`

3. **Use Transitions**: Add smooth transitions for better user experience.

4. **Test Responsiveness**: Ensure your theme looks good on mobile devices.

---

## Troubleshooting

### Theme Not Showing in Admin

- Make sure you're using the correct filter hook: `cf7_styler_register_themes`
- Check that your plugin is activated
- Verify the theme array structure is correct

### Styles Not Loading on Frontend

- Make sure you're using the correct action hook: `cf7_styler_enqueue_theme_styles`
- Check that the CSS file path is correct
- Verify the theme slug matches between registration and enqueuing
- Clear your browser cache

### License Issues (Pro Themes)

- Ensure the license key is saved in the database
- Check that your license validation logic is working
- Verify that pro features are only loaded when license is valid

---

## Support

For questions or issues related to these hooks, please refer to the main plugin documentation or contact support.
