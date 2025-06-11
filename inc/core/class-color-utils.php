<?php
namespace Polysaas\Core;

class Color_Utils {
    /**
     * Generate color shades
     */
    public static function generate_shades($hex_color) {
        $rgb = self::hex_to_rgb($hex_color);
        
        // Generate shades (mix with white for lighter shades, black for darker)
        return [
            '10'  => self::mix_with_white($rgb, 0.98),  // 98% white
            '25'  => self::mix_with_white($rgb, 0.95),  // 95% white
            '50'  => self::mix_with_white($rgb, 0.90),  // 90% white
            '100' => self::mix_with_white($rgb, 0.80),  // 80% white
            '200' => self::mix_with_white($rgb, 0.60),  // 60% white
            '300' => self::mix_with_white($rgb, 0.40),  // 40% white
            '400' => self::mix_with_white($rgb, 0.20),  // 20% white
            '500' => $rgb,                              // Original color
            '600' => self::mix_with_black($rgb, 0.20),  // 20% black
            '700' => self::mix_with_black($rgb, 0.40),  // 40% black
            '800' => self::mix_with_black($rgb, 0.60),  // 60% black
            '900' => self::mix_with_black($rgb, 0.80),  // 80% black
        ];
    }

    /**
     * Convert hex to RGB
     */
    private static function hex_to_rgb($hex) {
        $hex = ltrim($hex, '#');
        
        return [
            hexdec(substr($hex, 0, 2)), // Red
            hexdec(substr($hex, 2, 2)), // Green
            hexdec(substr($hex, 4, 2))  // Blue
        ];
    }

    /**
     * Mix color with white
     */
    private static function mix_with_white($rgb, $percentage) {
        return [
            round($rgb[0] + (255 - $rgb[0]) * $percentage),
            round($rgb[1] + (255 - $rgb[1]) * $percentage),
            round($rgb[2] + (255 - $rgb[2]) * $percentage)
        ];
    }

    /**
     * Mix color with black
     */
    private static function mix_with_black($rgb, $percentage) {
        return [
            round($rgb[0] * (1 - $percentage)),
            round($rgb[1] * (1 - $percentage)),
            round($rgb[2] * (1 - $percentage))
        ];
    }

    /**
     * Convert RGB array to CSS RGB string
     */
    public static function rgb_to_css($rgb) {
        return implode(', ', $rgb);
    }
}