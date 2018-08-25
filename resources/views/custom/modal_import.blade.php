<!-- The Modal -->
<div id="{{ $id }}" class="my-modal" style="z-index: 100">
<div class="ui-dialog ui-widget ui-widget-content ui-corner-all ui-front ui-dialog-buttons ui-draggable modal-content" tabindex="-1" role="dialog" aria-describedby="dialog_simple" aria-labelledby="ui-id-50" style="height: auto; width: 800px; top: 479px; left: 475px;">
    <div class="ui-dialog-titlebar ui-widget-header ui-corner-all ui-helper-clearfix">
                <span id="ui-id-50" class="ui-dialog-title">
                    <div class="widget-header"><h4><i class="fa fa-pencil-square-o"></i> {{  $modal_title }}</h4>
                    </div>
                </span>
        <button class="ui-dialog-titlebar-close"></button>
    </div>
    @php
    $lang = App::isLocale('en') ? 'en' : 'vi';
    @endphp
    <form ng-submit="{{ $event_submit }}">
        <div id="dialog_simple" class="ui-dialog-content ui-widget-content" style="width: auto; min-height: 0px; max-height: none; height: auto;">
            <div class="collapse-group">
                <div class="panel panel-default">
                    <div class="panel-heading" role="tab" id="headingThree">
                      <h4 class="panel-title">
                        <a role="button" data-toggle="collapse" href="#collapseThree" aria-expanded="true" aria-controls="collapseThree" class="trigger">
                          Thông Tin Chính
                        </a>
                      </h4>
                    </div>
                    <div id="collapseThree" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingThree">
                      <div class="panel-body">
                        
                        @foreach ($visible as $key => $value) 
                           @if($value['section'] == 'level_1') 
                                @if ($value['type'] == 'checkbox')
                                    @if (isset($value['child']))
                                        {!! Form::lbCheckbox($key, '', trans($key), [
                                            'ng-model' => $scope_form.'.'.$key,
                                            'ng-true-value' => 1,
                                            'ng-false-value' => 0
                                        ])!!}
                                        <span class="help-block" style="color: #b94a48;" ng-show="errors.{{ $key }}[0]" ng-bind="errors.{{ $key }}[0].toString()"></span>
                                        @foreach($value['child'] as $item)
                                            @if($visible[$item]['type'] == 'text' )
                                                {!! Form::lbText($item, '', trans($item), '', '', [
                                                    'ng-model' =>  $scope_form.'.'.$item,
                                                    'ng-class' => '{error: errors.'.$item.'[0]}'
                                                ]) !!}
                                                <span class="help-block" style="color: #b94a48;" ng-show="errors.{{ $item }}[0]" ng-bind="errors.{{ $item }}[0].toString()"></span>
                                            @else
                                                {!! Form::lbSelect($item, '', $visible[$item]['item'], trans($item), [
                                                    'ng-model' => $scope_form.'.'.$item, 
                                                    'ng-class' => '{error: errors.'.$item.'[0]}'
                                                ])!!} 
                                                <span class="help-block" style="color: #b94a48;" ng-show="errors.{{ $item }}[0]" ng-bind="errors.{{ $item }}[0].toString()"></span>
                                            @endif
                                        @endforeach
                                    @else
                                        {!! Form::lbCheckbox($key, '', trans($key), [
                                            'ng-model' =>  $scope_form.'.'.$key,
                                            'ng-true-value' => 1,
                                            'ng-false-value' => 0
                                        ])!!}
                                        <span class="help-block" style="color: #b94a48;" ng-show="errors.{{ $key }}[0]" ng-bind="errors.{{ $key }}[0].toString()"></span>
                                    @endif
                                @elseif ($value['type'] == 'check_select' || $value['type'] == 'select_special')
                                    {!! Form::lbSelect($key, '', $value['item'], trans($key), [
                                        'ng-model' => $scope_form.'.'.$key, 
                                        'ng-class' => '{error: errors.'.$key.'[0]}'
                                    ])!!}
                                    <span class="help-block" style="color: #b94a48;" ng-show="errors.{{ $key }}[0]" ng-bind="errors.{{ $key }}[0].toString()"></span>
                                @elseif ($value['type'] == 'select')
                                    @if (!isset($value['parent']))
                                        {!! Form::lbSelect($key, '', $value['item'], trans($key), [
                                            'ng-model' => $scope_form.'.'.$key, 
                                            'ng-class' => '{error: errors.'.$key.'[0]}'
                                        ])!!}
                                        <span class="help-block" style="color: #b94a48;" ng-show="errors.{{ $key }}[0]" ng-bind="errors.{{ $key }}[0].toString()"></span>
                                    @endif
                                @elseif ($value['type'] == 'text')
                                    @if (!isset($value['parent']))
                                        {!! Form::lbText($key, '', trans($key), '', '', [
                                            'ng-model' => $scope_form.'.'.$key, 
                                            'ng-class' => '{error: errors.'.$key.'[0]}'
                                        ]) !!}
                                        <span class="help-block" style="color: #b94a48;" ng-show="errors.{{ $key }}[0]" ng-bind="errors.{{ $key }}[0].toString()"></span>
                                    @endif
                                @endif
                            @endif 
                        @endforeach
                      </div>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading" role="tab" id="headingOne">
                      <h4 class="panel-title">
                        <a role="button" data-toggle="collapse" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne" class="trigger collapsed">
                          Thông Tin Chung
                        </a>
                      </h4>
                    </div>
                    <div id="collapseOne" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">
                      <div class="panel-body">
                        @foreach ($visible as $key => $value) 
                            @if($value['section'] == 'level_2') 
                                @if ($value['type'] == 'checkbox')
                                    @if (isset($value['child']))
                                        {!! Form::lbCheckbox($key, '', trans($key), [
                                            'ng-model' => $scope_form.'.'.$key,
                                            'ng-true-value' => 1,
                                            'ng-false-value' => 0
                                        ])!!}
                                        <span class="help-block" style="color: #b94a48;" ng-show="errors.{{ $key }}[0]" ng-bind="errors.{{ $key }}[0].toString()"></span>
                                        @foreach($value['child'] as $item)
                                            @if($visible[$item]['type'] == 'text' )
                                                {!! Form::lbText($item, '', trans($item), '', '', [
                                                    'ng-model' =>  $scope_form.'.'.$item,
                                                    'ng-class' => '{error: errors.'.$item.'[0]}'
                                                ]) !!}
                                                <span class="help-block" style="color: #b94a48;" ng-show="errors.{{ $item }}[0]" ng-bind="errors.{{ $item }}[0].toString()"></span>
                                            @else
                                                {!! Form::lbSelect($item, '', $visible[$item]['item'], trans($item), [
                                                    'ng-model' => $scope_form.'.'.$item, 
                                                    'ng-class' => '{error: errors.'.$item.'[0]}'
                                                ])!!} 
                                                <span class="help-block" style="color: #b94a48;" ng-show="errors.{{ $item }}[0]" ng-bind="errors.{{ $item }}[0].toString()"></span>
                                            @endif
                                        @endforeach
                                    @else
                                        {!! Form::lbCheckbox($key, '', trans($key), [
                                            'ng-model' =>  $scope_form.'.'.$key,
                                            'ng-true-value' => 1,
                                            'ng-false-value' => 0
                                        ])!!}
                                        <span class="help-block" style="color: #b94a48;" ng-show="errors.{{ $key }}[0]" ng-bind="errors.{{ $key }}[0].toString()"></span>
                                    @endif
                                @elseif ($value['type'] == 'check_select' || $value['type'] == 'select_special')
                                    {!! Form::lbSelect($key, '', $value['item'], trans($key), [
                                        'ng-model' => $scope_form.'.'.$key, 
                                        'ng-class' => '{error: errors.'.$key.'[0]}'
                                    ])!!}
                                    <span class="help-block" style="color: #b94a48;" ng-show="errors.{{ $key }}[0]" ng-bind="errors.{{ $key }}[0].toString()"></span>
                                @elseif ($value['type'] == 'select')
                                    @if (!isset($value['parent']))
                                        {!! Form::lbSelect($key, '', $value['item'], trans($key), [
                                            'ng-model' => $scope_form.'.'.$key, 
                                            'ng-class' => '{error: errors.'.$key.'[0]}'
                                        ])!!}
                                        <span class="help-block" style="color: #b94a48;" ng-show="errors.{{ $key }}[0]" ng-bind="errors.{{ $key }}[0].toString()"></span>
                                    @endif
                                @elseif ($value['type'] == 'text')
                                    @if (!isset($value['parent']))
                                        {!! Form::lbText($key, '', trans($key), '', '', [
                                            'ng-model' => $scope_form.'.'.$key, 
                                            'ng-class' => '{error: errors.'.$key.'[0]}'
                                        ]) !!}
                                        <span class="help-block" style="color: #b94a48;" ng-show="errors.{{ $key }}[0]" ng-bind="errors.{{ $key }}[0].toString()"></span>
                                    @endif
                                @endif
                            @endif
                        @endforeach
                      </div>
                    </div>
                </div>
                
            </div>
        </div>       
        <div class="ui-dialog-buttonpane ui-widget-content ui-helper-clearfix">
            <div class="ui-dialog-buttonset">
                
                <button type="button" class="btn btn-default" id="close"><i class="fa fa-times"></i>&nbsp; {{ $button_cancel }}</button>
                <span ng-if="ids != check">
                    <button type="button" class="btn btn-success" ng-click="reCheck()"><i class="fa fa-paper-plane"></i> Submit and Next</button>
                </span>
                <button type="submit" class="btn btn-primary"><i class="fa fa-pencil-square-o"></i>&nbsp;{{ $button_complete }}</button>
                
            </div>
        </div>
    </form>
</div>
</div>
<!-- End the Modal -->
@push('script')

    <script>
        $( function() {
            $( "#accordion" ).accordion({
              collapsible: true
            });
        } );
    </script>
    <script type="text/javascript">
        $(".open-button").on("click", function() {
            $(this).closest('.collapse-group').find('.collapse').collapse('show');
        });

        $(".close-button").on("click", function() {
            $(this).closest('.collapse-group').find('.collapse').collapse('hide');
        });
        $(document).ready(function () {
            // Get the modal
            var modal = document.getElementById('{{ $id }}');
            // var modal = document.getElementsByClassName('mode');

            // When the user clicks the button, open the modal
            $(document).on('click','{{ $element_show }}', function () {
                $('#{{ $id }}').show();
            });

            // When the user clicks on <span> (x), close the modal
            $('.ui-dialog-titlebar-close, #close').click(function () {
                $('#{{ $id }}').hide();
            });
        });

</script>


@endpush
@push('css')
    <style type="text/css">
        a:hover, a:visited, a:link, a:active {
          text-decoration: none;
        }
        .menu-on-top aside#left-panel{
            z-index: 1;
        }

        .controls {
            margin-bottom: 10px;
        }

        .collapse-group {
            margin-bottom: 10px;
        }

        .panel-title .trigger:before {
            content: '\e082';
            font-family: 'Glyphicons Halflings';
            vertical-align: text-bottom;
        }

        .panel-title .trigger.collapsed:before {
            content: '\e081';
        }
    
        .error {
            background-color: #f2dede;
        }
        .my-modal {
            display: none; /* Hidden by default */
            position: fixed; /* Stay in place */
            z-index: 1; /* Sit on top */
            padding-top: 0px; /* Location of the box */
            left: 0;
            top: 0;
            width: 100%; /* Full width */
            height: 100%; /* Full height */
            /*overflow: auto;  Enable scroll if needed */
            background-color: rgb(0,0,0); /* Fallback color */
            background-color: rgba(0,0,0,0.3); /* Black w/ opacity */
        }

        .modal-content {
            position: relative !important;
            margin: auto !important;
            top: 70px !important;
            left: 0 !important;
            box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2),0 6px 20px 0 rgba(0,0,0,0.19) !important;
            -webkit-animation-duration: 0.4s;
            animation-duration: 0.4s
        }
        .ui-dialog .ui-dialog-content {
            height: 400px !important;
        }
        .ui-draggable .ui-dialog-titlebar {
            cursor: auto;
        }
    </style>
@endpush