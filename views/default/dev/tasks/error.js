import path from 'path';
import util from 'gulp-util';
import notifier from 'node-notifier';
import browserSync from 'browser-sync';



let errorHandler = function(err) {
  browserSync.notify(err.message, 3000);
  util.log(util.colors.red(err.toString()));
  notifier.notify({
    title: 'Gulp notification',
    message: err.message,
    icon: path.join(__dirname, '/img/stop.png'),
    sound: true
  });
  this.emit('end');
}

export default errorHandler;