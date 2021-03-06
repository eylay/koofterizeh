@extends('layouts.dashboard')
@section('title')
    اطلاعات سفارش برای پیک
@endsection
@section('content')

    @master

        @if ($orders->count())

            <div class="tile">

                <h2 class="mb-3"> سفارشات جدید </h2>

                <div class="row justify-content-center">
                    @foreach ($orders as $transaction)
                        <div class="col-md-4 my-2">
                            <div class="card">
                                <form class="card-body" action="{{route('transaction.set_peyk', $transaction->id)}}" method="post">
                                    @csrf
                                    <h5> <i class="material-icons icon">list</i> اقلام سفارش </h5>
                                    <ul>
                                        @foreach ($transaction->items as $item)
                                            <li>
                                                {{$item->count}}
                                                عدد
                                                <a href="{{$item->food->public_link()}}"> {{$item->food->title}} </a>
                                                از
                                                <a href="{{$item->food->cook->dashboard_link()}}"> {{$item->food->cook->full_name()}} </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                    <hr>
                                    <h5> <i class="material-icons icon">home_work</i> آدرس مشتری </h5>
                                    <p> {{$item->transaction->address->body}} </p>
                                    <hr>
                                    <h5> <i class="material-icons icon">access_time</i> زمان و تاریخ تحویل </h5>
                                    <p> {{human_date($transaction->delivery)}} - ساعت {{$transaction->time}} </p>
                                    <hr>
                                    <h5 class="mb-3"> انتخاب پیک </h5>
                                    <select class="select2" name="peyk" data-placeholder="-- انتخاب پیک --" required>
                                        <option value=""></option>
                                        @if (settings('deliver_type') != 'peyk')
                                            <option value="0"> تحویل به آژانس </option>
                                        @endif
                                        @foreach ($peyks as $peyk)
                                            <option value="{{$peyk->id}}">{{$peyk->full_name()}} - {{$peyk->mobile}}</option>
                                        @endforeach
                                    </select>
                                    @if (settings('deliver_type') != 'peyk')
                                        <div class="alert alert-warning mt-2">
                                            این سفارش زمانی که نوع تحویل روی پیک بوده توسط مشتری ثبت شده است.
                                            حال که نوع تحویل روی آژانس قرار دارد، سفارشات بعدی نیازی به تنظیم پیک نخواهد داشت.
                                            درصورتی که تمایل دارید این سفارش نیز توسط آژانس تحویل داده شود
                                            از منوی انتخاب پیک گزینه تحویل به آژانس را انتخاب کنید.
                                        </div>
                                    @endif
                                    <div class="text-center mt-3">
                                        <button type="submit" class="btn btn-outline-primary"> تایید </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>

            </div>

        @endif

        @if ($pending_comments->count() || $fresh_cooks->count() || $pending_foods->count())
            <div class="tile">
                <div class="container">
                    <div class="row justify-content-center">
                        @if ($count = $fresh_cooks->count())
                            <div class="col-md-4">
                                <span class="text-info ml-1">
                                    @if ($count > 9)
                                        <i class="material-icons">filter_9_plus</i>
                                    @else
                                        <i class="material-icons icon">filter_{{$count}}</i>
                                    @endif
                                </span>
                                شما
                                <b class="text-primary mx-1">{{$count == 1 ? 'یک' : $count}}</b>
                                درخواست جدید برای همکاری دارید.
                                <hr>
                                <a href="{{route('cook.fresh_requests')}}" class="btn btn-primary"> مدیریت درخواست ها </a>
                            </div>
                        @endif
                        @if ($count = $pending_comments->count())
                            <div class="col-md-4">
                                <span class="text-info ml-1">
                                    @if ($count > 9)
                                        <i class="material-icons">filter_9_plus</i>
                                    @else
                                        <i class="material-icons icon">filter_{{$count}}</i>
                                    @endif
                                </span>
                                شما
                                <b class="text-primary mx-1">{{$count == 1 ? 'یک' : $count}}</b>
                                کامنت معلق دارید.
                                <hr>
                                <a href="{{route('comment.index')}}" class="btn btn-primary"> مدیریت کامنت ها </a>
                            </div>
                        @endif
                        @if ($count = $pending_foods->count())
                            <div class="col-md-4">
                                <span class="text-info ml-1">
                                    @if ($count > 9)
                                        <i class="material-icons">filter_9_plus</i>
                                    @else
                                        <i class="material-icons icon">filter_{{$count}}</i>
                                    @endif
                                </span>
                                شما
                                <b class="text-primary mx-1">{{$count == 1 ? 'یک' : $count}}</b>
                                غذا جهت بررسی دارید
                                <hr>
                                <a href="{{route('food.index')}}?confirmed=0" class="btn btn-primary"> مدیریت غذا ها </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif
        <div class="tile">
            بعدا در این قسمت آمار هایی برای تحلیل عملکرد کاربران به شما داده میشود.
        </div>
    @endmaster

    @cook
        <div class="tile">

            @if (!$cook)
                <div class="alert alert-danger">
                    در حساب کاربری شما خطایی رخ داده. لطفا با پشتیبانی هماهنگ کنید.
                </div>
            @elseif (!$cook->active)
                <div class="alert alert-danger">
                    حساب شما درحال حاضر
                    <b> غیرفعال </b>
                    میباشد.
                </div>
            @else
                <div class="row justify-content-center text-center">
                    <div class="col-lg-4 col-md-6">
                        <div class="card">
                            <div class="card-body">
                                درآمد قابل برداشت
                                <hr>
                                <b class="calibri text-info"> {{nf(current_cook()->balance())}} </b>
                                تومان
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="card">
                            <div class="card-body">
                                مجموع درآمد شما
                                <hr>
                                <b class="calibri text-info"> {{nf(current_cook()->total_income())}} </b>
                                تومان
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if ($cook && $cook->modify_reason)
                @if ($cook->fresh)
                    <div class="alert alert-success">
                        اصلاحات مورد نظر شما با موفقیت انجام شده.
                        منتظر تایید ناظر باشید.
                        <hr>
                        <a href="{{route('cook.cook_edit', $cook->uid)}}" class="btn btn-outline-dark"> اصلاح مجدد </a>
                    </div>
                @else
                    <div class="alert alert-warning">
                        برای شما درخواست اصلاح ارسال شده است.
                        برای فعال شدن حساب کاربری خود، اصلاحات مورد نیاز را باید انجام دهید.
                        <hr>
                        علت نیاز به اصلاح : <b>{{$cook->modify_reason}}</b>
                        <br><br>
                        <a href="{{route('cook.cook_edit', $cook->uid)}}" class="btn btn-outline-dark"> ویرایش اطلاعات و اعمال اصلاحات </a>
                    </div>
                @endif
            @endif
        </div>
    @endcook


    @customer
        <div class="tile">
            <div class="row justify-content-center">
                @foreach ($transactions as $transaction)
                    @foreach ($transaction->items as $item)
                        @unless ($customer->review_on($item->food_id))

                            <div class="col-md-6">
                                <div class="card text-center">
                                    <div class="card-body">
                                        <h5> {{$item->food->title}} </h5>
                                        <h6> {{$item->food->cook->full_name()}} </h6>
                                        <hr>
                                        <p> از سفارش خود راضی بودید؟ </p>
                                        <form class="my-2" action="{{route('review.store')}}" method="post" data-selected="0">
                                            @csrf
                                            <input type="hidden" name="food_uid" value="{{$item->food->uid}}">

                                            <div class="rating"></div>

                                            <textarea name="body" rows="4" class="form-control hidden my-3" placeholder="توضیحات (اختیاری)"></textarea>

                                            <div class="w-100 my-3"></div>

                                            <button type="submit" class="btn btn-primary"> ثبت کردن </button>

                                        </form>
                                    </div>
                                </div>
                            </div>

                        @endunless
                    @endforeach
                @endforeach
            </div>
            <div class="text-center my-3">
                <a href="{{route('transaction.index')}}" class="btn btn-primary"> لیست سفارشات شما </a>
            </div>
        </div>
    @endcustomer


    @peyk

        <div class="tile">
            <div class="row justify-content-center text-center">
                <div class="col-lg-4 col-md-6">
                    <div class="card">
                        <div class="card-body">
                            درآمد قابل برداشت
                            <hr>
                            <b class="calibri text-info"> {{nf(current_peyk()->balance())}} </b>
                            تومان
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="card">
                        <div class="card-body">
                            مجموع درآمد شما
                            <hr>
                            <b class="calibri text-info"> {{nf(current_peyk()->total_income())}} </b>
                            تومان
                        </div>
                    </div>
                </div>
            </div>
        </div>

    @endpeyk

    @admin

        <div class="alert alert-info">
            قسمت حسابداری و گزارش فروش پس از اولین فروش فعال خواهد شد.
        </div>

    @endadmin

@endsection
