# Solspace: FreeForm

## Setting-up dev environment

1. Switch to folder: `cd ~/Repos/ee-freeform` (Kelsey's lol)
2. Install Yarn in environment: `yarn install`
3. Update Yarn: `yarn update`
4. Deploy editions individually:
    - `yarn deploy:express`
    - `yarn deploy:lite`
    - `yarn deploy:pro`
5. Everything will be deployed to the `[..]/ee-freeform/dist` folder.

## Building the packages for official releases

1. Make sure you're using Node ~10. If you have `nvm` installed, you can switch to node 10 by using `nvm use 10`
2. Run the following three commands to build the files. After each command, copy the zip file in `/dist` to a different location, since each command cleans up this folder to build the zip file:
   1. `npm run deploy:express`
   2. `npm run deploy:lite`
   3. `npm run deploy:pro`

**Note**: What the `npm` commands does is run `gulp deploy` commands. Probably better to not try to run `gulp` directly.

## Composer

With PHP 8 out, some libraries used through composer are not compatible between PHP 7 and 8 (in particular 8.1 as of this writing). Running composer with PHP 7 would complain that some library versions were too high, and bringing those versions down would make PHP 8 complain when using composer.

This was remedied by adding a `/php7` folder containing a PHP 7-friendly version of composer. `addon.setup.php` is where we load up composer dependencies, so a conditional controls if the PHP 7 or 8 version of dependencies are loaded.

When preparing a release, make sure to run `composer` at both levels.

### For PHP 7:

1. Go into the `php7` folder.
2. Run composer with PHP 7. If you're using something like Homebrew, this is how this would work: `/opt/homebrew/opt/php@7.1/bin/php /usr/local/bin/composer update`

### For PHP 8:

1. Go to the root folder of the repo.
2. Run composer with PHP 8. If you're using something like Homebrew, this is how this would work: `/opt/homebrew/opt/php@8.1/bin/php /usr/local/bin/composer update`

**NOTE**: Make sure you add the full path of `composer` (eg. `/usr/local/bin/composer`) if you're using a custom php version on your computer to run `composer` instead of the global `php` command you might have set up on your computer.
