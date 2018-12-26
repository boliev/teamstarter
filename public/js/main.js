function changeLocale(locale) {
    Cookies.set('_locale', locale);
    location.reload();
}

$(function () {
    $('select').select2();
});