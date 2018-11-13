@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div>
            <div class="card rounded-circle"><img style="width:70px; height:70px; border-radius:50%;" src="{!! $profile->avatare !!}"></div>
        </div>
        <div class="col-md-8">
            <div class="card">
                <form action="/user/create" method="post" enctype = "multipart/form-data">
                    <div class="card-header">
                        <div class="form-group">
                            <label for="exampleInputEmail1">Name</label>
                            <input type="text" class="form-control-plaintext" placeholder="{{ $profile->name }}" readonly>
                        </div>

                        <div class="form-group">
                            <label for="exampleInputEmail1">Email address</label>
                            <input type="text" class="form-control-plaintext" placeholder="{{ $profile->email }}" readonly>
                        </div>

                        <div class="form-group">
                            <label for="exampleInputEmail1">Birth date</label>
                            <input type="date" class="form-control" value="{{ $profile->birth_date }}" id="birth_date" name="birth_date" aria-describedby="emailHelp" placeholder="Enter email">
                        </div>

                        <div class="form-group">
                            <label for="exampleFormControlFile1">Example file input</label>
                            <input type="file" name="avatare" class="form-control-file" id="exampleFormControlFile1">
                        </div>
                        {{ csrf_field() }}
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>                    
                    <!--{{ $profile->birth_date }}-->
                </form>   
            </div>
           
        </div>
    </div>
</div>
@endsection
