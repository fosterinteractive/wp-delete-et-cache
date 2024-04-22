# Delete ET Cache Plugin

## Description

The Delete ET Cache Plugin is designed to help manage the Divi Visual Builder slowness on WordPress sites hosted on Pantheon by periodically deleting the /wp-content/et-cache directory. This ensures that stale cache does not accumulate and degrade the performance of the Divi Builder.

## Features

* Automatically deletes the /wp-content/et-cache directory on a schedule.
* Supports both weekly, daily and hourly deletion schedules.
* MUST be enabled or disabled via wp-config.php.

Warning: hourly sets the cron to run "now". Pantheon runs it once an hour, but if you have custom cron jobs you should not use on production.

## Installation

Make sure you have a symlink in your git code for the /wp-content/et-cache as outlined in pantheon's docs https://docs.pantheon.io/symlinks-assumed-write-access#for-macos--linux

`cd /wp-content`

`ln -s ./uploads/et-cache ./et-cache`


Upload Plugin: 

* Download the delete-et-cache.zip file and extract it. 
* Upload the delete-et-cache folder to the /wp-content/plugins/ directory on your WordPress site.

Activate Plugin: Navigate to the WordPress Dashboard > Plugins > Installed Plugins. Locate "Delete ET Cache" and click Activate.

## Configuration

Copy and paste the following to your wp-config.php file below the `/* That's all, stop editing! Happy Pressing. */` line:

```
/** Deletes /et-cache folder directory to work around issues in pantheon's caching system and divi making 1000's of files. */

// Enable the plugin's functionality
define('ENABLE_ET_CACHE_DELETION', true);

// Options: 'hourly', 'daily', 'weekly' to delete all the files and folders in et-cache 
// 3:00AM for daily, Sunday at 3:00AM for Weekly, hourly runs on every cron run (1hr by default on pantheon)
define('ET_CACHE_DELETION_FREQUENCY', 'weekly');  
```

## Usage

Once configured and activated, the plugin will automatically delete the /wp-content/et-cache directory based on the defined schedule in wp-config.php. No further action is required unless you need to change the scheduling or disable the plugin.


## How to Test

1. Install the plugin with 'hourly' setting
2. Log into the file director in FTP to see the contents of /files/et-cache (there should be many #### folders in there)
3A. Either wait for cron to run (every hour)
3B. run `terminus wp your-site.your-test-enviroment cron event list` (Verify the cron is delete_et_cache_hook) is on the list then... 
    run `terminus wp your-site.your-test-enviroment cron event run --due-now` and refresh your SFTP listing.
4. The many numbered directories in /files/et-cache should be delete
5. Set the frequncy to a more reasonable 'weekly' or 'daily' setting
    



## Legal Disclaimer

The "Delete ET Cache on Cron" plugin is provided "as is", without warranty of any kind, express or implied. The authors and distributors of this plugin assume no responsibility for the accuracy, reliability, completeness, or timeliness of the material, services, software, text, graphics, and links. By using this plugin, you expressly acknowledge and agree that any use of this plugin is entirely at your own risk.

Neither the authors nor the distributors of this plugin shall be liable for any direct, indirect, incidental, special, consequential, or exemplary damages, including but not limited to, damages for loss of profits, goodwill, use, data, or other intangible losses (even if advised of the possibility of such damages), resulting from the use or the inability to use the plugin or any other matter relating to the plugin.

## License

This plugin is open-source and freely distributable under the GPLv2 license.


## Author

Aidan Foster
http://fosterinteractive.com
