import gulp from 'gulp';
import watch from 'gulp-watch';



gulp.task('watch', () => {
  watch(['../*.php', '../pages/*.php'], () => {
    gulp.start('php');
  })

  watch(['sass/**/*.scss', 'plugin/temp/**/*.scss'], () => {
    gulp.start('sass');
  })
  watch('js/**/*.js', () => {
    gulp.start('js');
  })
  watch('../js/script.js', () => {
    gulp.start('js-reload');
  })
  
  watch(['img/**/*.png', 'img/**/*.jpg', 'img/**/*.ico', 'img/**/*.gif', 'img/**/*.svg', 'img/**/*.json', 'img/**/*.xml', '!img/sprite/*.png', '!img/build-favicon/**/*'], () => {
    gulp.start('img');
  })
});