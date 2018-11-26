@extends('layouts.app',['title'=> $title])
@section('content')
<style type="text/css">
</style>
<section class="top-section">
	<div class="container">
		<div class="row site-pro">
			<div class="col-md-2 img-box">
			   <a href="javascript:void(0)" class="files-select-mock">
			    @if (isset($siteDetail->site_image) && file_exists(public_path('/upload/sites').'/'.$siteDetail->site_image)) 
					<img src="{{url('/').'/public/upload/sites/'.$siteDetail->site_image}}" alt="" title=""  id="siteimage"/>
				   @else 
                   <img src="{{ asset('images/default.jpg') }}" alt="" title="" id="siteimage"/>
				   @endif
				   <input type="file" class="file-select" name="image" id="imgep" siteid="{{$siteDetail->id}}">
				 </a>
			</div>
			<div class="col-md-10 site-pro">
				<h4>{{$siteDetail->name}}</h4>
				<a href="#"> {{$siteDetail->url}} </a>
			</div>
		</div>
		<div class="row nav-box">
			<div class="col-md-9 left-nav">
				<ul class="nav nav-pills">
						<li class="nav-item active">
							<a  data-toggle="pill" class="nav-link"  href="#menu1">Overview <span class="sr-only">(current)</span></a>
						</li>
						<li class="nav-item">
							<a data-toggle="pill"  class="nav-link"  href="#menu2">Billing</a>
						</li>
					</ul>
			
			</div>
			 <div class="col-md-3 right-nav">
				<!-- <div class="dropdown">
					<a class="dropdown-toggle" data-toggle="dropdown">
						<i class="fa fa-cog" aria-hidden="true"></i>
					</a>
					<div class="dropdown-menu">
						<a class="dropdown-item" href="#">Link 1</a>
						<a class="dropdown-item" href="#">Link 2</a>
						<a class="dropdown-item" href="#">Link 3</a>
					</div>
				</div> -->
				<a href="{{$siteDetail->url}}/wp-admin"  target="_blank" class="btn btn-admin"> wp admin </a>
			</div>
		</div>
		<div class="tab-content">
<div class="tab-pane  in active" id="menu1">
	<div class="container ">
		<div class="row">
			<div class="col-md-8">
			<div class="card custome-card">
				<div class="card-header">
					Services
				</div>
				<div class="card-body">
					<div class="col-md-12">
					  <div class="card-body">
                       @php $totalAmount=$siteDetail->parent['plan_amount']/100; @endphp
                        <div class="row">
                        <div class="col-md-8">
                         {{$siteDetail->parent['name']}}
                        </div>
                        <div class="col-md-2">
                        <h2>${{$siteDetail->parent['plan_amount']/100}}</h2>
                        </div>
                        </div>
                       @if(!empty($siteDetail->parent->children))
                        @foreach($siteDetail->parent->children as $service)
                        @php $totalAmount = $totalAmount+ $service['plan_amount']/100; @endphp 
                        <h2>{{$service['name']}}</h2>
                        <div class="row">
                        <div class="col-md-8">
                        SEO
                        </div>
                        <div class="col-md-2">
                        <h2>${{$service['plan_amount']/100}}</h2>
                        </div>
                        </div>
                        @endforeach
                        @endif 
					    Total :- <h4>${{ $totalAmount}}/mo</h4>
                       </div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-md-4">
             <div class="card custome-card">
				<div class="card-header">
					Contact 
				</div>
				<div class="card-body">
					
				</div>
			</div>
			</div>
		</div>
	</div>
</div>
<div class="tab-pane fade" id="menu2">
	<div class="container " >
		<div class="row " >
			<div class="col-md-12">
			<div class="card custome-card">
				<div class="card-header">
					Client Billing History
				</div>
				<div class="card-body">
					<p class="date-active"> Active since {{ Auth::user()->created_at->format('F Y') }}  </p>
				     @if(!empty($siteDetail->parent->invoicelist))
					@foreach($siteDetail->parent->invoicelist as $invoicelist)
					<div class="row paid-box align-items-center">
						<div class="col-md-2 paid-btn-box">
							<a href="#" class="btn btn-success btn-paid"> {{$invoicelist['status'] }} </a>
						</div>
						<div class="col-md-2 invoice-box">
							<a href="{{URL('/view-invoice-pdf')}}/{{{base64_encode($invoicelist['id'])}}}" target="_blank">View Invoice</a>
						</div>
						<div class="col-md-2 date-box">
							{{$invoicelist['created_at']->format('m/d/y') }}
						</div>
						<div class="col-md-6 text-right price-box">
							<b>${{$invoicelist['amount']['plan_amount']/100 }}</b>
						</div>
					</div>
					@endforeach
					@endif
				</div>
			</div>
		</div>
		</div>
	</div>
</div>
	</div>
	</div>
</section> 
<script type="text/javascript">
	function readURL(input) {
		var fileTypes = ['jpg', 'jpeg', 'png'];
      if (input.files && input.files[0]) {
    	var extension = input.files[0].name.split('.').pop().toLowerCase(),  //file extension from input file
            isSuccess = fileTypes.indexOf(extension) > -1;
		if (isSuccess) {
          var reader = new FileReader();
          reader.onload = function (e) {
				console.log("image url");
	            $('#siteimage').attr('src', e.target.result);
	            var dataimg = new FormData();
			    dataimg.append('img',input.files[0]);
			    dataimg.append("_token", "{{ csrf_token() }}");
			    dataimg.append("id",$('#imgep').attr('siteid'));
		        $.ajax({
		            type:'POST',
		            url: "http://localhost/varo/image-upload",
		            data:dataimg,
		            cache:false,
		            contentType: false,
		            processData: false,
		            success:function(data){
		                console.log("success");
		                console.log(data);
		            },
		            error: function(data){
		                console.log("error");
		                console.log(data);
		            }
		        });
	        }
            reader.readAsDataURL(input.files[0]);
		    }else{
				alert("please enter a valid image");	
			}
		}else{
         alert("please enter a valid image");
		}
        
}
	 $('body').on('change', '#imgep', function() {
	    readURL(this);
	});


</script>

@endsection