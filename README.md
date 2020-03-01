# Wordpress Custom Directory Plugin

WordPress directory to create a list of "things" in WordPress.

A list of things can be
- A list of people
- A list of machine parts
- A list of products (Although WooCommerce is better for that use case)
- A list of documents

## How it works

The plugin creates 1 custom type, 1 taxonomy and 2 shortcodes.

The custom post type, that by default is called `Directory Item`, allows you to enter information of each element. Since its a _WordPress Post Type_ you can use the [Advanced Custom Field](https://www.advancedcustomfields.com/) (ACF) plugin to add attributes to each item.

The **Custom Taxonomy** allows you to group items in different directories. This way you can create multiple list in one site.

The 2 main shortcodes allows you to display the list on any page and to add a _Live Search Form_ to filter the directory.

## Help

You can refer to the [Online Help](help/PLUGIN_HELP.md) inside the plugin by accesing  the path `Custom Directory > Help`

## Development

You need to have installed `node` with `npm`, `php` as a command-line app and [`composer`](https://getcomposer.com)  globally.

```bash
cd /path/to/wordpress/wp-content/plugins/
git clone git@bitbucket.org:dazzet/wordpress-custom-directory.git
cd wordpress-custom-directory
composer install
npm install
composer phpcs
npm start # Optional
```

The last command is required if you are going to change the `js` files. Otherwise you don't need the `watch` activated.

## Deployment

### Create a ZIP file

```bash
npm run build
composer zip
composer install
composer dump-autoload
```

### Secure Copy to server

```bash
#!/bin/bash

REMOTE_USER=username
REMOTE_HOST=example.com
REMOTE_CONN=${REMOTE_USER}@${REMOTE_HOST} # Or a connection in your .ssh/hosts
REMOTE_PATH=/path/to/wordrpess/wp-content/plugins/`basename $PWD` # NO TRAILING SLASH

echo "Uploading to ${REMOTE_CONN}:${REMOTE_PATH}/"
sleep 2

npm run build

rsync -avz -e ssh --delete ./{classes,help,js,languages,vendor,*.php} ${REMOTE_CONN}:${REMOTE_PATH}/

composer install
composer dump-autoload -o
```