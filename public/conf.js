exports.config = {

  allScriptsTimeout: 30000,

  getPageTimeout: 30000,

  seleniumAddress: 'http://localhost:4444/wd/hub',

  specs: ['e2e/authorization.spec.js', 'e2e/admin.spec.js'],

  rootElement: 'html',

  params: {
    dev: {
      login: {
        username: 'jpalmer',
        password: 'Jj1@2016'
      }
    },
    staging: {
      login: {
        username: 'jpalmer',
        password: 'Jj1@2016'
      }
    }
  },

  capabilities: {
    'browserName': 'chrome'
  },

  baseUrl: 'https://ffmpricing.localhost/',

  framework: 'jasmine',

  jasmineNodeOpts: {

    defaultTimeoutInterval: 30000

  },

};
