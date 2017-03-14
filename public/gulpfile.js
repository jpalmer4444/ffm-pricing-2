var gulp = require('gulp');
var concat = require('gulp-concat');
var uglify = require('gulp-uglify');

var vendorFiles = [
  'js/vendor/jquery-3.1.0.min.js',
  'js/vendor/jquery-ui.min.js',
  'js/vendor/jquery.dataTables.min.js',
  'js/DT_bootstrap_3.js',
  'js/vendor/dataTables.fixedHeader.min.js',
  'js/vendor/bootstrap.min.js',
  'js/vendor/bootstrap-datepicker.min.js',
  'js/vendor/bootstrap-select.min.js',
  'js/vendor/bootstrap-notify.min.js',
  'js/zf-table.js',
  'js/ie/respond.min.js',
  'js/ie/html5shiv.min.js',
];

gulp.task('vendor', function () {
  return gulp.src(vendorFiles)
          .pipe(concat('vendor.js'))
          .pipe(uglify())
          .pipe(gulp.dest('js/dist/'));
});

var angularAppFiles = [
  'js/ng/vendor/angular.min.js',
  'js/ng/vendor/angular-animate.min.js',
  'js/ng/vendor/angular-sanitize.min.js',
  'js/ng/vendor/ui-bootstrap-tpls.min.js',
  'js/ng/vendor/select.min.js',
  'js/ng/app.js',
  'js/ng/header/header.module.js',
  'js/ng/header/header.controller.js',
  'js/ng/screen/confirmationModal.controller.js',
  'js/ng/screen/datepicker.directive.js',
  'js/ng/screen/opened.directive.js',
  'js/ng/screen/screen.module.js',
  'js/ng/screen/screen.service.js',
  'js/ng/screen/select-on-focus.directive.js',
  'js/ng/screen/warningModal.controller.js'
];

gulp.task('angular-app', function () {
  return gulp.src(angularAppFiles)
          .pipe(concat('angular-app-files.js'))
          .pipe(gulp.dest('js/dist/'));
});

gulp.task('watch-js', function () {
  gulp.watch(angularAppFiles, ['angular-app'])
});


gulp.task('default', ['vendor', 'angular-app']);