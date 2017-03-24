import gulp from 'gulp';
import watch from 'gulp-watch';



gulp.task('watch', () => {
  watch(['../*.php', '../pages/*.php', '../pages/layouts/*.php'], () => {
    gulp.start('php');
  })
  watch(['sass/**/*.scss'], () => {
    gulp.start('sass');
  })
  
  watch('js/**/*.js', () => {
    gulp.start('js')
  })
  watch(['img/**/*.png', 'img/**/*.jpg', 'img/**/*.gif', 'img/**/*.svg', 'img/**/*.json', 'img/**/*.xml', '!img/sprite/*.png', '!img/build-favicon/**/*'], () => {
    gulp.start('img')
  })
  watch('img/sprite/*.png', () => {
    gulp.start('sprite')
  })
});