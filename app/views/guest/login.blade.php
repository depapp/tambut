@section('content')
<div class="uk-text-center">
    <div class="uk-vertical-align-middle" style="width: 250px;">

        {{ Form::open(array('url' => '/authenticate', 'class' => 'uk-panel uk-panel-box uk-form')) }}
            <div class="uk-form-row">
                {{ Form::text('email', null, array('class'=>'uk-width-1-1 uk-form-large', 'placeholder'=>'Email')) }}
            </div>
            <div class="uk-form-row">
                {{ Form::password('password', array('class'=>'uk-width-1-1 uk-form-large', 'placeholder'=>'Password')) }}
            </div>
            <div class="uk-form-row">
                {{ Form::submit('Login', array('class'=>'uk-width-1-1 uk-button uk-button-primary uk-button-large')) }}
            </div>

            <div class="uk-form-row">
                {{ link_to_route('guest.forgot', 'Anda lupa password?')}}
            </div>

        {{ Form::close() }}

    </div>
</div>
@stop