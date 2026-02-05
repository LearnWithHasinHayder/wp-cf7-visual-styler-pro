# CF7 Visual Styler Pro

A premium extension for CF7 Visual Styler that adds beautiful premium themes to your Contact Form 7 forms.

## Features

- **3 Premium Themes**: Neon Glow, Gradient Flow, and Cyberpunk
- **License Validation**: Secure license key system to protect premium features
- **Easy Integration**: Seamlessly integrates with the free CF7 Visual Styler plugin
- **Simple Setup**: Just activate your license and start using premium themes

## Installation

1. Upload the `cf-style-pro` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to **Contact > Pro License** and enter your license key
4. Go to **Contact > Form Styles** to select a premium theme

## License Activation

The pro plugin requires a valid license key to function. Without a license key, premium themes will not be available.

**For classroom demo purposes:** Any non-empty license key will be accepted.

### How to Activate

1. Navigate to **Contact > Pro License** in your WordPress admin
2. Enter your license key in the input field
3. Click "Activate License"
4. Premium themes will now be available in **Contact > Form Styles**

## Premium Themes

### Neon Glow
A futuristic theme with vibrant neon colors and glowing effects. Perfect for modern, tech-focused websites.

### Gradient Flow
A smooth gradient theme with modern aesthetics and elegant color transitions. Great for creative and design-focused sites.

### Cyberpunk
A dark theme with neon accents and tech-inspired design. Ideal for gaming, technology, or edgy websites.

## Plugin Structure

```
cf-style-pro/
├── cf-style-pro.php          # Main plugin file
├── assets/
│   └── css/
│       ├── neon-glow.css      # Neon Glow theme styles
│       ├── gradient-flow.css  # Gradient Flow theme styles
│       └── cyberpunk.css      # Cyberpunk theme styles
└── README.md                 # This file
```

## How It Works

The pro plugin uses WordPress hooks to extend the free CF7 Visual Styler plugin:

1. **`cf7_styler_register_themes`** filter - Adds premium themes to the theme list
2. **`cf7_styler_enqueue_theme_styles`** action - Loads premium theme CSS files

When a user selects a premium theme, the pro plugin detects this and loads the appropriate CSS file.

## License Validation

The plugin checks for a valid license key before loading premium features:

```php
public function is_license_valid() {
    $license_key = get_option( 'cf7_styler_pro_license_key', '' );
    return ! empty( $license_key );
}
```

**Note:** For this classroom demo, any non-empty license key is considered valid. In a production environment, you would implement server-side validation.

## Admin Pages

### Pro License Page
Located at **Contact > Pro License**, this page allows users to:
- Enter and save their license key
- View license activation status

### Pro Features Page
Located at **Contact > Pro Features**, this page shows:
- Available premium themes
- License information
- Links to manage license

### Pro Pricing Page
Located at **Contact > Pro Pricing**, this page displays:
- Pricing plans (Personal, Business, Agency)
- Feature comparison
- Call-to-action buttons

## Requirements

- WordPress 5.0 or higher
- Contact Form 7 plugin
- CF7 Visual Styler (free version) plugin

## Support

For support, please visit our website or contact our support team.

## Changelog

### 1.0.0
- Initial release
- 3 premium themes: Neon Glow, Gradient Flow, Cyberpunk
- License validation system
- Admin pages for license, features, and pricing

## License

This plugin is proprietary software. A valid license key is required to use premium features.
