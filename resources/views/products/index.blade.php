@extends('layouts.app')

@section('content')
    <example-component></example-component>
    {{--<div class="container">--}}
        {{--<div class="row justify-content-center">--}}
            {{--<div class="col-md-8">--}}
                {{--<div class="card">--}}
                    {{--<div class="card-header">{{ __('Login') }}</div>--}}

                    {{--<div class="card-body">--}}
                        {{--<form method="POST" action="{{ route('products.store') }}">--}}
                            {{--@csrf--}}

                            {{--<div class="form-group row">--}}
                                {{--<label for="name" class="col-sm-4 col-form-label text-md-right">Product name</label>--}}

                                {{--<div class="col-md-6">--}}
                                    {{--<input id="name" type="text"--}}
                                           {{--class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}"--}}
                                           {{--name="name" value="{{ old('name') }}" required autofocus>--}}

                                    {{--@if ($errors->has('name'))--}}
                                        {{--<span class="invalid-feedback">--}}
                                        {{--<strong>{{ $errors->first('name') }}</strong>--}}
                                    {{--</span>--}}
                                    {{--@endif--}}
                                {{--</div>--}}
                            {{--</div>--}}

                            {{--<div class="form-group row">--}}
                                {{--<label for="quantity" class="col-md-4 col-form-label text-md-right">Quantity in--}}
                                    {{--stock</label>--}}

                                {{--<div class="col-md-6">--}}
                                    {{--<input id="quantity" type="number"--}}
                                           {{--class="form-control{{ $errors->has('quantity') ? ' is-invalid' : '' }}"--}}
                                           {{--name="quantity" required>--}}

                                    {{--@if ($errors->has('quantity'))--}}
                                        {{--<span class="invalid-feedback">--}}
                                        {{--<strong>{{ $errors->first('quantity') }}</strong>--}}
                                    {{--</span>--}}
                                    {{--@endif--}}
                                {{--</div>--}}
                            {{--</div>--}}
                            {{--<div class="form-group row">--}}
                                {{--<label for="price" class="col-md-4 col-form-label text-md-right">Price per item</label>--}}

                                {{--<div class="col-md-6">--}}
                                    {{--<input id="price" type="number"--}}
                                           {{--class="form-control{{ $errors->has('price') ? ' is-invalid' : '' }}"--}}
                                           {{--name="price" required>--}}

                                    {{--@if ($errors->has('price'))--}}
                                        {{--<span class="invalid-feedback">--}}
                                        {{--<strong>{{ $errors->first('price') }}</strong>--}}
                                    {{--</span>--}}
                                    {{--@endif--}}
                                {{--</div>--}}
                            {{--</div>--}}

                            {{--<div class="form-group row mb-0">--}}
                                {{--<div class="col-md-8 offset-md-4">--}}
                                    {{--<button type="submit" class="btn btn-primary submit">--}}
                                        {{--{{ __('Submit') }}--}}
                                    {{--</button>--}}
                                {{--</div>--}}
                            {{--</div>--}}
                        {{--</form>--}}
                        {{--<p>Total sum of all products is: {{$total ?? 0}}</p>--}}
                        {{--<ul>--}}
                            {{--@forelse($products as $product)--}}
                                {{--<li>--}}
                                    {{--Product Name: {{$product->name}} <br>--}}
                                    {{--Quantity in stock: {{$product->quantity}} <br>--}}
                                    {{--Price per item: {{$product->price}} <br>--}}
                                    {{--Datetime submitted: {{$product->submitted}} <br>--}}
                                    {{--Total value number: {{$product->total}} <br>--}}
                                    {{--<hr>--}}
                                {{--</li>--}}
                            {{--@empty--}}
                                {{--<li>Please submit some products</li>--}}
                            {{--@endforelse--}}
                        {{--</ul>--}}

                    {{--</div>--}}

                {{--</div>--}}

            {{--</div>--}}
        {{--</div>--}}
    {{--</div>--}}

@endsection
