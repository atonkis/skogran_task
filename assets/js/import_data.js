$(function () {
  $('.js-import-data').on('click', function (e) {
    e.preventDefault();

    var $link = $(e.currentTarget);
    $.ajax({
      method: 'POST',
      url: $link.attr('href'),
     // data: date,
     
    }).done(function (data) {

      location.href = "/show/2021.03.01";
     //  alert(JSON.stringify(data));
    });
  });
});
