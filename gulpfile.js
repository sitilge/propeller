// Include gulp
var gulp = require('gulp');

// Include Plugins
var rename = require('gulp-rename');
var sass = require('gulp-sass');
var cssNano = require('gulp-cssnano');
var cssConcat = require('gulp-concat-css');
var jsConcat = require('gulp-concat');
var jsUglify = require('gulp-uglify');
var imgOptimize = require('gulp-image-optimization');

// Compile Sass
gulp.task('sass', function() {
    return gulp.src('public/scss/src/*.scss')
        .pipe(sass())
        .pipe(gulp.dest('public/css/src'));
});

// Concatenate & Minify CSS
gulp.task('css', function() {
    return gulp.src('public/css/src/*.css')
        .pipe(cssConcat('all.css'))
        .pipe(cssNano())
        .pipe(gulp.dest('public/css'));
});

// Concatenate & Minify JS
gulp.task('js', function() {
    return gulp.src([
            'public/js/src/jquery.js',
            'public/js/src/bootstrap.js',
            'public/js/src/*.js'])
        .pipe(jsConcat('all.js'))
        .pipe(jsUglify())
        .pipe(gulp.dest('public/js'));
});

// Optimize Images
gulp.task('img', function(cb) {
    gulp.src(['public/img/**/*.png', 'public/img/**/*.jpg', 'public/img/**/*.jpeg', 'public/img/**/*.gif'])
        .pipe(imgOptimize({
            optimizationLevel: 5,
            progressive: true,
            interlaced: true
        }))
        .pipe(gulp.dest('public/img'))
        .on('end', cb)
        .on('error', cb);
});

// Watch Files For Changes
gulp.task('watch', function() {
    gulp.watch('public/scss/src/*.scss', ['sass']);
    gulp.watch('public/js/src/*.js', ['js']);
});

// Default Task
gulp.task('default', ['sass', 'css', 'js', 'watch']);