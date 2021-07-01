$(function () {
  $('.js-import-data').on('click', function (e) {
    e.preventDefault();

    let $link = $(e.currentTarget);
    $.ajax({
      method: 'POST',
      url: $link.attr('href'),
      dataType: 'json',
    }).done(function (data) {
      // let obj = JSON.stringify(data);
      // let date = JSON.parse(obj).data;

      location.href = `/show`;
    });
  });
});
