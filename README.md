=== Plugin Name ===
Tags: kicker, liga, kickerliebe, ligatool, foosball
Version: 2.6.4
Requires at least: 4.3.1
Tested up to: 4.3.1
Stable tag: 1.0
License: MIT
License URI: http://opensource.org/licenses/MIT

Integration of the Leagedatabase of KKL into Wordpress

== Description ==

Plugin provides an interface to manage the foosball league in cologne/germany.
it features page-templates, shortcodes, a full backend with teams, clubs,
leagues, seasons, matches, locations...

this is a very rough first version, needs changes.

== Installation ==

1. Upload `kickerliebe_ligatool/` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
4. Create the database by dumping sql/scheme.sql into mysql
3. Login as an Adminuser
4. Click on "KKL Ligatool", go to "Einstellungen", setup the database you just inserted data into
5. fiddle around, read the help on the top right of the backend screen (german only)

== Frequently Asked Questions ==

== Screenshots ==

== Changelog ==
1.0	initial version (05.12.2015)
2.0 move to composer (21.04.2018)

== Upgrade Notice ==

== Docker Development Environment ==

install docker on your machine
clone the project
run "docker-compose up -d" in the root folder of the project
    this will take some time for the first run
    will be faster in every subsequent run
go to http://localhost:8080
    do basic wordpress setup of language and user
go to http://localhost:8181
    login as root:db4wp
    create a new database "kkl_ligatool"
    import a kickerliga-sql-dump into the new database
go to http://localhost:8080/wp-admin/
    login with your admin user
    activate the plugin
    got to the plugin page, then "settings"
    fill out the database config
        host: mysql
        name: kkl_ligatool
        user: root
        pass: db4wp
    save and reload the page
