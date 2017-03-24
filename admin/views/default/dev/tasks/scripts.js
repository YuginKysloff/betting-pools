import gulp from 'gulp';
import plumber from 'gulp-plumber';
import sourcemaps from 'gulp-sourcemaps';
import concat from 'gulp-concat';
import uglify from 'gulp-uglify';
import browserSync from 'browser-sync';
import errorHandler from './error';

gulp.task('js', () => {
  gulp.src(['js/vendor/*.js', 'js/vendor/plugins/*.js', 'js/*.js'])
    .pipe(plumber({
      errorHandler: errorHandler
    }))
    .pipe(sourcemaps.init())
    .pipe(concat('script.js'))
    .pipe(uglify())
    .pipe(sourcemaps.write('map'))
    .pipe(gulp.dest('../js'))
     browserSync.reload();
});