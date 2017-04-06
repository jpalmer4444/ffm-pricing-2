exports.config = {
  allScriptsTimeout: 30000,
  getPageTimeout: 30000,
  seleniumAddress: 'http://localhost:4444/wd/hub',
  specs: ['e2e/authorization.spec.js'],
  rootElement: 'html',
  capabilities: {
    'browserName': 'chrome'
  },
  baseUrl: 'https://ffmpricing.localhost/',
  framework: 'jasmine',
  jasmineNodeOpts: {
    defaultTimeoutInterval: 30000
  },
};
