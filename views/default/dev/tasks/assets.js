import gulp from 'gulp';
import browserSync from 'browser-sync';



gulp.task('php', () => {
  gulp.src('../*.php')
    browserSync.reload();
});