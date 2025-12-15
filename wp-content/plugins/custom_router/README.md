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

### Using JavaScript to Navigate to Pages
Use the following format to navigate to pages using the public API. (Also available in jQuery)
```js
var target_link_element = document.querySelector(".target-link a");
var button = document.querySelector("button");

button.addEventListener("click", function (e) {
    var parent = target_link_element.dataset.pageParent;
    var slug = target_link_element.dataset.pageSlug;

    if (!parent || parent.length === 0) {
        // navigating to single page
        SPARouter.navigate(slug);
    } else {
        // navigating to subpage
        SPARouter.navigate(parent, slug);

        // also possible in string format
        // SPARouter.navigate("parent/sub-parent/slug");
    }
});
```

# Bug
- slug recognized as parent, null is set as slug when navigating to previous link via back button (ex: https://development.inbody.com/wp-spa/load/sample-spa-page/null)