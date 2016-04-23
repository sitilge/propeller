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
gulp.task('sass-template', function() {
    return gulp.src('public/scss/src/template/*.scss')
        .pipe(sass())
        .pipe(gulp.dest('public/css/src/template'));
});

gulp.task('sass-schema', function() {
    return gulp.src('public/scss/src/schema/*.scss')
        .pipe(sass())
        .pipe(gulp.dest('public/css/src/schema'));
});

gulp.task('sass-table', function() {
    return gulp.src('public/scss/src/table/*.scss')
        .pipe(sass())
        .pipe(gulp.dest('public/css/src/table'));
});

gulp.task('sass-row', function() {
    return gulp.src('public/scss/src/row/*.scss')
        .pipe(sass())
        .pipe(gulp.dest('public/css/src/row'));
});

gulp.task('sass-row', function() {
    return gulp.src('public/scss/src/row/*.scss')
        .pipe(sass())
        .pipe(gulp.dest('public/css/src/row'));
});

gulp.task('sass-row-plugins', function() {
    return gulp.src('public/scss/src/row/plugins/*.scss')
        .pipe(sass())
        .pipe(gulp.dest('public/css/src/row/plugins'));
});

gulp.task('sass-throwable', function() {
    return gulp.src('public/scss/src/throwable/*.scss')
        .pipe(sass())
        .pipe(gulp.dest('public/css/src/throwable'));
});

// Concatenate & Minify CSS
gulp.task('css-template', ['sass-template'], function() {
    return gulp.src(['public/css/src/template/*.css'])
        .pipe(concatcss('template.css'))
        .pipe(cssnano())
        .pipe(gulp.dest('public/css/dist'));
});

gulp.task('css-schema', ['sass-schema'], function() {
    return gulp.src(['public/css/src/schema/*.css'])
        .pipe(concatcss('schema.css'))
        .pipe(cssnano())
        .pipe(gulp.dest('public/css/dist'));
});

gulp.task('css-table', ['sass-table'], function() {
    return gulp.src(['public/css/src/table/*.css'])
        .pipe(concatcss('table.css'))
        .pipe(cssnano())
        .pipe(gulp.dest('public/css/dist'));
});

gulp.task('css-row', ['sass-row'], function() {
    return gulp.src(['public/css/src/row/*.css'])
        .pipe(concatcss('row.css'))
        .pipe(cssnano())
        .pipe(gulp.dest('public/css/dist'));
});

gulp.task('css-row-plugins', ['sass-row-plugins'], function() {
    return gulp.src(['public/css/src/row/plugins/*.css'])
        //.pipe(concatcss('row.css'))
        .pipe(cssnano())
        .pipe(gulp.dest('public/css/dist/plugins'));
});

gulp.task('css-throwable', ['sass-throwable'], function() {
    return gulp.src(['public/css/src/throwable/*.css'])
        .pipe(concatcss('throwable.css'))
        .pipe(cssnano())
        .pipe(gulp.dest('public/css/dist'));
});

// Concatenate & Minify JS
gulp.task('js-template', function() {
    return gulp.src([
            'public/js/src/template/jquery.js',
            'public/js/src/template/bootstrap.js',
            'public/js/src/template/*.js'])
        .pipe(concat('template.js'))
        .pipe(uglify())
        .pipe(gulp.dest('public/js/dist'));
});

gulp.task('js-schema', function() {
    return gulp.src([
            'public/js/src/schema/*.js'])
        .pipe(concat('schema.js'))
        .pipe(uglify())
        .pipe(gulp.dest('public/js/dist'));
});

gulp.task('js-table', function() {
    return gulp.src([
            'public/js/src/table/*.js'])
        .pipe(concat('table.js'))
        .pipe(uglify())
        .pipe(gulp.dest('public/js/dist'));
});

gulp.task('js-row', function() {
    return gulp.src([
            'public/js/src/row/moment.js',
            'public/js/src/row/*.js'])
        .pipe(concat('row.js'))
        .pipe(uglify())
        .pipe(gulp.dest('public/js/dist'));
});

gulp.task('js-row-plugins', ['js-row'], function() {
    return gulp.src([
            'public/js/src/row/plugins/*.js'])
        //.pipe(concat('image.js'))
        .pipe(uglify())
        .pipe(gulp.dest('public/js/dist/plugins'));
});

gulp.task('js-throwable', function() {
    return gulp.src([
            'public/js/src/throwable/*.js'])
        .pipe(concat('row.js'))
        .pipe(uglify())
        .pipe(gulp.dest('public/js/dist'));
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
    gulp.watch('public/scss/src/template/*.scss', 'sass-template');
    gulp.watch('public/scss/src/schema/*.scss', 'sass-schema');
    gulp.watch('public/scss/src/table/*.scss', 'sass-table');
    gulp.watch('public/scss/src/row/*.scss', 'sass-row');
    gulp.watch('public/scss/src/throwable/*.scss', 'sass-throwable');

    gulp.watch('public/css/src/template/*.css', 'css-template');
    gulp.watch('public/css/src/shema/*.css', 'css-shema');
    gulp.watch('public/css/src/table/*.css', 'css-table');
    gulp.watch('public/css/src/row/*.css', 'css-row');
    gulp.watch('public/css/src/throwable/*.css', 'css-throwable');

    gulp.watch('public/js/src/template.js', 'js-template');
    gulp.watch('public/js/src/schema.js', 'js-schema');
    gulp.watch('public/js/src/table.js', 'js-table');
    gulp.watch('public/js/src/row.js', 'js-row');
    gulp.watch('public/js/src/throwable.js', 'js-throwable');
});

// Default Task
gulp.task('default', [
    'sass-template',
    'sass-schema',
    'sass-table',
    'sass-row',
    'sass-row-plugins',
    'sass-throwable',
    'css-template',
    'css-schema',
    'css-table',
    'css-row',
    'css-row-plugins',
    'css-throwable',
    'js-template',
    'js-schema',
    'js-table',
    'js-row',
    'js-row-plugins',
    'js-throwable'
]);