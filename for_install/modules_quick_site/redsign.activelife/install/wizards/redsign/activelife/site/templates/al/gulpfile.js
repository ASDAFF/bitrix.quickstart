'use strict';

var gulp = require('gulp');
var runSequence = require('run-sequence');
var sass = require('gulp-sass');
var sourcemaps = require('gulp-sourcemaps');
var args = require('yargs').argv;
var pixrem = require('gulp-pixrem');
var autoprefixer = require('gulp-autoprefixer');
var cssmin = require('gulp-minify-css');
var spritesmith = require('gulp.spritesmith');
var spritesmash = require('gulp-spritesmash');
var uglify = require('gulp-uglify');
var rename = require('gulp-rename');

var path = {
  src: {
    styles: './sass',
    js: './',
    images: './img',
  },
  build: {
    styles: './assets/css',
    images: './assets/img',
    js: './',
  },
  watch: {
    styles: './sass/**/*.scss',
    sprite: {
      png: 'assets/img/themes/'
    },
    js: [
      './**/*.js',
      '!./node_modules/*.js',
      '!./node_modules/**/*.js',
      '!./gulpfile.js',
      '!./**/*.min.js',
      '!./**/*.map.js',
      '!*.min.js',
      '!*.map.js',
    ]
  }
};

var themes = [
  'activelife',
  'everyday',
  'fashionshow',
  'homeware',
  'lovekids',
  'mediamart',
  'officespace',
  'pethouse',
  'stroymart',
]

gulp.task('default', ['build', 'watch']);

// gulp.task('build', [
    // 'js:build',
    // 'sprite:build',
    // 'styles:build',
// ]);


gulp.task('build', function(){

  if(args.theme == 'all') {

    for (var key in themes) {
      runSequence(
        'sprite.png:build --theme=' + themes[key],
        'styles:build --theme=' + themes[key]
      );  
    }

  } else if (args.theme) {
    runSequence('sprite.png:build', 'styles:build');
  } else {
    runSequence('sprite.png:build', 'styles:build');
  }

});

gulp.task('watch', function(){

  //gulp.watch([path.watch.js], ['js:build']);
  gulp.watch([path.watch.sprite.png], ['sprite.png:build']);
  gulp.watch([path.watch.styles], ['styles:build']);

});

gulp.task('js:build', function () {

  // if(args.production) {

    // gulp.src(path.watch.js)
      // .pipe(uglify())
      // .pipe(rename({suffix: '.min'}))
      // .pipe(sourcemaps.write('./'))
      // .pipe(gulp.dest(path.build.js));
      
  // } else {
    
    gulp.src(path.watch.js)
      .pipe(uglify().on('error', function(e){
        console.log(e);
      }))
      .pipe(rename({suffix: '.min'}))
      .pipe(sourcemaps.write('./'))
      .pipe(gulp.dest(path.build.js));
      
  //}
});

gulp.task('styles:build', function() {

  var sDestPath = './build/' + themes[0] + '/'+ path.build.styles;
  if (args.theme == 'all') {
  } else if (args.theme) {
    sDestPath = path.build.styles;
  } else {
    sDestPath = path.build.styles;
  }
  
  if (args.production) {

    gulp.src(path.watch.styles)
      .pipe(sass().on('error', sass.logError))
      .pipe(pixrem({
        "atrules": true
      }))
      .pipe(autoprefixer({
          browsers: ['last 2 versions'],
          cascade: false
      }))
      .pipe(gulp.dest(sDestPath))
      .pipe(cssmin())
      .pipe(rename({suffix: '.min'}))
      .pipe(gulp.dest(sDestPath));
      
  } else {

    gulp.src(path.watch.styles)
      .pipe(sourcemaps.init())
      .pipe(sass().on('error', sass.logError))
      .pipe(pixrem({
        "atrules": true,
        rootValue: '16px'
      }))
      .pipe(autoprefixer({
          browsers: ['last 2 versions'],
          cascade: false
      })) 
      .pipe(sourcemaps.write('./'))
      .pipe(gulp.dest(sDestPath));
      
    gulp.src(sDestPath+'/template.css')
      .pipe(pixrem({
        "atrules": true,
        "replace": true,
        "rootValue": '16px'
      }))
      .pipe(gulp.dest(sDestPath+'/1/'));
  }

});

gulp.task('sprite:build', [
  'sprite.png:build',
]);


gulp.task('sprite.png:build', function () {

  var fileName = 'icons.png';

  var spriteData = gulp.src([
      path.watch.sprite.png + '_base/icon/png/*.png',
      path.watch.sprite.png + (args.theme ? args.theme : themes[0]) + '/icon/png/*.png'
    ])
    .pipe(spritesmith({
      imgName: fileName,
      cssName: './_sprite.scss',
      cssFormat: 'scss',
      imgPath: '../img/' + fileName
    }));

  spriteData.img
    .pipe(gulp.dest(path.build.images))

  spriteData.css
    .pipe(spritesmash())
    .pipe(gulp.dest(path.src.styles))

});