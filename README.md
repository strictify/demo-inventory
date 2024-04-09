# Free Zoho demo

This is just a demo, and not all use-cases will be covered. At least not for free 😉

## Getting Started

### Linux

* Run `make build` to build entire project
* Run `make up`; assert that there is no running nginx or apache that can interfere with installed Caddy server
* Run `make sh` to enter the PHP container
* Within the container: `composer install`
* Within the container: `bin/console importmap:install` to download and install CSS and JS assets
* Within the container: `bin/console d:s:u --force` to create DB tables and keep them in sync
* Within the container: `bin/console d:f:l -n` to install fixtures

Open https://inventory.localhost

* Login with email `demo@example.com` and password `demo`
* Play around, look at the code, try to break something.
* Within the container, run `vendor/bin/psalm --no-diff --find-unused-code` to see what happens
* If you have set Zoho account, set `ZOHO_CLIENT_ID` and `ZOHO_CLIENT_SECRET` in your `.env.local`
* Withing the container: `bin/console messenger:consume async`; all communication with Zoho happens in the background.

## Windows

You need to have a terminal that supports Makefile like PowerShell. If that doesn't work, read the commands from the
file
itself and enter them manually; it is pretty straightforward if you ever worked with Docker.

## Zoho integration

* Go to https://api-console.zoho.eu/ and create new client, pick `Server-Based applications`.
* Use any name, set https://inventory.localhost as homepage, and https://inventory.localhost/app/zoho/oauth2 as
  Authorized Redirect URIs
* Write down details into `ZOHO_CLIENT_ID` and `ZOHO_CLIENT_SECRET` in your `.env.local` file.
* **Important**: you must set Multi-DC option is `Settings` tab; app does not ask users for region.
* Go to https://inventory.localhost/app/zoho/connect to connect app with your Zoho account
* App will ask for all privileges, so use fake account as playground.

Once you want to disconnect the app, go to https://accounts.zoho.eu/home#sessions/userconnectedapps and remove it.

## Docker details

This repository is forked from an [amazing image](https://github.com/dunglas/symfony-docker) created by Kevin Dunglas.
Make sure to read its documentation as well, and see how to keep up with changes in Dockerfile.
