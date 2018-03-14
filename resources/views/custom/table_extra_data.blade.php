<?php
    $columns = array_merge([
        [
            'class' => 'details-control',
            'orderable' => false,
            'data' => null,
            'defaultContent' => '',
            'title' => '',
            'flag' => true,
        ]
    ], $columns);
?>
<table id="{{ $table_id }}" class="table table-striped table-bordered" width="100%">
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
                <th class="hasinput" style="white-space: nowrap;">
                    @if (isset($column['hasFilter']))
                        @if (@$column['filterType'] == 'dropdown')
                            @if ($table_id == 'payment_condition')
                                @include('custom.form_select2_payment_condition',[
                                     'name' => $column['name'],
                                     'onchange' => 'filterUpdate("' . $table_id . '")',
                                     'items' => $column['items'],
                                     'value' => ''
                                ])
                            @else
                                @include('custom.form_select2',[
                                     'name' => $column['name'],
                                     'onchange' => 'filterUpdate("' . $table_id . '")',
                                     'items' => $column['items'],
                                     'value' => ''
                                ])
                            @endif
                        @elseif (@$column['filterType'] == 'super_input')
                            @if(!isset($column['hint']) || @$column['hint'] == true)
                                <input onchange="filterUpdate('{{$table_id}}')" type="text" name="{{ $column['name'] }}" class="form-control" placeholder="{{ $column['title'] }}" />

                                <a href="#" class="hint-custom" data-toggle="tooltip" title="" data-original-title="{!! trans('back_end.hintAdvancedFilter') !!}" data-placement="bottom">
                                    <i class="icon-prepend fa fa-question-circle"></i>
                                </a>
                                
                            @else
                                <!-- <input style="width: 80%" onkeyup="filterUpdate('{{$table_id}}')" type="text" name="{{ $column['name'] }}" class="form-control" placeholder="{{ $column['title'] }}" /> -->
                            @endif
                        @endif
                    @endif
                </th>
            @endforeach
        </tr>
    @endif
    <tr>
        @foreach ($columns as $column)
            <th style="white-space: nowrap;">{{ $column['title'] }}</th>
        @endforeach
    </tr>
    </thead>
    <tbody></tbody>
</table>

@push('css')
    <style type="text/css">
        .select2-container .select2-choice .select2-arrow, .select2-selection__arrow {
            width: 12px !important;
        }
        @if ($table_id == 'success' || $table_id == 'update' || $table_id == 'error' || $table_id == 'ignore' || $table_id == 'new')
        .dt-button-collection li:nth-child(1) {display: none}
        .dt-button-collection li:nth-child(2) {display: none}
        @endif
    </style>
@endpush

@push('script')
<script type="text/javascript">

    var t_{{ $table_id }};
    
    $(document).ready(function () {
        var responsiveHelper_datatable_fixed_column = undefined;

        var breakpointDefinition = {
            tablet: 1024,
            phone: 480
        };
        t_{{ $table_id }} = $('#{{ $table_id }}').DataTable({
            // "sDom": "<'dt-toolbar'<'col-xs-12 col-sm-6 hidden-xs'f><'col-sm-6 col-xs-12 hidden-xs'<'toolbar'>>r>"+
            // 		"<'autooverflow't>"+
            // 		"<'dt-toolbar-footer'<'col-sm-6 col-xs-12 hidden-xs'i><'col-xs-12 col-sm-6'p>>",
            dom: "<'dt-toolbar'<'col-xs-12 col-sm-6 hidden-xs'><'col-sm-6 col-xs-12 align-right hidden-xs'<'toolbar'>B>r>" +
            "<'autooverflow't>" +
            "<'dt-toolbar-footer'<'col-sm-6 col-xs-12 hidden-xs'i><'col-xs-12 col-sm-6'p>>",
            "autoWidth": true,
            "oLanguage": {
                "sSearch": '<span class="input-group-addon"><i class="glyphicon glyphicon-search"></i></span>'
            },
            preDrawCallback: function () {
                // Initialize the responsive datatables helper once.
                if (!responsiveHelper_datatable_fixed_column) {
                    responsiveHelper_datatable_fixed_column = new ResponsiveDatatablesHelper($('#{{ $table_id }}'), breakpointDefinition);
                }
            },
            rowCallback: function (nRow) {
                responsiveHelper_datatable_fixed_column.createExpandIcon(nRow);
            },
            drawCallback: function (oSettings) {

                responsiveHelper_datatable_fixed_column.respond();
            },
            buttons: [
                'colvis'
            ],
            serverSide: true,
            @if ($table_id == 'ri_list')
            "columnDefs": [
                { "visible": false, "targets": [12] },
                { "visible": false, "targets": [13] },
            ],
            @endif
            ajax: {
                url: '{{ $url }}',
                type: '{{ $method or "GET"}}',
                data: function (d) {
                    @foreach ($columns as $column)
                        @if (isset($column['hasFilter']) && $column['hasFilter'])
                            d['{{$column['name']}}'] = $('#{{$table_id}} [name="{{$column['name']}}"]').val();
                        @endif
                    @endforeach
                }
            },
            stateSave: true,
            columns: <?php echo json_encode($columns); ?>,
            pageLength: {{ $pageLength or 50 }},
            order: [[1, "asc"]],
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

        function format(d) {
            return d.extra_view;
        }

        $('#{{ $table_id }} tbody').on('click', 'td.details-control', function () {
            var tr = $(this).closest('tr');
            var row = t_{{ $table_id }}.row(tr);

            if (row.child.isShown()) {
                // This row is already open - close it
                row.child.hide();
                tr.removeClass('shown');
            }
            else {
                // Open this row
                row.child(format(row.data())).show();
                tr.addClass('shown');
            }
        });
    });

    // $("#{{ $table_id }} thead th input[type=text]").on( 'keyup change', function () {

    //     t_{{ $table_id }}
    //         .column( $(this).parent().index()+':visible' )
    //             .draw();

    // });

    function filterUpdate(table_id) {
        $('#' + table_id).DataTable().draw();
    }
</script>

@endpush