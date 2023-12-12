#!/bin/bash

bin/magento maintenance:enable
rm -rf generated/*
rm -rf var/page_cache/*
rm -rf var/cache/*
rm -rf var/view_preprocessed/*
rm -rf pub/static/*
bin/magento setup:upgrade
bin/magento setup:di:compile
bin/magento s:s:d de_DE en_US --jobs=2
bin/magento c:c
bin/magento c:f
bin/magento maintenance:disable

