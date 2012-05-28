# Maufs

Maufs is a PHP command line tool to manage AUFS mounts a bit more elegantly.  

## Installation

Download the latest `maufs` binary, make it executable, and put it in your /usr/bin directory:

    wget https://github.com/downloads/AdrianSchneider/Maufs/maufs
    chmod +x maufs
    sudo mv maufs /usr/bin

## Usage

By calling the `maufs` command, it will present you with all of the options.

Assuming the following layout:

> **platform** in /path/to/base/platform
> **pluginA** in /path/to/plugins/a
> **pluginB** in /path/to/plugins/b
    
and a client site which should be empty

> **client** in /path/to/clients/client/install

To get started, use the maufs mount:mount command.  It will prompt you on which libraries to add, and where you want to store local files. If client/install contains the mounted directory, we can also have a secondary directory which will only contain any files that we change or create. Configuration, logs, etc. will all be placed here.

Here is an example of the process:

    cd client
    mkdir install
    mkdir files
    maufs mount:init install
    
    Enter the libraries you'd like to add.  Leave a blank line when you are done.
    Add library path? /path/to/base/platform
    Add library path? /path/to/plugins/a
    Add library path? /path/to/plugins/b
    Add library path? 
    Specify where you'd like file modifications to be placed.  If ommitted, /path/to/plugins/b will be used
    Modifications path? files

## Other Commands

If you need to make changes, you can use the other maufs commands to add/remove libraries, change the modifications path, remount, or unmount.

## Notes

All write commands will require sudo to function.  If you forget, just use `sudo !!` to re-run the same command.  Alternatively, you can run commands with --test=1 to print the raw mount command instead of running it through PHP.

Relative paths are fully supported, and are expanded to fully qualified paths in the mount command.

Maufs creates a ".maufs" configuration file in the mount directory.
