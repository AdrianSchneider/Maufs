# Maufs

Maufs is a PHP command line tool to manage AUFS mounts a bit more elegantly.  

# Installation

To install, you'll first need composer to download all the dependencies.

    curl -s http://getcomposer.org/installer | php
    php composer.phar install

Then, place a symlink for it in your /usr/bin directory, so its always accessible:

    sudo ln -s /full/path/to/maufs/bin/maufs /usr/bin/maufs

# Usage

By calling the `maufs` command, it will present you with all of the options.
