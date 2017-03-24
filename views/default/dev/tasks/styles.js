import gulp from 'gulp';
import sourcemaps from 'gulp-sourcemaps';
import plumber from 'gulp-plumber';
import autoprefixer from 'gulp-autoprefixer';
import sass from 'gulp-sass';
import sassGlob from 'gulp-sass-glob';
import browserSync from 'browser-sync';
import errorHandler from './error';
var cache = require('gulp-cache');

gulp.task('sass',(done) => {
  gulp.src('sass/style.scss')
    .pipe(plumber({
      errorHandler: errorHandler
    }))
    .pipe(sassGlob())
    .pipe(sourcemaps.init())
    .pipe(sass({
      outputStyle: 'compressed'
    }).on('error', sass.logError))
    .pipe(autoprefixer({
      browsers: ['last 4 versions'],
      cascade: false
    }))
    .pipe(sourcemaps.write('map'))
    .pipe(gulp.dest('../css'))
    .pipe(browserSync.stream({match: '**/*.css'}));
    return cache.clearAll(done);
});

 
gulp.task('clear', function (done) {
  return cache.clearAll(done);
});
