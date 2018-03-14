<p>Tạo tài khoản thành công</p>
{!!trans('back_end.user_name')!!} : {!! $user_name !!}
<br />
{!!trans('back_end.password')!!} : {!!$password!!}
@if(isset($link))
<p>Click vào <a href="{{$link}}">đây</a> để kích hoạt tài khoản</p>
@endif
