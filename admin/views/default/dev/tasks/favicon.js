import gulp from 'gulp';
import realFavicon from 'gulp-real-favicon';
import fs from 'fs';

const FAVICON_DATA_FILE = 'img/build-favicon/faviconData.json';
const projectName = 'Имя проекта';



gulp.task('favicon', ['generate-favicon'], () => {
  gulp.src(['img/build-favicon/html-template/code.html'])
    .pipe(realFavicon.injectFaviconMarkups(JSON.parse(fs.readFileSync(FAVICON_DATA_FILE)).favicon.html_code))
    .pipe(gulp.dest('img/build-favicon'));
});
gulp.task('generate-favicon', (done) => {
  realFavicon.generateFavicon({
    masterPicture: 'img/build-favicon/favicon.png',
    dest: 'img/favicons',
    iconsPath: '/img/favicons',
    design: {
      ios: {
        pictureAspect: 'backgroundAndMargin',
        backgroundColor: '#ffffff',
        margin: '14%',
        assets: {
          ios6AndPriorIcons: true,
          ios7AndLaterIcons: true,
          precomposedIcons: true,
          declareOnlyDefaultIcon: true
        }
      },
      desktopBrowser: {},
      windows: {
        pictureAspect: 'noChange',
        backgroundColor: '#da532c',
        onConflict: 'override',
        assets: {
          windows80Ie10Tile: false,
          windows10Ie11EdgeTiles: {
            small: false,
            medium: true,
            big: false,
            rectangle: false
          }
        }
      },
      androidChrome: {
        pictureAspect: 'backgroundAndMargin',
        margin: '17%',
        backgroundColor: '#ffffff',
        themeColor: '#ffffff',
        manifest: {
          name: projectName,
          display: 'standalone',
          orientation: 'notSet',
          onConflict: 'override',
          declared: true
        },
        assets: {
          legacyIcon: true,
          lowResolutionIcons: true
        }
      },
      safariPinnedTab: {
        pictureAspect: 'silhouette',
        themeColor: '#5bbad5'
      }
    },
    settings: {
      scalingAlgorithm: 'Mitchell',
      errorOnImageTooSmall: false
    },
    markupFile: FAVICON_DATA_FILE
  }, () => {
    done();
  });
});