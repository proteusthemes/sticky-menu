# ProteusThemes Sticky Menu #

This is a composer package for the PT Sticky Menu, also available on the packagist repository: https://packagist.org/packages/proteusthemes/pt-sticky-menu.

## Instructions ##

1. require this package in your composer.json file: "proteusthemes/pt-sticky-menu"
2. run `composer update`
3. import the basic SCSS file into the theme (in style.scss file):
`@import '../../vendor/proteusthemes/pt-sticky-menu/assets/scss/sticky-menu-minimal';`
4. insert `js-sticky-mobile-option` and `js-sticky-desktop-option` classes, to the HTML elements, from which the sticky menu will be active. Example: add `js-sticky-mobile-option` class to hamburger button and add the `js-sticky-desktop-option` class to the nav element in header. When the browser window top will scroll to these elements, the sticky menu will be activated.

That should be it. Now you just have to style the sticky menu according to the theme style.

## Filters ##

There are a few filters, which you can use to modify the behavior of the sticky menu:

- `pt-sticky-menu/settings_default`, used for specifying the default customizer settings,
- `pt-sticky-menu/logo_mod_names`, change the theme_mod names for the logos,
- `pt-sticky-menu/theme_menu_location`, change menu location name used for the sticky menu,
- `pt-sticky-menu/cta_html_output`, change the CTA HTML output,
- `pt-sticky-menu/theme_panel`, change the customizer panel name, to which the Sticky Menu section will be attached.