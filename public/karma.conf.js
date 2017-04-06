// Karma configuration
// Generated on Wed Apr 05 2017 18:39:45 GMT-0500 (CDT)

module.exports = function(config) {
  config.set({

    // base path that will be used to resolve all patterns (eg. files, exclude)
    basePath: '',


    // frameworks to use
    // available frameworks: https://npmjs.org/browse/keyword/karma-adapter
    frameworks: ['jasmine'],


    // list of files / patterns to load in the browser
    files: [
      'js/jquery.min.js',
      'js/jquery.dataTables.min.js',
      'js/vendor-orders/dataTables.select.min.js',
      'js/vendor-orders/dataTables.buttons.min.js',
      'js/vendor-orders/buttons.html5.min.js',
      'js/vendor-orders/buttons.flash.min.js',
      'js/vendor-orders/pdfmake.min.js',
      'js/vendor-orders/vfs_fonts.js',
      'js/vendor-orders/dataTables.fixedColumns.js',
      'js/ng/vendor/angular.min.js',
      'js/ng/vendor/select.min.js',
      'js/ng/vendor/ui-bootstrap-tpls.min.js',
      'js/ng/vendor/angular-sanitize.min.js',
      'js/ng/vendor/angular-local-storage.min.js',
      'js/ng/vendor/angular-datatables/angular-datatables.js',
      'js/ng/vendor/angular-datatables/plugins/fixedcolumns/angular-datatables.fixedcolumns.min.js',
      'js/ng/vendor/angular-datatables/plugins/buttons/angular-datatables.buttons.min.js',
      'js/ng/vendor/angular-datatables/plugins/select/angular-datatables.select.min.js',
      'node_modules/angular-mocks/angular-mocks.js',
      //'js/ng/**/*.js',
      'js/ng/common/*.js',
      'test/**/*Spec.js'
    ],


    // list of files to exclude
    exclude: [
    ],


    // preprocess matching files before serving them to the browser
    // available preprocessors: https://npmjs.org/browse/keyword/karma-preprocessor
    preprocessors: {
    },


    // test results reporter to use
    // possible values: 'dots', 'progress'
    // available reporters: https://npmjs.org/browse/keyword/karma-reporter
    reporters: ['progress'],


    // web server port
    port: 9876,


    // enable / disable colors in the output (reporters and logs)
    colors: true,


    // level of logging
    // possible values: config.LOG_DISABLE || config.LOG_ERROR || config.LOG_WARN || config.LOG_INFO || config.LOG_DEBUG
    logLevel: config.LOG_INFO,


    // enable / disable watching file and executing tests whenever any file changes
    autoWatch: true,


    // start these browsers
    // available browser launchers: https://npmjs.org/browse/keyword/karma-launcher
    browsers: ['PhantomJS'],


    // Continuous Integration mode
    // if true, Karma captures browsers, runs the tests and exits
    singleRun: false,

    // Concurrency level
    // how many browser should be started simultaneous
    concurrency: Infinity
  })
}
