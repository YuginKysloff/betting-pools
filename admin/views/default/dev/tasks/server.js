import gulp from 'gulp';
import browserSync from 'browser-sync';



gulp.task('browser-sync', () => {
  browserSync.init({
    proxy: "http://freelot/admin/"
    // server: {
    //     baseDir: "../"
    // }
  });
});