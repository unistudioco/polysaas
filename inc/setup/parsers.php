<?php
/**
 * WordPress eXtended RSS file parser implementations
 *
 * @package WordPress
 * @subpackage Importer
 */

namespace PolySaaS\Setup\Parsers;

use WP_Error;

_deprecated_file( basename( __FILE__ ), '0.7.0' );

/** WXR_Parser class */
require_once __DIR__ . '/parsers/class-wxr-parser.php';

/** WXR_Parser_SimpleXML class */
require_once __DIR__ . '/parsers/class-wxr-parser-simplexml.php';

/** WXR_Parser_XML class */
require_once __DIR__ . '/parsers/class-wxr-parser-xml.php';

/** WXR_Parser_Regex class */
require_once __DIR__ . '/parsers/class-wxr-parser-regex.php';

// Alias the parser classes into the global namespace
if (!class_exists('WXR_Parser')) {
    class_alias('PolySaaS\\Setup\\Parsers\\WXR_Parser', 'WXR_Parser');
}
if (!class_exists('WXR_Parser_SimpleXML')) {
    class_alias('PolySaaS\\Setup\\Parsers\\WXR_Parser_SimpleXML', 'WXR_Parser_SimpleXML');
}
if (!class_exists('WXR_Parser_XML')) {
    class_alias('PolySaaS\\Setup\\Parsers\\WXR_Parser_XML', 'WXR_Parser_XML');
}
if (!class_exists('WXR_Parser_Regex')) {
    class_alias('PolySaaS\\Setup\\Parsers\\WXR_Parser_Regex', 'WXR_Parser_Regex');
}
