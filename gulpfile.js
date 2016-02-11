// Include gulp
var gulp = require('gulp');

// Include Plugins
var rename = require('gulp-rename');
var sass = require('gulp-sass');
var cssnano = require('gulp-cssnano');
var concatcss = require('gulp-concat-css');
var concat = require('gulp-concat');
var uglify = require('gulp-uglify');
var imagemin = require('gulp-imagemin');
var pngquant = require('imagemin-pngquant');

// Compile Sass
gulp.task('sass', function() {
    return gulp.src('public/scss/src/*.scss')
        .pipe(sass())
        .pipe(gulp.dest('public/css/src'));
});

// Concatenate & Minify CSS
gulp.task('css', function() {
    return gulp.src(['public/css/src/*.css'])
        .pipe(concatcss('all.css'))
        .pipe(cssnano())
        .pipe(gulp.dest('public/css'));
});

// Concatenate & Minify JS
gulp.task('js', function() {
    return gulp.src([
            'public/js/src/jquery.js',
            'public/js/src/bootstrap.js',
            'public/js/src/*.js'])
        .pipe(concat('all.js'))
        .pipe(uglify())
        .pipe(gulp.dest('public/js'));
});

// Optimize Images
gulp.task('img', function() {
    return gulp.src('public/img/**/*')
        .pipe(imagemin({
            progressive: true,
            svgoPlugins: [{removeViewBox: false}],
            use: [pngquant()]
        }))
        .pipe(gulp.dest('public/img'));
});

// Watch Files For Changes
gulp.task('watch', function() {
    gulp.watch('public/scss/src/*.scss', ['sass']);
    gulp.watch('public/css/src/*.css', ['css']);
    gulp.watch('public/js/src/*.js', ['js']);
});

// Default Task
gulp.task('default', ['sass', 'css', 'js', 'img', 'watch']);