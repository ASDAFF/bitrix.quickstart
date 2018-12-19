var gulp = require('gulp');
minify = require('gulp-minify');
uglify = require('gulp-uglify');
babel = require('gulp-babel');
cleanCSS = require('gulp-clean-css');
sourcemaps = require('gulp-sourcemaps');
cssmin = require('gulp-cssmin');
rename = require('gulp-rename');

var moduleId = "altasib.pagespeed";

gulp.task('default', function () {
    // place code for your default task here
});

var watcherJs = gulp.watch(['../install/js/*.js', '!../install/js/*min.js'], []);
watcherJs.on('change', function (event) {
    console.log('File ' + event.path + ' was ' + event.type + ', running tasks...');
    gulp.src(event.path)
        //.pipe(sourcemaps.init())
        // .pipe(babel({
        //     presets: ['env']
        // }))
        .pipe(minify())
        //.pipe(sourcemaps.write('../install/js'))
        .pipe(gulp.dest('../install/js'))
        .pipe(gulp.dest('../../../js/' + moduleId));
    console.log('end JS');
});

var watcherCss = gulp.watch(['../install/css/*.css', '!../install/css/*min.css'], []);
watcherCss.on('change', function (event) {
    console.log('File ' + event.path + ' was ' + event.type + ', running tasks...');
    gulp.src(event.path)
        .pipe(cssmin())
        .pipe(rename({suffix: '.min'}))
        .pipe(gulp.dest('../install/css'))
        .pipe(gulp.dest('../../../css/' + moduleId));
    console.log('end css');
});