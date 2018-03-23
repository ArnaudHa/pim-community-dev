# Writing frontend tests

## Acceptance

### How it works

The frontend acceptance tests are run with cucumber-js for the scenarios and puppeteer for executing the tests. We run the tests without any backend using fixture data. To achieve this, we:

#### 1. Make a dump of the form extensions using the FormExtensionProvider

In our acceptance test definitions we can capture network requests called by the app and replace them with custom fixtures. When the app calls the form extensions endpoint we will return this dump. To create this dump, run `bin/console pim:installer:dump-extensions`.

#### 2. Use webpack to create a test version of our frontend bundle
For this step, we build the app using custom entry points (`index.html` and `index.js` in `webpack/test/templates`). This allows us to replace the normal `index.html.twig` from the EnrichBundle and eventually only render the views that we want instead of the whole app. 

We have a custom webpack test config `webpack-test.config.js` that uses the custom entry points and outputs all the built javascript inline inside index.html. 

To create the test version of the PIM you can run `yarn run webpack-test`. This will generate in the `web/test_dist` folder an index.html file that includes all the frontend code. 

#### 3. Running the tests

Just like the other frontend commands, we use scripts in package.json to run the acceptance tests.

There are three commands that can be run: 
- `yarn run acceptance` - This runs the tests normally, using headless chrome (The CI uses this)
- `yarn run acceptance-debug` - This runs the tests in debug mode, opening a chrome instance and keeping the process open after scenarios are run
- `yarn run generate-report` - This generates a html report of the last test

> Note: If you want to run inspect or add breakpoints in the acceptance tests or step definitions you can follow these steps:
    >- Add a breakpoint somewhere in your step definition with `debugger;`
    >- Run `node --inspect-brk  node_modules/.bin/cucumber-js -r ./webpack/test/acceptance/run-steps.js -r ./tests/front/acceptance/cucumber ./tests/front/acceptance/features/`
    >- Go to `chrome://inspect` in Chrome and click on the target `node_modules/.bin/cucumber-js`. If you don't see it, you can click on `Open dedicated DevTools for Node` instead. An inspector window will open. 
    >- Go to the sources tab in the inspector and click the play icon, you should now be able to walk through the steps

The important files and folders are:
- `run-steps.js` - This file gathers all the step definitions from `tests/front/acceptance/cucumber/step-definitions. 
- `world.js` - This is the main file the cucumber-js uses to execute the scenarios. Here we do all the setup:
    - Before each scenario, load the browser using `puppeteer` (with some debug options)
    - Start the interception of requests
    - Capture console log messages from the browser
    - Set up the JSON responses for the user and form extensions endpoints
    - After each scenario capture and report the success or failure
    - Generate a screenshot for the scenario failure
    - Close the browser
- `generate-report.js` - After the tests are run, this file can take the test output json from `web/test_dist/acceptance-js.json` and create a html report. 

> Note: Cucumber normally handles the auto-discovery of steps but this feature doesn't work with the EE. Cucumber unfortunately doesn't support executing step definitions from CE using the cucumber instance from inside the EE node_modules. So we manually gather the step definition files and pass in the cucumber instance.

In the end, you only have to worry about running these commands:
1. `bin/console pim:installer:dump-extensions` (once)
2. `yarn run webpack-test` (once)
3. `yarn run acceptance`

### Steps
