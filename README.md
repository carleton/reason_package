Welcome to Reason CMS!
----------------------

- [reasoncms.org](http://reasoncms.org)
- [Requirements](http://apps.carleton.edu/opensource/reason/reqs/)

Automated Install (Using install.sh)
------------------------------------
1. Rename reasoncms to reason_package and move the directory outside your web root.

2. Run the install.sh script in the reason_package folder and follow instructions.

3. Run /reason/setup.php in your browser and follow instructions.

Semi-Automated Install (Not using install.sh)
---------------------------------------------
1. Move reason_package outside your web root.

2. Create a symbolic link called reason from your document root directory to ./reason_4.0/www/
    - Example: ```ln -s /absolute/path/to/reason_package/reason_4.0/www/ /absolute/path/to/document_root/reason```

3. Create a symbolic link called reason_package your document root directory to ./www/
    - Example: ```ln -s /absolute/path/to/reason_package/www/ /absolute/path/to/document_root/reason_package```

4. Visit /reason/setup.php in your web browser.

For manual installation instructions, see ./reason_4.0/www/install.htm

Upgrade
-------
Visit /reason/upgrade.php on your web server.

About Reason CMS
------------
- Reason CMS is designed to make it simple for content creators to create content.

- Reason CMS is more capable than Wordpress, and simpler than Drupal.

- Reason CMS is free and open source - released under the GNU General Public License Version 2.

[Release Notes](ReleaseNotes.md)
-------------

