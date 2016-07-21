# ProteusThemes Sticky Menu #

This is a composer package for the PT Sticky Menu, also available on the packagist repository: https://packagist.org/packages/proteusthemes/sticky-menu.

## How it works ##

In customizer under *Theme Options -> Sticky menu* you will find the settings for the sticky menu:

- enable/disable sticky menu checkbox,
- featured page button settings,
- sticky menu container background color.

The sticky menu will be displayed only when the user scrolls up, otherwise it will remain hidden.

## Instructions ##

1. require this package in your composer.json file: "proteusthemes/sticky-menu",
2. run `composer update`,
3. import the basic SCSS file into the theme (in style.scss file):
`@import '../../vendor/proteusthemes/sticky-menu/assets/scss/sticky-menu-minimal';`,
4. require the js file in the theme (in main.js file). Just add this path to the `require` call: `'vendor/proteusthemes/sticky-menu/assets/js/sticky-menu'`,
5. run `grunt build`, to generate new style.css file (with sticky menu css),
6. insert `js-sticky-mobile-option` and `js-sticky-desktop-option` classes, to the HTML elements, from which the sticky menu will be active. Example: add `js-sticky-mobile-option` class to hamburger button and add the `js-sticky-desktop-option` class to the nav element in header. When the browser window top will scroll to these elements, the sticky menu will be activated,
7. use the `pt-sticky-menu/theme_panel` filter to set the panel, to which the sticky menu will be attached in the customizer,
8. instantiate the sticky menu class (take a look at the */inc/theme-sticky-menu.php* file in Auto theme, which is then required in functions.php)

That should be it. Now you just have to style the sticky menu according to the theme style.

## Filters ##

There are a few filters, which you can use to modify the behavior of the sticky menu:

- `pt-sticky-menu/settings_default`, used for specifying the default customizer settings,
- `pt-sticky-menu/logo_mod_names`, change the theme_mod names for the logos,
- `pt-sticky-menu/theme_menu_location`, change menu location name used for the sticky menu,
- `pt-sticky-menu/cta_html_output`, change the CTA HTML output,
- `pt-sticky-menu/theme_panel`, change the customizer panel name, to which the Sticky Menu section will be attached,
- `pt-sticky-menu/mobile_menu_button_class`, mobile menu button class, default is `btn-primary`,
- `pt-sticky-menu/cta_button_class`, CTA button class, default is `btn-primary`.
