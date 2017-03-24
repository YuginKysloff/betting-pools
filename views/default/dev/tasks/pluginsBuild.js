import gulp from 'gulp';
import clean from 'gulp-dest-clean';
import rename from 'gulp-rename';
import {getEnabledPluginsCss} from '../plugins.js';

    
gulp.task('plugin', () => {
  gulp.src(getEnabledPluginsCss)
    .pipe(rename({
        extname: ".scss"
    }))
    .pipe(clean('plugin/temp', 'extras/**', {
      force: true
    }))

    .pipe(gulp.dest('plugin/temp'))
});