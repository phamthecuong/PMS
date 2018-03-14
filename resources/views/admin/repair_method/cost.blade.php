@extends('front-end.layouts.app')

@section('backend')
    active
@endsection

@section('side_menu_repair_method')
    active
@endsection

@section('content')
    @include('front-end.layouts.partials.heading', [
        'icon' => 'fa-list-ol',
        'text1' => trans('back_end.repair_method'),
        'text2' => trans('back_end.cost')
    ])
    <?php 

    ?>
    <section id="widget-grid">
        <div class="row">
            <article class="col-lg-6">
                @box_open(trans('back_end.cost'))
                <div>
                    <div class="widget-body">
                        <?php 
                            $lang = App::getLocale() == 'en' ? 'en' : 'vn';
                            $costs = $method->costs;
                        ?>
                        <legend>
                            {{ trans('back_end.cost_config_for') }}: <b><?= $method->{'name_' . $lang} ?></b>. (1000 VND)
                        </legend>
                        @if ($errors->any())
                            @foreach($errors->all() as $error)
                              <div class="alert alert-danger alert-dismissible" role="alert">
                                  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                  {{ $error }}
                              </div>
                              <?php break; ?>
                            @endforeach
                        @endif
                        {!! Form::open(array("url" => "/admin/repair_methods/$method->id/cost", "method" => "POST")) !!}
                        
                        @foreach ($orgs as $org)
                            <?php
                                $value = 0;
                                foreach ($costs as $c) 
                                {
                                    if ($c->organization_id == $org->id)
                                    {
                                        $value = $c->cost;
                                        break;
                                    }
                                }
                            ?>
                            {!! Form::lbText("cost[$org->id]", $value, $org->{'name_' . $lang}) !!}
                        @endforeach
                        <div class="widget-footer">
                            {!! Form::lbSubmit() !!}
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
                @box_close()
            </article>
        </div>
    </section>

@endsection
