# Drug Monitor (PHP)

![Drug Monitor App](assets/images/pillorganizer.jpg)

An app to plan and organise prescription drug purchases.

For older people on medication, it can be hard to remember what to take, when to take it, and when to re-stock. Drug Monitor helps keep track of all three.

This is the PHP + MySQL version, built to run on standard shared hosting (cPanel). The original Node.js version lives on the `master` branch.

## Built with

- PHP (plain, no framework)
- MySQL / MariaDB (PDO)
- Bootstrap 5

## Features

- Add, edit and delete drugs (name, dosage, tablets per card, tablets per pack, taken per day)
- Daily dosage view
- Purchase calculator: works out how many cards and packs to buy for a chosen number of days

## Requirements

- PHP 7.4 or newer
- MySQL 5.7+ or MariaDB
- The PDO MySQL extension (enabled by default on virtually all hosts)

## Local setup (Laragon)

1. Place the project in `C:\laragon\www\drug-monitor` (already done).
2. Create the database and table. In HeidiSQL or phpMyAdmin, create a database called `drug_monitor`, then import `sql/schema.sql`. Or from the command line:

   ```bash
   mysql -u root -e "CREATE DATABASE drug_monitor CHARACTER SET utf8mb4;"
   mysql -u root drug_monitor < sql/schema.sql
   ```

3. Copy the config template and adjust if needed:

   ```bash
   cp config.example.php config.php
   ```

   The defaults (`localhost`, user `root`, empty password) match Laragon out of the box.
4. Open the site at your Laragon URL, for example `http://drug-monitor.test`.

## Deploying to shared hosting (cPanel)

1. In cPanel, create a MySQL database and a database user, then add the user to the database with all privileges.
2. Import `sql/schema.sql` into that database via phpMyAdmin.
3. Copy `config.example.php` to `config.php` and fill in your cPanel database name, user and password. Note that cPanel usually prefixes them, for example `myacct_drugmonitor`.
4. Upload the whole project into `public_html` (or a subfolder) using the File Manager or FTP.
5. Visit your domain. That's it, no build step and no Node required.

`config.php` holds your database credentials and is git-ignored, so it never gets committed.

## Note on the database

The Node.js version used MongoDB. This version uses MySQL, since shared hosting supports MySQL rather than MongoDB. Existing Mongo data does not carry over automatically; re-enter drugs through the Add Drug form, or import them into the `drugs` table directly.

## Author

Peter Banigo

## Licence

ISC
