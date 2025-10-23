(function ($) {
  "use strict";

  var loans_table = $("#loans_table").DataTable({
    processing: true,
    serverSide: true,
    ajax: {
      url: _url + "/admin/loans/get_table_data",
      method: "POST",
      data: function (d) {
        d._token = $('meta[name="csrf-token"]').attr("content");

        if ($("select[name=status]").val() != "") {
          d.status = $("select[name=status]").val();
        }
      },
      error: function (request, status, error) {
        console.log(request.responseText);
      },
      },
    columns: [
      { data: "loan_id", name: "loan_id" },
      { data: "loan_product.name", name: "loan_product.name", "defaultContent": "" },
      { data: "borrower.first_name", name: "borrower.first_name", "defaultContent": "" },
      { data: "borrower.member_no", name: "borrower.member_no", "defaultContent": "" },
      { data: "release_date", name: "release_date" },
      { 
        data: "applied_amount", 
        name: "applied_amount"
      },
      { data: "status", name: "status" },
      { data: "action", name: "action" },
    ],
    footerCallback: function (row, data, start, end, display) {
      if (!data || data.length === 0) return;

      var api = this.api();

      // Get sum of approved and completed loans
      var total = api
        .rows({ search: 'applied' })
        .data()
        .reduce(function (sum, row) {
          // Check if loan is approved or completed (status = success or info class)
          if (row.status.includes('success') || row.status.includes('info')) {
            // Extract amount value
            var amount = row.applied_amount.replace(/[^0-9.]/g, '');
            return sum + (parseFloat(amount) || 0);
          }
          return sum;
        }, 0);

      // Get currency symbol from first row
      var firstRow = api.row(0).data();
      var currencySymbol = '';
      if (firstRow && firstRow.applied_amount) {
        currencySymbol = firstRow.applied_amount.replace(/[\d,. ]/g, '') + ' ';
      }

      // Format total with currency and thousand separators
      var formattedTotal = currencySymbol + total.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');

      // Update footer
      var footer = $(api.column(5).footer());
      footer.html(formattedTotal);
      footer.css({
        'font-weight': 'bold',
        'text-align': 'right',
        'color': '#000'
      });
    },
    responsive: true,
    bStateSave: true,
    bAutoWidth: false,
    ordering: false,
    language: {
      decimal: "",
      emptyTable: $lang_no_data_found,
      info:
        $lang_showing +
        " _START_ " +
        $lang_to +
        " _END_ " +
        $lang_of +
        " _TOTAL_ " +
        $lang_entries,
      infoEmpty: $lang_showing_0_to_0_of_0_entries,
      infoFiltered: "(filtered from _MAX_ total entries)",
      infoPostFix: "",
      thousands: ",",
      lengthMenu: $lang_show + " _MENU_ " + $lang_entries,
      loadingRecords: $lang_loading,
      processing: $lang_processing,
      search: $lang_search,
      zeroRecords: $lang_no_matching_records_found,
      paginate: {
        first: $lang_first,
        last: $lang_last,
        previous: "<i class='ti-angle-left'></i>",
        next: "<i class='ti-angle-right'></i>",
      },
    },
    drawCallback: function () {
      $(".dataTables_paginate > .pagination").addClass("pagination-bordered");
    },
  });

  $(".select-filter").on("change", function (e) {
    loans_table.draw();
  });

  $(document).on("ajax-screen-submit", function () {
    loans_table.draw();
  });
})(jQuery);
