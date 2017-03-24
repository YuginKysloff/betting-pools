import gulp from 'gulp';
import browserSync from 'browser-sync';



gulp.task('browser-sync', () => {
  browserSync.init({
    proxy: "http://betting-pools/"
    // server: {
    //     baseDir: "../"
    // }
  });
});