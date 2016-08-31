#!/usr/bin/env bash

cd .git/hooks
ln -s ../../git_hooks/pre-commit
cd ..
cd ..
rm -rf vendor
composer install
wait