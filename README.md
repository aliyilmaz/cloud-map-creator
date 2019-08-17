# Cloud map creator

This tools downloads the images from NASA's AQUA and TERRA satellites and creates a statistical cloud map for each day of the year.

Data source: https://neo.sci.gsfc.nasa.gov/view.php?datasetId=MODAL2_M_CLD_FR (AQUA + TERRA)

## Requirements

The following tools must be installed
* PHP > 7.2
* imagemagick
* composer

**IMPORTANT:** Before you run the app, you need to increase imagemagick's memory limit.

Solution for Linux (tested on Ubuntu)
1. Open `/etc/ImageMagick-6/policy.xml`
2. Set "disk" policy to 10GB `<policy domain="resource" name="disk" value="10GiB"/>`

## Installation

To install the tool run `composer install` in the root folder of this project.

## Run the tools

1. Run the tool: `php ./public/run.php`
2. The final results are located in the `/results` folder.
