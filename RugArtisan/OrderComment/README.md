# Order Attachment Module 

This module contains customization for uploading attachment in order comment from admin order section.

## Installation

Magento2 module installation is very easy, please follow the steps for installation-

Move all module's files into magento root directory app/code/RugArtisan/OrderComment/ folder.


## Run following command via terminal from magento root directory

```bash
php bin/magento module:enable RugArtisan_OrderComment
php bin/magento setup:upgrade
php bin/magento setup:di:compile
php bin/magento setup:static-content:deploy
```

## Usage

Admin user can add images as attachment with order comment.
