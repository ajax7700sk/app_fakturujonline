const gulp = require('gulp'),
    sass = require('gulp-sass')(require('sass')),
    gulpBabel = require('gulp-babel'),
    sourcemaps = require('gulp-sourcemaps'),
    postcss = require('gulp-postcss'),
    autoprefixer = require('autoprefixer'),
    browserSync = require('browser-sync').create(),
    cssnano = require('cssnano'),
    rename = require('gulp-rename'),
    uglify = require('gulp-uglify'),
    concat = require('gulp-concat'),
    include = require('gulp-include'),
    rev = require('gulp-rev'),
    revFormat = require('gulp-rev-format'),
    semaphore = require('stream-semaphore'),
    del = require('del'),
    runSequence = require('run-sequence');

let productionMode = true;
const config = {
    wwwFolder: './../www',
    manifestFile: './../www/dist/manifest.json'
}

/****************************
 // App styles task
 ****************************/

/**
 *
 * @param (string) sourcePath
 * @param (string) destinationPath
 * @param (string) netteWWWDestinationPath
 * @returns {*}
 * @private
 */
function _styles(sourcePath, destinationPath)
{
    const plugins = [
        autoprefixer(),
        cssnano()
    ];

    // Clean all build files
    clean(['./../www/dist/css/*.css', './../www/dist/css/*.map']);

    // Where is main(app) scss file
    const task = gulp.src(sourcePath)
        // init sourcemaps
        .pipe(sourcemaps.init())
        // Pass that file through less compiler
        .pipe(sass({
            outputStyle: productionMode ? 'compressed' : 'expanded',
            includePaths: ['node_modules'],
        }))

    // Post CSS
    if (productionMode) {
        task
            .pipe(postcss(plugins));
    }

    task
        .pipe(rename({
            extname: '.min.css'
        }))

    return store(sourcePath, task, 'dist/css/');
}

/**
 * Main styles
 * @returns {*}
 * @public
 */
function mainStyles()
{
    return _styles('./scss/main.scss', './css');
}


// ------------------------------------ Scripts ----------------------------------- \\

/**
 * @param sourcePath
 * @param destinationPath
 * @returns {*}
 * @private
 */
function _scripts(sourcePath, destinationPath)
{
    // Clean all build files
    clean(['./../www/dist/js/*.js', './../www/dist/js/*.map']);

    let task = gulp.src(sourcePath)
        //init sourcemaps
        .pipe(sourcemaps.init());

    //include javascript files
    task
        .pipe(include({
            extensions: 'js',
            hardFail: true,
            separateInputs: true,
            includePaths: [
                __dirname + '/node_modules'
            ]
        }))

    if (productionMode) {
        //convert next generation ES2015+ code into ES5 code (ES == JavaScript)
        task
            .pipe(gulpBabel({
                presets: [
                    ['@babel/preset-env', {modules: false}]
                ]
            }))
            // minify javascript file with uglify
            .pipe(uglify());
    }

    //rename file
    task
        .pipe(rename({
            extname: '.min.js'
        }));

    // build versioned file and update manifest.json
    return store(sourcePath, task, 'dist/js');
}

function mainScript()
{
    return _scripts('./js/main.js', './js', '');
}

/***
 * Create file versioned version and add it to manifest file
 *
 * @param file
 * @param stream
 * @returns {*}
 */
function store(file, stream, destFolder)
{
    return stream
        .pipe(rename({
            dirname: destFolder //manually fixing path for rev-manifest
        }))
        .pipe(rev())
        .pipe(revFormat({prefix: '.'}))
        .pipe(sourcemaps.write('.'))
        .pipe(gulp.dest(config.wwwFolder))
        .pipe(semaphore.lockStream('manifest', file))
        .pipe(rev.manifest({
            path: config.manifestFile,
            merge: true
        }))
        .pipe(gulp.dest('./')) // write manifest
        .pipe(semaphore.unlockStream('manifest', file));
}

/**
 * @param {string[]} patterns
 */
function clean(patterns)
{
    del(patterns, {
        'force': true
    });
}

/****************************
 // Watch - watch files and reload browser on files change with browserSync
 ****************************/
function watchFiles()
{

    // browserSync.init({
    //     // IMPORTANT: naming "localhost" is buggy, use "127.0.0.1" instead
    //     proxy: '127.0.0.1:7000'
    // });

    //watch these files
    gulp.watch('./scss/**/*.scss', gulp.parallel('styles'));
    gulp.watch('./js/**/*.js', gulp.parallel('scripts'));
    // gulp.watch('./web/css/**/*.less', gulp.parallel('stylesWeb'));
}

// ---------------- Register single tasks ----------------------- \\

// Styles
gulp.task(mainStyles);
// All styles
gulp.task('styles', gulp.parallel('mainStyles'));
// Scripts
gulp.task(mainScript);
// All scripts
gulp.task('scripts', gulp.parallel('mainScript'));

gulp.task(watchFiles);
gulp.task('setDevelopmentMode', done => {
    productionMode = false;

    done();
});

//register join tasks as single task
gulp.task('default', gulp.parallel('styles', 'scripts'/*, 'stylesWeb'*/));
gulp.task('build', gulp.parallel('default'));
gulp.task('watch', gulp.series('setDevelopmentMode', 'default', 'watchFiles'));