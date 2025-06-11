<?php
/**
 * Fix for typography controls in customizer preview
 * This ensures specific heading controls (h1, h2, etc.) take precedence over the base heading control
 */

/**
 * Modified dynamic typography CSS output function
 * This prioritizes specific element controls over general controls
 */
function polysaas_dynamic_typography_css() {
    $typography_css = '';
    
    // Get all theme mods
    $theme_mods = get_theme_mods();
    
    // Define patterns to match typography settings with their specificity level
    // Higher number means more specific (will be applied later to override general styles)
    $patterns = [
        'polysaas_body_text_typography' => ['selector' => 'body', 'specificity' => 1],
        'polysaas_heading_text_typography' => ['selector' => 'h1, h2, h3, h4, h5, h6', 'specificity' => 1],
        'polysaas_heading_h1_typography' => ['selector' => 'h1', 'specificity' => 2],
        'polysaas_heading_h2_typography' => ['selector' => 'h2', 'specificity' => 2],
        'polysaas_heading_h3_typography' => ['selector' => 'h3', 'specificity' => 2],
        'polysaas_heading_h4_typography' => ['selector' => 'h4', 'specificity' => 2],
        'polysaas_heading_h5_typography' => ['selector' => 'h5', 'specificity' => 2],
        'polysaas_heading_h6_typography' => ['selector' => 'h6', 'specificity' => 2],
        'polysaas_buttons_typography' => ['selector' => 'button, .uc-button, .btn, input[type="button"], input[type="submit"]', 'specificity' => 2],
        'polysaas_links_typography' => ['selector' => 'a', 'specificity' => 2],
        'polysaas_menu_base_typography' => ['selector' => '.uc-navbar', 'specificity' => 2],
        'polysaas_menu_typography' => ['selector' => '.uc-navbar .menu-item > a', 'specificity' => 2],
        'polysaas_submenu_typography' => ['selector' => '.uc-navbar .uc-nav .menu-item > a', 'specificity' => 2],
        'polysaas_blog_post_content_typography' => ['selector' => '.single-post .entry-content', 'specificity' => 2],
        'polysaas_blog_post_meta_typography' => ['selector' => '.single-post .entry-meta, .single-post .entry-footer', 'specificity' => 2],
        'polysaas_blog_archive_title_typography' => ['selector' => '.blog .entry-title, .archive .entry-title', 'specificity' => 2],
        'polysaas_blog_archive_excerpt_typography' => ['selector' => '.blog .entry-summary, .archive .entry-summary', 'specificity' => 2],
        'polysaas_blog_comments_title_typography' => ['selector' => '.comments-title, .comment-reply-title', 'specificity' => 2],
        'polysaas_blog_comments_content_typography' => ['selector' => '.comment-body, .comment-metadata, .comment-author', 'specificity' => 2],
    ];
    
    // Temp storage for typography settings
    $typography_settings = [];
    
    // Find all typography settings
    foreach ($theme_mods as $setting_id => $value) {
        // Skip if not a string (typography values are stored as JSON strings)
        if (!is_string($value)) {
            continue;
        }
        
        // Check if this matches one of our known patterns
        $selector = null;
        $specificity = 1; // Default specificity
        
        foreach ($patterns as $pattern => $pattern_data) {
            if ($pattern === $setting_id) {
                $selector = $pattern_data['selector'];
                $specificity = $pattern_data['specificity'];
                break;
            }
        }
        
        // If we didn't find a match but it contains '_typography'
        if (!$selector && strpos($setting_id, '_typography') !== false) {
            // Try to guess the selector from the setting ID
            if (strpos($setting_id, 'body_') !== false) {
                $selector = 'body';
            } elseif (strpos($setting_id, 'heading_h1_') !== false) {
                $selector = 'h1';
                $specificity = 2; // More specific
            } elseif (strpos($setting_id, 'heading_h2_') !== false) {
                $selector = 'h2';
                $specificity = 2;
            } elseif (strpos($setting_id, 'heading_h3_') !== false) {
                $selector = 'h3';
                $specificity = 2;
            } elseif (strpos($setting_id, 'heading_h4_') !== false) {
                $selector = 'h4';
                $specificity = 2;
            } elseif (strpos($setting_id, 'heading_h5_') !== false) {
                $selector = 'h5';
                $specificity = 2;
            } elseif (strpos($setting_id, 'heading_h6_') !== false) {
                $selector = 'h6';
                $specificity = 2;
            } elseif (strpos($setting_id, 'button_') !== false) {
                $selector = 'button, .button, input[type="button"], input[type="submit"]';
                $specificity = 2;
            } elseif (strpos($setting_id, 'link_') !== false) {
                $selector = 'a';
                $specificity = 2;
            }
        }
        
        // Skip if we couldn't determine a selector
        if (!$selector) {
            continue;
        }
        
        // Try to decode the value
        $settings = json_decode($value, true);
        if (!$settings || !is_array($settings)) {
            continue;
        }
        
        // Store the setting with its specificity for later processing
        $typography_settings[] = [
            'selector' => $selector,
            'settings' => $settings,
            'specificity' => $specificity,
            'setting_id' => $setting_id
        ];
    }
    
    // Sort settings by specificity (lower specificity first, higher specificity later)
    // This ensures specific controls override general controls
    usort($typography_settings, function($a, $b) {
        return $a['specificity'] - $b['specificity'];
    });
    
    // Now process the sorted settings
    foreach ($typography_settings as $setting) {
        $selector = $setting['selector'];
        $settings = $setting['settings'];
        $setting_id = $setting['setting_id'];
        
        // Process desktop settings
        if (isset($settings['desktop'])) {
            $desktop = $settings['desktop'];
            
            // Add main styles
            $typography_css .= "$selector {";
            if (!empty($desktop['font_family'])) $typography_css .= "font-family: {$desktop['font_family']} !important;";
            if (!empty($desktop['font_size'])) $typography_css .= "font-size: {$desktop['font_size']}px;";
            if (!empty($desktop['font_weight'])) $typography_css .= "font-weight: {$desktop['font_weight']};";
            if (!empty($desktop['line_height'])) $typography_css .= "line-height: {$desktop['line_height']};";
            if (!empty($desktop['letter_spacing'])) $typography_css .= "letter-spacing: {$desktop['letter_spacing']}px;";
            if (!empty($desktop['text_transform'])) $typography_css .= "text-transform: {$desktop['text_transform']};";
            if (!empty($desktop['font_style'])) $typography_css .= "font-style: {$desktop['font_style']};";
            $typography_css .= "}";
            
            // Special handling for headings for base heading control only
            if ($selector === 'h1, h2, h3, h4, h5, h6' && isset($desktop['font_size']) && $setting_id === 'polysaas_heading_text_typography') {
                $base_size = intval($desktop['font_size']);
                
                // Set scaled heading sizes only if specific heading controls don't exist
                if (!array_key_exists('polysaas_heading_h1_typography', $theme_mods)) {
                    $typography_css .= "h1 { font-size: " . ($base_size * 2.5) . "px; }";
                }
                if (!array_key_exists('polysaas_heading_h2_typography', $theme_mods)) {
                    $typography_css .= "h2 { font-size: " . ($base_size * 2) . "px; }";
                }
                if (!array_key_exists('polysaas_heading_h3_typography', $theme_mods)) {
                    $typography_css .= "h3 { font-size: " . ($base_size * 1.75) . "px; }";
                }
                if (!array_key_exists('polysaas_heading_h4_typography', $theme_mods)) {
                    $typography_css .= "h4 { font-size: " . ($base_size * 1.5) . "px; }";
                }
                if (!array_key_exists('polysaas_heading_h5_typography', $theme_mods)) {
                    $typography_css .= "h5 { font-size: " . ($base_size * 1.25) . "px; }";
                }
                if (!array_key_exists('polysaas_heading_h6_typography', $theme_mods)) {
                    $typography_css .= "h6 { font-size: " . $base_size . "px; }";
                }
            }
        }
        
        // Process tablet styles
        if (isset($settings['tablet'])) {
            $tablet = $settings['tablet'];
            
            $typography_css .= "@media (max-width: 991px) {";
            $typography_css .= "$selector {";
            if (!empty($tablet['font_size'])) $typography_css .= "font-size: {$tablet['font_size']}px;";
            if (!empty($tablet['line_height'])) $typography_css .= "line-height: {$tablet['line_height']};";
            if (!empty($tablet['letter_spacing'])) $typography_css .= "letter-spacing: {$tablet['letter_spacing']}px;";
            $typography_css .= "}}";
            
            // Special handling for headings at tablet size, only for base heading control
            if ($selector === 'h1, h2, h3, h4, h5, h6' && isset($tablet['font_size']) && $setting_id === 'polysaas_heading_text_typography') {
                $tablet_base = intval($tablet['font_size']);
                $typography_css .= "@media (max-width: 991px) {";
                
                // Apply only if specific controls don't exist
                if (!array_key_exists('polysaas_heading_h1_typography', $theme_mods)) {
                    $typography_css .= "h1 { font-size: " . ($tablet_base * 2.2) . "px; }";
                }
                if (!array_key_exists('polysaas_heading_h2_typography', $theme_mods)) {
                    $typography_css .= "h2 { font-size: " . ($tablet_base * 1.8) . "px; }";
                }
                if (!array_key_exists('polysaas_heading_h3_typography', $theme_mods)) {
                    $typography_css .= "h3 { font-size: " . ($tablet_base * 1.6) . "px; }";
                }
                if (!array_key_exists('polysaas_heading_h4_typography', $theme_mods)) {
                    $typography_css .= "h4 { font-size: " . ($tablet_base * 1.4) . "px; }";
                }
                if (!array_key_exists('polysaas_heading_h5_typography', $theme_mods)) {
                    $typography_css .= "h5 { font-size: " . ($tablet_base * 1.2) . "px; }";
                }
                if (!array_key_exists('polysaas_heading_h6_typography', $theme_mods)) {
                    $typography_css .= "h6 { font-size: " . $tablet_base . "px; }";
                }
                
                $typography_css .= "}";
            }
        }
        
        // Process mobile styles
        if (isset($settings['mobile'])) {
            $mobile = $settings['mobile'];
            
            $typography_css .= "@media (max-width: 479px) {";
            $typography_css .= "$selector {";
            if (!empty($mobile['font_size'])) $typography_css .= "font-size: {$mobile['font_size']}px;";
            if (!empty($mobile['line_height'])) $typography_css .= "line-height: {$mobile['line_height']};";
            if (!empty($mobile['letter_spacing'])) $typography_css .= "letter-spacing: {$mobile['letter_spacing']}px;";
            $typography_css .= "}}";
            
            // Special handling for headings at mobile size if needed, only for base heading control
            if ($selector === 'h1, h2, h3, h4, h5, h6' && isset($mobile['font_size']) && $setting_id === 'polysaas_heading_text_typography') {
                $mobile_base = intval($mobile['font_size']);
                $typography_css .= "@media (max-width: 479px) {";
                
                // Apply only if specific controls don't exist
                if (!array_key_exists('polysaas_heading_h1_typography', $theme_mods)) {
                    $typography_css .= "h1 { font-size: " . ($mobile_base * 2.0) . "px; }";
                }
                if (!array_key_exists('polysaas_heading_h2_typography', $theme_mods)) {
                    $typography_css .= "h2 { font-size: " . ($mobile_base * 1.7) . "px; }";
                }
                if (!array_key_exists('polysaas_heading_h3_typography', $theme_mods)) {
                    $typography_css .= "h3 { font-size: " . ($mobile_base * 1.5) . "px; }";
                }
                if (!array_key_exists('polysaas_heading_h4_typography', $theme_mods)) {
                    $typography_css .= "h4 { font-size: " . ($mobile_base * 1.3) . "px; }";
                }
                if (!array_key_exists('polysaas_heading_h5_typography', $theme_mods)) {
                    $typography_css .= "h5 { font-size: " . ($mobile_base * 1.15) . "px; }";
                }
                if (!array_key_exists('polysaas_heading_h6_typography', $theme_mods)) {
                    $typography_css .= "h6 { font-size: " . $mobile_base . "px; }";
                }
                
                $typography_css .= "}";
            }
        }
    }
    
    // Output the CSS if we have any
    if (!empty($typography_css)) {
        echo '<style id="polysaas-typography-css">' . $typography_css . '</style>';
    }
}
add_action('wp_head', 'polysaas_dynamic_typography_css', 20);

/**
 * Improved preview JS that handles all typography controls dynamically
 * This version also respects specificity of selectors
 */
function polysaas_typography_preview() {
    if (!is_customize_preview()) {
        return;
    }
    
    $typography_controls = [
        'polysaas_body_text_typography' => ['selector' => 'body', 'specificity' => 1],
        'polysaas_heading_text_typography' => ['selector' => 'h1, h2, h3, h4, h5, h6', 'specificity' => 1],
        'polysaas_heading_h1_typography' => ['selector' => 'h1', 'specificity' => 2],
        'polysaas_heading_h2_typography' => ['selector' => 'h2', 'specificity' => 2],
        'polysaas_heading_h3_typography' => ['selector' => 'h3', 'specificity' => 2],
        'polysaas_heading_h4_typography' => ['selector' => 'h4', 'specificity' => 2],
        'polysaas_heading_h5_typography' => ['selector' => 'h5', 'specificity' => 2],
        'polysaas_heading_h6_typography' => ['selector' => 'h6', 'specificity' => 2],
        'polysaas_buttons_typography' => ['selector' => 'button, .uc-button, .btn, input[type="button"], input[type="submit"]', 'specificity' => 2],
        'polysaas_links_typography' => ['selector' => 'a', 'specificity' => 2],
        'polysaas_menu_base_typography' => ['selector' => '.uc-navbar', 'specificity' => 2],
        'polysaas_menu_typography' => ['selector' => '.uc-navbar .menu-item > a', 'specificity' => 2],
        'polysaas_submenu_typography' => ['selector' => '.uc-navbar .uc-nav .menu-item > a', 'specificity' => 2],
        'polysaas_blog_post_content_typography' => ['selector' => '.single-post .entry-content', 'specificity' => 2],
        'polysaas_blog_post_meta_typography' => ['selector' => '.single-post .entry-meta, .single-post .entry-footer', 'specificity' => 2],
        'polysaas_blog_archive_title_typography' => ['selector' => '.blog .entry-title, .archive .entry-title', 'specificity' => 2],
        'polysaas_blog_archive_excerpt_typography' => ['selector' => '.blog .entry-summary, .archive .entry-summary', 'specificity' => 2],
        'polysaas_blog_comments_title_typography' => ['selector' => '.comments-title, .comment-reply-title', 'specificity' => 2],
        'polysaas_blog_comments_content_typography' => ['selector' => '.comment-body, .comment-metadata, .comment-author', 'specificity' => 2],
    ];
    
    // Allow themes and plugins to add more controls
    $typography_controls = apply_filters('polysaas_typography_preview_controls', $typography_controls);
    
    // Pass the controls to our JavaScript
    add_action('wp_footer', function() use ($typography_controls) {
        ?>
        <script>
        (function($) {
            'use strict';
                        
            // Map of typography controls and their selectors/specificity
            var typographyControls = <?php echo json_encode($typography_controls); ?>;
                        
            // Wait for customizer to be ready
            wp.customize.bind('preview-ready', function() {
                console.log('Customizer preview is ready');
                
                // Create a dedicated style element for our dynamic styles
                var $styleElement = $('#polysaas-typography-dynamic-css');
                if ($styleElement.length === 0) {
                    $styleElement = $('<style id="polysaas-typography-dynamic-css"></style>').appendTo('head');
                }
                
                // Storage for all applied styles with their specificity
                var appliedStyles = {};
                
                // Function to apply typography styles
                function applyTypography(settingId, selector, specificity, value) {
                    try {
                        // Parse value if needed
                        var settings = (typeof value === 'string') ? JSON.parse(value) : value;
                        
                        if (!settings || !settings.desktop) {
                            return;
                        }
                        
                        // Store the styles with their specificity
                        if (!appliedStyles[selector]) {
                            appliedStyles[selector] = [];
                        }
                        
                        // Remove any existing styles for this setting ID
                        appliedStyles[selector] = appliedStyles[selector].filter(function(style) {
                            return style.settingId !== settingId;
                        });
                        
                        // Add the new styles
                        appliedStyles[selector].push({
                            settingId: settingId,
                            specificity: specificity,
                            settings: settings,
                            selector: selector
                        });
                        
                        // Regenerate all CSS
                        regenerateAllCSS();
                    } catch (e) {
                        console.error('Error applying typography:', e);
                    }
                }
                
                // Function to regenerate all CSS from stored styles
                function regenerateAllCSS() {
                    var css = '';
                    
                    // Process all selectors
                    for (var selector in appliedStyles) {
                        if (appliedStyles.hasOwnProperty(selector)) {
                            // Sort by specificity (low to high)
                            appliedStyles[selector].sort(function(a, b) {
                                return a.specificity - b.specificity;
                            });
                            
                            // Generate CSS for this selector from all applicable styles
                            css += generateSelectorCSS(selector, appliedStyles[selector]);
                        }
                    }
                    
                    // Update the style element
                    $styleElement.html(css);
                }
                
                // Function to generate CSS for a specific selector
                function generateSelectorCSS(selector, styles) {
                    var css = '';
                    var processedProperties = {};
                    
                    // Process styles in order of specificity (low to high)
                    // This ensures more specific styles override less specific ones
                    styles.forEach(function(style) {
                        var settings = style.settings;
                        
                        // Desktop styles
                        if (settings.desktop) {
                            var desktop = settings.desktop;
                            var desktopCSS = '';
                            
                            // Only add properties that haven't been set by a more specific rule
                            // or override existing properties if this is more specific
                            if (desktop.font_family) {
                                processedProperties['font-family'] = true;
                                desktopCSS += 'font-family:' + desktop.font_family + ' !important;';
                            }
                            
                            if (desktop.font_size) {
                                processedProperties['font-size'] = true;
                                desktopCSS += 'font-size:' + desktop.font_size + 'px;';
                            }
                            
                            if (desktop.font_weight) {
                                processedProperties['font-weight'] = true;
                                desktopCSS += 'font-weight:' + desktop.font_weight + ';';
                            }
                            
                            if (desktop.line_height) {
                                processedProperties['line-height'] = true;
                                desktopCSS += 'line-height:' + desktop.line_height + ';';
                            }
                            
                            if (desktop.letter_spacing) {
                                processedProperties['letter-spacing'] = true;
                                desktopCSS += 'letter-spacing:' + desktop.letter_spacing + 'px;';
                            }
                            
                            if (desktop.text_transform) {
                                processedProperties['text-transform'] = true;
                                desktopCSS += 'text-transform:' + desktop.text_transform + ';';
                            }
                            
                            if (desktop.font_style) {
                                processedProperties['font-style'] = true;
                                desktopCSS += 'font-style:' + desktop.font_style + ';';
                            }
                            
                            // Add CSS only if we have properties to add
                            if (desktopCSS) {
                                css += selector + '{' + desktopCSS + '}';
                            }
                            
                            // Special handling for headings base control
                            if (selector === 'h1, h2, h3, h4, h5, h6' && desktop.font_size && style.settingId === 'polysaas_heading_text_typography') {
                                var baseSize = parseInt(desktop.font_size);
                                
                                // Check if specific controls exist before applying scaled sizes
                                if (!wp.customize('polysaas_heading_h1_typography')) {
                                    css += 'h1 { font-size: ' + (baseSize * 2.5) + 'px; }';
                                }
                                if (!wp.customize('polysaas_heading_h2_typography')) {
                                    css += 'h2 { font-size: ' + (baseSize * 2) + 'px; }';
                                }
                                if (!wp.customize('polysaas_heading_h3_typography')) {
                                    css += 'h3 { font-size: ' + (baseSize * 1.75) + 'px; }';
                                }
                                if (!wp.customize('polysaas_heading_h4_typography')) {
                                    css += 'h4 { font-size: ' + (baseSize * 1.5) + 'px; }';
                                }
                                if (!wp.customize('polysaas_heading_h5_typography')) {
                                    css += 'h5 { font-size: ' + (baseSize * 1.25) + 'px; }';
                                }
                                if (!wp.customize('polysaas_heading_h6_typography')) {
                                    css += 'h6 { font-size: ' + baseSize + 'px; }';
                                }
                            }
                        }
                        
                        // Tablet styles
                        if (settings.tablet) {
                            var tablet = settings.tablet;
                            var tabletCSS = '';
                            var tabletProcessedProperties = {};
                            
                            if (tablet.font_size) {
                                tabletProcessedProperties['font-size'] = true;
                                tabletCSS += 'font-size:' + tablet.font_size + 'px;';
                            }
                            
                            if (tablet.line_height) {
                                tabletProcessedProperties['line-height'] = true;
                                tabletCSS += 'line-height:' + tablet.line_height + ';';
                            }
                            
                            if (tablet.letter_spacing) {
                                tabletProcessedProperties['letter-spacing'] = true;
                                tabletCSS += 'letter-spacing:' + tablet.letter_spacing + 'px;';
                            }
                            
                            if (tabletCSS) {
                                css += '@media (max-width: 991px) {' + selector + '{' + tabletCSS + '}}';
                            }
                            
                            // Special handling for headings base control
                            if (selector === 'h1, h2, h3, h4, h5, h6' && tablet.font_size && style.settingId === 'polysaas_heading_text_typography') {
                                var tabletBase = parseInt(tablet.font_size);
                                var tabletHeadingCSS = '@media (max-width: 991px) {';
                                
                                // Only apply if specific heading controls don't exist
                                if (!wp.customize('polysaas_heading_h1_typography')) {
                                    tabletHeadingCSS += 'h1 { font-size: ' + (tabletBase * 2.2) + 'px; }';
                                }
                                if (!wp.customize('polysaas_heading_h2_typography')) {
                                    tabletHeadingCSS += 'h2 { font-size: ' + (tabletBase * 1.8) + 'px; }';
                                }
                                if (!wp.customize('polysaas_heading_h3_typography')) {
                                    tabletHeadingCSS += 'h3 { font-size: ' + (tabletBase * 1.6) + 'px; }';
                                }
                                if (!wp.customize('polysaas_heading_h4_typography')) {
                                    tabletHeadingCSS += 'h4 { font-size: ' + (tabletBase * 1.4) + 'px; }';
                                }
                                if (!wp.customize('polysaas_heading_h5_typography')) {
                                    tabletHeadingCSS += 'h5 { font-size: ' + (tabletBase * 1.2) + 'px; }';
                                }
                                if (!wp.customize('polysaas_heading_h6_typography')) {
                                    tabletHeadingCSS += 'h6 { font-size: ' + tabletBase + 'px; }';
                                }
                                
                                tabletHeadingCSS += '}';
                                css += tabletHeadingCSS;
                            }
                        }
                        
                        // Mobile styles
                        if (settings.mobile) {
                            var mobile = settings.mobile;
                            var mobileCSS = '';
                            var mobileProcessedProperties = {};
                            
                            if (mobile.font_size) {
                                mobileProcessedProperties['font-size'] = true;
                                mobileCSS += 'font-size:' + mobile.font_size + 'px;';
                            }
                            
                            if (mobile.line_height) {
                                mobileProcessedProperties['line-height'] = true;
                                mobileCSS += 'line-height:' + mobile.line_height + ';';
                            }
                            
                            if (mobile.letter_spacing) {
                                mobileProcessedProperties['letter-spacing'] = true;
                                mobileCSS += 'letter-spacing:' + mobile.letter_spacing + 'px;';
                            }
                            
                            if (mobileCSS) {
                                css += '@media (max-width: 479px) {' + selector + '{' + mobileCSS + '}}';
                            }
                            
                            // Special handling for headings base control
                            if (selector === 'h1, h2, h3, h4, h5, h6' && mobile.font_size && style.settingId === 'polysaas_heading_text_typography') {
                                var mobileBase = parseInt(mobile.font_size);
                                var mobileHeadingCSS = '@media (max-width: 479px) {';
                                
                                // Only apply if specific heading controls don't exist
                                if (!wp.customize('polysaas_heading_h1_typography')) {
                                    mobileHeadingCSS += 'h1 { font-size: ' + (mobileBase * 2.0) + 'px; }';
                                }
                                if (!wp.customize('polysaas_heading_h2_typography')) {
                                    mobileHeadingCSS += 'h2 { font-size: ' + (mobileBase * 1.7) + 'px; }';
                                }
                                if (!wp.customize('polysaas_heading_h3_typography')) {
                                    mobileHeadingCSS += 'h3 { font-size: ' + (mobileBase * 1.5) + 'px; }';
                                }
                                if (!wp.customize('polysaas_heading_h4_typography')) {
                                    mobileHeadingCSS += 'h4 { font-size: ' + (mobileBase * 1.3) + 'px; }';
                                }
                                if (!wp.customize('polysaas_heading_h5_typography')) {
                                    mobileHeadingCSS += 'h5 { font-size: ' + (mobileBase * 1.15) + 'px; }';
                                }
                                if (!wp.customize('polysaas_heading_h6_typography')) {
                                    mobileHeadingCSS += 'h6 { font-size: ' + mobileBase + 'px; }';
                                }
                                
                                mobileHeadingCSS += '}';
                                css += mobileHeadingCSS;
                            }
                        }
                    });
                    
                    return css;
                }
                
                // Set up all typography controls dynamically
                Object.keys(typographyControls).forEach(function(settingId) {
                    var controlData = typographyControls[settingId];
                    var selector = typeof controlData === 'string' ? controlData : controlData.selector;
                    var specificity = typeof controlData === 'string' ? 1 : (controlData.specificity || 1);
                    
                    setupTypographyControl(settingId, selector, specificity);
                });
                
                // Function to set up a typography control
                function setupTypographyControl(settingId, selector, specificity) {
                    if (wp.customize(settingId)) {
                        wp.customize(settingId, function(value) {
                            // Apply initial value
                            var initialValue = value.get();
                            if (initialValue) {
                                applyTypography(settingId, selector, specificity, initialValue);
                            }
                            
                            // Set up binding for changes
                            value.bind(function(newValue) {
                                applyTypography(settingId, selector, specificity, newValue);
                            });
                        });
                    }
                }
                
                // If using previewedDevice, listen for changes
                if (wp.customize.previewedDevice && typeof wp.customize.previewedDevice.bind === 'function') {
                    wp.customize.previewedDevice.bind(function(device) {
                        console.log('Device changed to:', device);
                        
                        // Re-generate all CSS when device changes
                        regenerateAllCSS();
                    });
                }
            });
        })(jQuery);
        </script>
        <?php
    }, 20);
}
add_action('wp_enqueue_scripts', 'polysaas_typography_preview');

/*
 * Force postMessage transport for typography controls
 */
function polysaas_force_typography_transport($args) {
    if (isset($args['type']) && $args['type'] === 'polysaas-typography-popup') {
        // Force postMessage for live preview
        $args['transport'] = 'postMessage';
    }
    return $args;
}
add_filter('kirki_field_add_setting_args', 'polysaas_force_typography_transport', 99);