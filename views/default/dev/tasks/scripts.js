import gulp from 'gulp';
import plumber from 'gulp-plumber';
import sourcemaps from 'gulp-sourcemaps';
import concat from 'gulp-concat';
import uglify from 'gulp-uglify';
import browserSync from 'browser-sync';
import errorHandler from './error';

import {getEnabledPluginsJs} from '../plugins.js';

gulp.task('js', () => {
  gulp.src(getEnabledPluginsJs, {base: 'plugin'})
    .pipe(plumber({
      errorHandler: errorHandler
    }))
    .pipe(sourcemaps.init())
    .pipe(concat('script.js'))
    .pipe(uglify())
    .pipe(sourcemaps.write('map'))
    .pipe(gulp.dest('../js'))
});

gulp.task('js-reload', () => {
  gulp.src('../js/script.js')
    browserSync.reload()
});


var cache = require('gulp-cache');
 
gulp.task('clear', function (done) {
  return cache.clearAll(done);
});