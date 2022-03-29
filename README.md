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
