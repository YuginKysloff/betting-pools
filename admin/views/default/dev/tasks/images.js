import gulp from 'gulp';
import clean from 'gulp-dest-clean';
import changed from 'gulp-changed';
import imagemin from 'gulp-imagemin';
import browserSync from 'browser-sync';



gulp.task('img', () => {
  gulp.src(['img/**/*.{png,jpg,svg,gif,json,xml,ico}', '!img/sprite/**/*', '!img/build-favicon/**/*'])
    .pipe(clean('../img', 'extras/**', {
      force: true
    }))
    .pipe(changed('../img'))
    .pipe(imagemin({
      progressive: true,
      interlaced: true,
      svgoPlugins: [{
        cleanupIDs: false
      }]
    }))
    .pipe(gulp.dest('../img'))
    browserSync.reload();
});