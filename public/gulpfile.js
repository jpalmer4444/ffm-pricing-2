var gulp = require('gulp');
var concat = require('gulp-concat');
var uglify = require('gulp-uglify');

var vendorFiles = [
        'js/vendor/jquery-3.1.0.min.js',
        'js/vendor/jquery-ui.min.js',
        'js/vendor/jquery.dataTables.min.js',
        'js/vendor/dataTables.fixedHeader.min.js',
		'js/vendor/bootstrap.min.js',
		'js/vendor/bootstrap-datepicker.min.js',
		'js/vendor/bootstrap-select.min.js',
		'js/vendor/bootstrap-notify.min.js',
		'js/zf-table.js'
	];

gulp.task('vendor', function(){
	return gulp.src(vendorFiles)
		.pipe(concat('vendor.js'))
		.pipe(uglify())
		.pipe(gulp.dest('js/dist/'));
});

gulp.task('just-ie', function(){
	return gulp.src(['js/ie/respond.min.js', 'js/ie/html5shiv.min.js'])
		.pipe(concat('just-ie.js'))
		.pipe(uglify())
		.pipe(gulp.dest('js/dist/'));
});

gulp.task('device', function(){
	return gulp.src(['js/device/highcharts.js', 'js/device/moment.min.js', 'js/device/stats.js'])
		.pipe(concat('device.js'))
		.pipe(uglify())
		.pipe(gulp.dest('js/dist/'));
});

gulp.task('watch-vendor', function(){
	gulp.watch(vendorFiles, ['vendor'])
});

gulp.task('default', ['vendor', 'just-ie', 'device']);