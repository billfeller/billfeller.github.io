var gulp = require('gulp');
var swPrecache = require('sw-precache');
var connect = require('gulp-connect');

var rootDir = 'dist/';

/*************** generate service worker ******************/
gulp.task('gsw', function(callback) {
    swPrecache.write(`${rootDir}/service-worker.js`, {
        staticFileGlobs: [rootDir + '/**/*.{js,html,css,png,jpg,gif,svg,eot,ttf,woff}'],
        stripPrefix: rootDir,
        runtimeCaching: [{
            urlPattern: /^https:\/\/cloud\.githubusercontent\.com\//,                             // cdn
            handler: 'fastest',
            options: {
                cache: {
                    name: 'cdn'
                }
            }
        }]
    }, callback);
});

gulp.task('connect', function() {
  connect.server({
    root: 'dist/',
    index : 'index.html',
    port : 80,
    livereload: true
  });
});