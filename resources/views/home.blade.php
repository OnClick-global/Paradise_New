@extends('layouts.blank')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 mt-3">
                <div class="card mt-3">

                    <div class="card-body text-center">
                        @php($restaurant_logo=\App\Model\BusinessSetting::where(['key'=>'logo'])->first()->value)
                        <a href="{{route('admin.dashboard')}}">  <img class=""  {{route('admin.dashboard')}} style="width: 200px!important"
                             onerror="this.src='{{asset('public/assets/admin/img/160x160/img2.jpg')}}'"
                             src="{{asset('storage/app/public/restaurant/'.$restaurant_logo)}}"
                             alt="Logo"> </a>
                        <br><hr>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
