$(function () {
    //slugify text
    var slug = $('.slugify');

    slug.on('keyup', function () {
        slugify(this);
    });

    slug.on('blur', function () {
        slugify(this, true);
    });
});

//slugify text
function slugify(ele, focus) {
    if (focus){
        ele.value = sanitize(ele.value);
    }
    else {
        var start = ele.selectionStart;
        var end = ele.selectionEnd;

        delay(function() {
            var lengthBefore = ele.value.length;
            ele.value = sanitize(ele.value);
            var lengthAfter = ele.value.length;
            var removed = lengthBefore-lengthAfter;

            ele.setSelectionRange(start-removed, end-removed);
        }, 2000);
    }
}

var timer = 0;

function delay(callback, ms) {
    clearTimeout (timer);
    timer = setTimeout(callback, ms);
}

//sanitize input
function sanitize(ele) {
    return ele.toString().toLowerCase()
        .replace(/\s+/g, '-')           // Replace spaces with -
        .replace(/[^\w\-]+/g, '')       // Remove all non-word chars
        .replace(/\-\-+/g, '-')         // Replace multiple - with single -
        .replace(/^-+/, '')             // Trim - from start of text
        .replace(/-+$/, '');            // Trim - from end of text
}