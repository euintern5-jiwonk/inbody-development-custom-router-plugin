# How to use
## Set up
Give read/write permission to .htaccess file
```bash
chmod 666 /Applications/XAMPP/xamppfiles/htdocs/wordpress/.htaccess
```

Check rewrite rules are applied in the admin page. If not, flush rewrite rules.

## Create SPA page
Elementor editor >> Page Settings ⚙️ >> Page Layout >> Choose 'SPA Page Template'

## SPA Link Creation in Wordpress Editor
Use the following format to create links within the Wordpress page.
```html
<a href="/sample-spa-page" data-spa-link data-page-slug="sample-spa-slug">
    Sample SPA Page
</a>
```

For pages with parent page.
```html
<a href="/parent-page/sample-spa-page" data-spa-link data-page-parent="parent-page" data-page-slug="sample-spa-slug">
    Sample SPA Page with Parent Page
</a>
```

# Bug
- slug recognized as parent, null is set as slug when navigating to previous link via back button (ex: https://development.inbody.com/wp-spa/load/sample-spa-page/null)