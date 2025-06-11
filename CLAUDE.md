# POLYSAAS THEME DEVELOPMENT GUIDE

## Build & Development Commands
- `npm run watch` - Watch SASS files and compile with source maps
- `npm run compile:css` - Compile SASS to CSS and fix stylelint issues
- `npm run compile:rtl` - Generate RTL stylesheet
- `npm run lint:scss` - Lint SCSS files
- `npm run lint:js` - Lint JS files
- `npm run bundle` - Create distributable theme ZIP file

## Code Style Guidelines
- **PHP**: Follow WordPress Coding Standards (WPCS)
- **Naming**: All functions/classes should be prefixed with `polysaas_`
- **PHP Version**: Maintain compatibility with PHP 5.6+
- **WordPress Version**: Support WordPress 4.5+
- **Text Domain**: Use `polysaas` for all internationalization
- **Array Formatting**: Multi-line arrays should use WP array style with => alignment
- **Classes**: Use proper OOP with namespacing for new components
- **Error Handling**: Use WP error objects (`WP_Error`) for PHP errors
- **JS Standards**: Follow WordPress JS coding standards
- **CSS/SASS**: Use BEM methodology for component styling

This file contains guidelines for Claude to assist with code development for the PolySaaS theme.