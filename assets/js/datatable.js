$(function () {
  $('#datatable').DataTable({
    serverSide: false,
    processing: true,
    paging: true,
    info: true,
    searching: true,
    pageLength: 10,
  });
});
