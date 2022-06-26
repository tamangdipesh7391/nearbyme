@extends('frontend.main')
@section('content')
  <!-- ======= Hero Section ======= -->
  <section class="hero-section" id="hero">

    <div class="wave">

      <svg width="100%" height="355px" viewBox="0 0 1920 355" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
        <g id="Page-1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
          <g id="Apple-TV" transform="translate(0.000000, -402.000000)" fill="#FFFFFF">
            <path d="M0,439.134243 C175.04074,464.89273 327.944386,477.771974 458.710937,477.771974 C654.860765,477.771974 870.645295,442.632362 1205.9828,410.192501 C1429.54114,388.565926 1667.54687,411.092417 1920,477.771974 L1920,757 L1017.15166,757 L0,757 L0,439.134243 Z" id="Path"></path>
          </g>
        </g>
      </svg>

    </div>

    <div class="container">
      <div class="row align-items-center">
       
        <div class="col-12 hero-text-image">
        
          <div class="row">
            <div class="col-lg-8 text-center text-lg-start">
              <form action="{{route('home.search')}}" class="form-group mb-2 rounded d-flex" method="POST">
                @csrf
                <input required name="search" placeholder="Start searching here ..." type="search" id="home_search" class="form-control" style="border-top-left-radius: 10px;border-bottom-left-radius: 10px;width:60%;border:2px solid blue;">
                <input type="submit" class="form-control" value="Search" style="border-top-right-radius: 10px;border-bottom-right-radius: 10px;width:20%;border:2px solid green;">
              </form>
              <h1 data-aos="fade-right">Save your time by using NearByMe</h1>
              <p class="mb-5" data-aos="fade-right" data-aos-delay="100">
                NearByMe is a web application that helps you to find the nearest service providers from your location.
              </p>
             
               
              
              <p data-aos="fade-right" data-aos-delay="200" data-aos-offset="-500"><a href="#getstarted" class="btn btn-outline-white">Get started</a></p>
            </div>
            <div class="col-lg-4 iphone-wrap">
              <img src="{{url('frontend/assets/img/phone_1.png')}}" alt="Image" class="phone-1" data-aos="fade-right">
              <img src="{{url('frontend/assets/img/phone_2.png')}}" alt="Image" class="phone-2" data-aos="fade-right" data-aos-delay="200">
            </div>
          </div>
        </div>
      </div>
    </div>

  </section><!-- End Hero -->
 @if (count($professions) > 0)
    <!-- ======= Home Section ======= -->
    <section class="section" id="services">
      <div class="container">

        <div class="row justify-content-center text-center mb-5">
          <div class="col-md-5" data-aos="fade-up">
            <h2 class="section-heading">Save your time by using NearByMe</h2>
          </div>
        </div>

        <div class="row">
         
            @foreach ($professions as $profession)
            <div style="cursor: pointer" class="col-md-4 shadow mt-4 pt-3" data-aos="fade-up" data-aos-delay="">
              <div class="feature-1 text-center">
                <div class="wrap-icon icon-1">
                  @if ($profession->avatar != null)
                    <img src="{{url('profession_avatar/'.$profession->avatar)}}" alt="{{$profession->name}}" style="border-radius: 50%;" class=" img-thumbnail" width="100px" height="100px">
                  @else
                    <img src="{{url('profession_avatar/default.jpg')}}" alt="{{$profession->name}}" style="border-radius: 50%;" class=" img-thumbnail" width="100px" height="100px">
                    
                  @endif
                </div>
                <h3 class="mb-3"><a href="">{{$profession->name}}</a></h3>
                <p>{!!$profession->meta_description!!}</p>
              </div>
            </div>
            @endforeach
            <div class="d-flex justify-content-center mt-4">
              {{$professions->links('pagination::bootstrap-4')}}
            </div>
           
            
        
          
        </div>

      </div>
    </section>
  @endif
 
    <!-- ======= CTA Section ======= -->
    <section class="section cta-section" id="getstarted">
      <div class="container">
      
        <div class="row align-items-center">
          <div class="col-md-6 me-auto text-center text-md-start mb-5 mb-md-0">
            <h2>Start Using our Platform</h2>
          </div>
          <div class="col-md-5 text-center text-md-end">
            <p><a href="#" class="btn btn-outline-info d-inline-flex align-items-center"><i class="bi bi-people-fill"></i><span>Sell Service</span></a> 
              <a href="#" class="btn btn-outline-info d-inline-flex align-items-center"><i class="bi bi-people"></i><span>Use Service</span></a></p>
          </div>
        </div>
      </div>
    </section><!-- End CTA Section -->

@endsection    