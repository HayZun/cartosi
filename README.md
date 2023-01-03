# GLPI/Carto-SI Plugin synchronization

## Introduction

This plugin allows to integrete applications from CartoSI (collaborative solution dedicated to operational staff who need to easily represent their assets, flows and data, while integrating easily into the company's ecosystem) on GLPI.

## Documentation

Once the plugin is activate :
1. Go on the config page of Carto-SI plugin and fill token and tenant fill.
2. Go on the page "Automatic actions" which is found on the Setup menu.
3. Search "Carto-SI".
4. Launch the automatic action (click on the execute button).

Applications from Carto-SI are imported on the menu Plugin/Carto-SI.


## Installation

On your host glpi :
Go on /var/www/html/plugins/
```sh
cd /var/www/html/plugins/
git clone https://github.com/HayZun/cartosi.git
```
