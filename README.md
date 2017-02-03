# Solspace: FreeForm

## Setting-up dev environment

1. First you will need NodeJS and NPM installed on your system.  
If you have it, skip this step  
Follow the instructions [here](https://docs.npmjs.com/getting-started/installing-node) to install NodeJS and NPM

2. Now you have to install all node packages required for running gulp tasks.  
To do that, open up your terminal of choice and CD into the freeform root directory and run `npm install`

3. Then you need to download and install the Composer package manager, and set it up globally. To do this, run this in your terminal  
```
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php -r "if (hash_file('SHA384', 'composer-setup.php') === 'e115a8dc7871f15d853148a7fbac7da27d6c0030b848d9b3dc09e2a0388afed865e6a3d6b3c0fad45c48e2b5fc1196ae') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
php composer-setup.php
php -r "unlink('composer-setup.php');"
mv composer.phar /usr/local/bin/composer
```

4. After the installation is complete, you can run `gulp` to install all dependencies, build JS and CSS files and have a working package you can link inside your Craft plugins directory.  
The dev environment files are located in `[..]/craft-freeform/src/freeform` folder

5. To prepare a deployment package, run `gulp deploy`, which will compile everything into the `[..]/craft-freeform/dist` folder.
