# Sitemap

## Summary:

Make an HTML sitemap, in Magento 2.

## Installation:

Require new package with composer:
```bash
composer clearcache
composer config repositories.moogento vcs https://github.com/moogento/sitemap
composer require moogento/sitemap
```

Add link to (eg. footer) block:
```
<li>
  <a href="{{store direct_url="sitemap"}}">Sitemap</a>
</li>
```
