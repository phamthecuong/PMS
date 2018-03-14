<?php
    $special_id = str_random(40);
?>
{{-- <form method="GET" action="{{@$export_url}}" id="frm_{{$special_id}}"> --}}
<form method="POST" action="{{ asset('user/work_planning/planned_section') }}" id="frm_{{$special_id}}">
    {!! csrf_field() !!}
    <table id="t_{{$special_id}}" class="table table-striped table-bordered table-hover" width="100%" style="overflow-x: auto;">
        <thead>
            
        <!-- </thead> -->

        <?php 
            $filter_flg = false;
            foreach ($columns as $column)
            {
                if (isset($column['hasFilter']) && $column['hasFilter'])
                {
                    $filter_flg = true;
                    break;
                }
            }
        ?>

        @if ($filter_flg)
        <!-- <thead> -->
            <tr>
                @foreach ($columns as $column)
                    <th class="hasinput">
                        @if (isset($column['hasFilter']))
                            @if (@$column['filterType'] == 'dropdown')
                                @include('custom.form_select2',[
                                     'name' => $column['name'],
                                     'onchange' => 'filterUpdate("t_' . $special_id . '")',
                                     'items' => $column['items'],
                                     'value' => ''
                                ])
                            @elseif (@$column['filterType'] == 'super_input')
                                @if(!isset($column['hint']) || @$column['hint'] == true)
                                    <input onchange="filterUpdate('t_{{$special_id}}')" type="text" name="{{ $column['name'] }}" class="form-control" placeholder="{{ $column['title'] }}" />
                                    
                                    <a href="#" class="hint-custom" data-toggle="tooltip" title="" data-original-title="{!! trans('back_end.hintAdvancedFilter') !!}" data-placement="bottom">
                                        <i class="icon-prepend fa fa-question-circle"></i>
                                    </a>
                                    
                                @else
                                    <!-- <input style="width: 80%" onkeyup="filterUpdate('t_{{$special_id}}')" type="text" name="{{ $column['name'] }}" class="form-control" placeholder="{{ $column['title'] }}" /> -->
                                @endif
                            @endif
                        @endif
                    </th>
                @endforeach
            </tr>
        
        @endif
            <tr>
                <th><input name="select_all" value="1" type="checkbox"></th>
                @foreach ($columns as $index => $column)
                    <?php if ($index == 0) continue; ?>
                    <th style="min-width: 50px">{{ $column['title'] }}</th>
                @endforeach
            </tr>
        </thead>
    </table>
</form>
<p class="form-group">
    <div class="btn-group dropup">
        <button style="margin-left: 5px" class="btn btn-danger" type="button" onclick="if (confirm('Are you sure?'))$('#frm_{{$special_id}}').submit(); return false">
            {{trans('wp.delete_selected_item')}}
        </button>
    </div>
</p>

@push('css')
    <style type="text/css">
        .select2-container .select2-choice .select2-arrow, .select2-selection__arrow {
            width: 12px !important;
        }
    </style>
@endpush

@push('script')
<script type="text/javascript">
    //
    // Updates "Select all" control in a data table
    //
    function updateDataTableSelectAllCtrl(table) {
        var $table             = table.table().node();
        var $chkbox_all        = $('tbody input[type="checkbox"]', $table);
        var $chkbox_checked    = $('tbody input[type="checkbox"]:checked', $table);
        var chkbox_select_all  = $('thead input[name="select_all"]', $table).get(0);

        // If none of the checkboxes are checked
        if ($chkbox_checked.length === 0) {
            chkbox_select_all.checked = false;
            if ('indeterminate' in chkbox_select_all) {
                chkbox_select_all.indeterminate = false;
            }

        // If all of the checkboxes are checked
        } else if ($chkbox_checked.length === $chkbox_all.length) {
            chkbox_select_all.checked = true;
            if ('indeterminate' in chkbox_select_all) {
                chkbox_select_all.indeterminate = false;
            }

        // If some of the checkboxes are checked
        } else {
            chkbox_select_all.checked = true;
            if ('indeterminate' in chkbox_select_all) {
                chkbox_select_all.indeterminate = true;
            }
        }
    }
    function filterUpdate(table_id)
    {
        $('#' + table_id).DataTable().draw();
    }     

    $(document).ready(function () {
        // Array holding selected row IDs
        var rows_selected = [];
        var table = $('#t_{{ $special_id }}').DataTable({
            dom: "<'dt-toolbar'<'col-xs-12 col-sm-6 hidden-xs'><'col-sm-6 col-xs-12 align-right hidden-xs'<'toolbar'>B>r>" +
            "<'autooverflow't>" +
            "<'dt-toolbar-footer'<'col-sm-6 col-xs-12 hidden-xs'i><'col-xs-12 col-sm-6'p>>",
            "autoWidth": true,
            "oLanguage": {
                "sSearch": '<span class="input-group-addon"><i class="glyphicon glyphicon-search"></i></span>'
            },
            ajax: {
                'url': '{{ $url }}',
                'type': 'GET',
                data: function (d) {
                    @foreach ($columns as $column)
                        @if (isset($column['hasFilter']) && $column['hasFilter'])
                            d['{{$column['name']}}'] = $('#t_{{$special_id}} [name="{{$column['name']}}"]').val();
                        @endif
                    @endforeach
                }
            },
            serverSide: true,
            columnDefs: [{
                'targets': 0,
                'searchable': false,
                'orderable': false,
                'width': '1%',
                'className': 'dt-body-center',
                'render': function (data, type, full, meta){
                    return '<input type="checkbox">';
                }
            }],
            columns: <?php echo json_encode($columns); ?>,
            order: [[1, 'asc']],
            rowCallback: function(row, data, dataIndex){
                // Get row ID
                var rowId = data.id;

                // If row ID is in the list of selected row IDs
                if ($.inArray(rowId, rows_selected) !== -1) {
                    $(row).find('input[type="checkbox"]').prop('checked', true);
                    $(row).addClass('selected');
                }
            },
            buttons: [
                'colvis'
            ],
            oLanguage: {
                sSearch: '<span class="input-group-addon"><i class="glyphicon glyphicon-search"></i></span>',
                @if (App::getLocale() != 'en')
                sProcessing: "Đang xử lý...",
                sLengthMenu: "Xem _MENU_ mục",
                sZeroRecords: "Không tìm thấy dòng nào phù hợp",
                sInfo: "Đang xem _START_ đến _END_ trong tổng số _TOTAL_ mục",
                sInfoEmpty: "Đang xem 0 đến 0 trong tổng số 0 mục",
                sInfoFiltered: "(được lọc từ _MAX_ mục)",
                sInfoPostFix: "",
                sSearch: "Tìm:",
                sUrl: "",
                oPaginate: {
                    sFirst: "Đầu",
                    sPrevious: "Trước",
                    sNext: "Tiếp",
                    sLast: "Cuối"
                },
                buttons: {
                    colvis: 'Cột hiển thị',
                    copy: 'Sao chép'
                }
                @else
                buttons: {
                    colvis: 'Display Item'
                }
                @endif
            }
        });

        // Handle click on checkbox
        $('#t_{{ $special_id }} tbody').on('click', 'input[type="checkbox"]', function(e){
            var $row = $(this).closest('tr');

            // Get row data
            var data = table.row($row).data();

            // Get row ID
            var rowId = data.id;

            // Determine whether row ID is in the list of selected row IDs 
            var index = $.inArray(rowId, rows_selected);

            // If checkbox is checked and row ID is not in list of selected row IDs
            if (this.checked && index === -1) {
                rows_selected.push(rowId);

            // Otherwise, if checkbox is not checked and row ID is in list of selected row IDs
            } else if (!this.checked && index !== -1) {
                rows_selected.splice(index, 1);
            }

            if (this.checked) {
                $row.addClass('selected');
            } else {
                $row.removeClass('selected');
            }

            // Update state of "Select all" control
            updateDataTableSelectAllCtrl(table);

            // Prevent click event from propagating to parent
            e.stopPropagation();
        });

        // Handle click on table cells with checkboxes
        $('#t_{{ $special_id }}').on('click', 'tbody td, thead th:first-child', function(e){
            $(this).parent().find('input[type="checkbox"]').trigger('click');
        });

        // Handle click on "Select all" control
        $('thead input[name="select_all"]', table.table().container()).on('click', function(e){
            if (this.checked) {
                $('#t_{{ $special_id }} tbody input[type="checkbox"]:not(:checked)').trigger('click');
            } else {
                $('#t_{{ $special_id }} tbody input[type="checkbox"]:checked').trigger('click');
            }

            // Prevent click event from propagating to parent
            e.stopPropagation();
        });

        // Handle table draw event
        table.on('draw', function(){
            // Update state of "Select all" control
            updateDataTableSelectAllCtrl(table);
        });

        

        // Handle form submission event 
        $('#frm_{{$special_id}}').on('submit', function(e){
            var form = this;
            if (form) {
                if (rows_selected.length == '0') {
                    alert('{{trans("wp.no_selected")}}');
                    return false;
                }
                $('input[name="id\[\]"]', form).remove();
                // Iterate over all selected checkboxes
                $.each(rows_selected, function(index, rowId){
                    // Create a hidden element
                    $(form).append(
                            $('<input>')
                                    .attr('type', 'hidden')
                                    .attr('name', 'id[]')
                                    .val(rowId)
                    );
                });
            }
        });

        $('#move_{{$special_id}}').on('submit', function(e){
            var form = this;
            
            if (rows_selected.length == 0) {
                alert('{{trans("wp.no_selected")}}');
                return false;
            }

            $('input[name="id\[\]"]', form).remove();
            // Iterate over all selected checkboxes
            $.each(rows_selected, function(index, rowId){
                // Create a hidden element 
                $(form).append(
                    $('<input>')
                        .attr('type', 'hidden')
                        .attr('name', 'id[]')
                        .val(rowId)
                );
            });
        });
    });
</script>
@endpush