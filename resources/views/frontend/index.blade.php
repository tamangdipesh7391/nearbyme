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
            <form action="{{route('home.search')}}" class="form-group mb-2 rounded d-flex home-search-form" method="POST">
              @csrf
              {{-- <input required name="search" placeholder="Start searching here ..." type="search" id="home_search" class="form-control" > --}}
              <select name="search" id="home_search" class="form-control">
                <option selected disabled>Choose any service</option>
                @foreach ($search_options as $option)
                <option value="{{$option->name}}">{{$option->name}}</option>

                @endforeach
              </select>
              <input type="submit" class="form-control" value="Search" id="home_search_submit_btn">
            </form>
            <h1 data-aos="fade-right">Save your time by using NearByMe</h1>
            <p class="mb-5" data-aos="fade-right" data-aos-delay="100">
              NearByMe is a web application that helps you to find the nearest service providers from your location.
            </p>



            <p data-aos="fade-right" data-aos-delay="200" data-aos-offset="-500"><a href="#getstarted" class="btn btn-outline-white">Get started</a></p>
          </div>
          <div class="col-lg-4 iphone-wrap">
            <img src="{{url('frontend/assets/img/phone_img.png')}}" alt="Image" class="img-fluid phone_img" data-aos="fade-right">
            {{-- <img src="{{url('frontend/assets/img/phone_2.png')}}" alt="Image" class="phone-2" data-aos="fade-right" data-aos-delay="200"> --}}
          </div>
        </div>
      </div>
    </div>
  </div>

</section><!-- End Hero -->
@if (count($professions) > 0)
<!-- ======= Home Section ======= -->


<section class="section profession-section" id="services">
  <div class="container">
    <div class="row justify-content-center mb-2">
      <div class="col-md-12" data-aos="fade-up">
        <h2 class="section-heading">Available Services</h2>
        <hr>
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

@else
<h2 class="text-center text-danger">No service available at this moment</h2>
<hr>

@endif

<!-- ======= CTA Section ======= -->
<section class="section cta-section" id="getstarted">
  <div class="container">

    <div class="row align-items-center">
      <div class="col-md-6 me-auto text-center text-md-start mb-5 mb-md-0">
        <h2 class="get-started-text">Start Using our Platform</h2>
      </div>
      <div class="col-md-5 text-center text-md-end">
        <p>
          <a href="{{url('provider-panel/login')}}" class="btn get-started-btns btn-outline-info d-inline-flex align-items-center"><i class="bi bi-people-fill"></i><span>Service Provider</span></a>
          <a href="{{url('user-panel/login')}}" class="btn get-started-btns btn-outline-info d-inline-flex align-items-center"><i class="bi bi-people"></i><span>Service User</span></a>
        </p>
      </div>
    </div>
  </div>
</section><!-- End CTA Section -->


<!-- best provider  -->
@if (count($bestProviders) > 0)
<!-- ======= Home Section ======= -->


<section class="section profession-section" id="services">
  <div class="container">
    <div class="row justify-content-center mb-2">
      <div class="col-md-12" data-aos="fade-up">
        <h2 class="section-heading">Our Top Service Providers</h2>
        <hr>
      </div>
    </div>

    <div class="row">

      @foreach ($bestProviders as $provider)
      <div style="cursor: pointer" class="col-md-4 shadow mt-4 pt-3" data-aos="fade-up" data-aos-delay="">
        <div class="feature-1 text-center">
          <div class="wrap-icon icon-1">
            @if ($provider->provider->avatar != null)
            <img src="{{url('provider_avatar/'.$provider->provider->avatar)}}" alt="{{$provider->provider->name}}" style="border-radius: 50%;" class=" img-thumbnail" width="100px" height="100px">
            @else
            <img src="{{url('profession_avatar/default.jpg')}}" alt="{{$provider->provider->name}}" style="border-radius: 50%;" class=" img-thumbnail" width="100px" height="100px">

            @endif
          </div>
          <div>
            <?php
            $primary_stars = $provider->rating;
            $secondary_stars = 5 - $primary_stars;

            ?>

            @if ($primary_stars > 0)
            @for ($i = 0; $i < $primary_stars; $i++) <span class="bi bi-star-fill text-warning"></span>
              @endfor
              @endif
              @if ($secondary_stars > 0)
              @for ($i = 0; $i < $secondary_stars; $i++) <span class="bi bi-star-fill text-secondary"></span>
                @endfor
                @endif

          </div>
          <h3 class="mb-3"><a  href="">{{$provider->provider->name}}</a>
        <br >
        <span class="mt-4">({{$provider->provider->profession->name}})</span> 
        </h3>
        </div>
      </div>
      @endforeach
    </div>

  </div>
</section>

@else
<h2 class="text-center text-danger">No service available at this moment</h2>
<hr>

@endif
<!-- end best provider -->
@endsection