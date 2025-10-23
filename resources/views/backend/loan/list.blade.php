@extends('layouts.app')

@section('content')
<div class="row">
	<div class="col-lg-12">
		<div class="card">
			<div class="card-header d-flex align-items-center">
				<span class="panel-title">{{ _lang('Loan List') }}</span>
				<div class="ml-auto d-flex align-items-center">
					<select name="status" class="select-filter filter-select auto-select mr-1" data-selected="{{ $status }}">
						<option value="">{{ _lang('All') }}</option>
						<option value="0">{{ _lang('Pending') }}</option>
						<option value="1">{{ _lang('Approved') }}</option>
						<option value="2">{{ _lang('Completed') }}</option>
					</select>
					<button class="btn btn-success btn-xs mr-1" id="export_excel"><i class="fas fa-file-excel"></i>&nbsp;{{ _lang('Export to Excel') }}</button>
					<a class="btn btn-primary btn-xs" href="{{ route('loans.create') }}"><i class="ti-plus"></i>&nbsp;{{ _lang('Add New') }}</a>
				</div>
			</div>

			<div class="card-body">
				<table id="loans_table" class="table table-bordered">
					<thead>
						<tr>
							<th>{{ _lang('Loan ID') }}</th>
							<th>{{ _lang('Loan Product') }}</th>
							<th>{{ _lang('Borrower') }}</th>
							<th>{{ _lang('Member No') }}</th>
							<th>{{ _lang('Release Date') }}</th>
							<th>{{ _lang('Applied Amount') }}</th>
							<th>{{ _lang('Status') }}</th>
							<th class="text-center">{{ _lang('Action') }}</th>
						</tr>
					</thead>
					<tbody>
					</tbody>
					<tfoot>
						<tr style="background-color: #f8f9fa;">
							<th colspan="5" style="text-align: right; font-weight: bold; padding: 10px;">{{ _lang('Total Active & Completed Loans Amount') }}:</th>
							<th style="text-align: right; font-weight: bold; padding: 10px;"></th>
							<th colspan="2"></th>
						</tr>
					</tfoot>
				</table>
			</div>
		</div>
	</div>
</div>
@endsection

@section('js-script')
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
<script src="{{ asset('public/backend/assets/js/datatables/loans.js?v=1.1') }}"></script>

<script>
$(document).ready(function() {
    $("#export_excel").on("click", function() {
        var table = $('#loans_table').DataTable();
        var data = [];
        
        // Get headers
        var headers = [];
        $('#loans_table thead th').each(function() {
            if($(this).text() != 'Action') { // Skip Action column
                headers.push($(this).text().trim());
            }
        });
        data.push(headers);
        
        // Get visible data
        table.rows().every(function(rowIdx, tableLoop, rowLoop) {
            var rowData = this.data();
            var row = [
                rowData.loan_id,
                rowData['loan_product']['name'],
                rowData['borrower']['first_name'],
                rowData['borrower']['member_no'],
                rowData.release_date,
                rowData.applied_amount,
                $(rowData.status).text() // Extract text from HTML status
            ];
            data.push(row);
        });

        // Get footer total
        var footerRow = new Array(headers.length).fill('');
        footerRow[4] = 'Total Applied Amount:';
        footerRow[5] = $('#loans_table tfoot th:eq(1)').text();
        data.push(footerRow);
        
        // Create workbook
        var wb = XLSX.utils.book_new();
        var ws = XLSX.utils.aoa_to_sheet(data);
        
        // Add worksheet to workbook
        XLSX.utils.book_append_sheet(wb, ws, "Loans");
        
        // Generate Excel file
        XLSX.writeFile(wb, "loans_list.xlsx");
    });
});
</script>
@endsection