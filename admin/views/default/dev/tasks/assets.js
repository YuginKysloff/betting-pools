import gulp from 'gulp';
import browserSync from 'browser-sync';



gulp.task('php', () => {
  gulp.src(['../pages/*.php','../pages/layouts/*.php'])
    browserSync.reload();
});