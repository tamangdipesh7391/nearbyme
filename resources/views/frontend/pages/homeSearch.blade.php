@extends('frontend.main')
@section('content')
<style>
   .profile-img {
   display: block;
   overflow: hidden;
   }
   .profile-img img {
   position: relative;
   width: 90px;
   height: 90px;
   border-radius: 50%;
   border: 2px solid #c7c7c7;
   }
   .profile-img i {
   position: absolute;
   top: 20px;
   left: 84px;
   /* right:30px; */
   }
   
   .cardHover:hover{
    box-shadow: rgba(0, 0, 0, 0.1) 0px 1px 3px 0px, rgba(0, 0, 0, 0.06) 0px 1px 2px 0px;
    background-color: #f2f3ff;   }
</style>
   <!-- ======= Single Blog Section ======= -->
   <section class="hero-section inner-page">
    <div class="wave">

      <svg width="1920px" height="265px" viewBox="0 0 1920 245" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
        <g id="Page-1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
          <g id="Apple-TV" transform="translate(0.000000, -402.000000)" fill="#FFFFFF">
            <path d="M0,439.134243 C175.04074,464.89273 327.944386,477.771974 458.710937,477.771974 C654.860765,477.771974 870.645295,442.632362 1205.9828,410.192501 C1429.54114,388.565926 1667.54687,411.092417 1920,477.771974 L1920,667 L1017.15166,667 L0,667 L0,439.134243 Z" id="Path"></path>
          </g>
        </g>
      </svg>

    </div>

    <div class="container">
      <div class="row align-items-center">
        <div class="col-12">
          <div class="row d-flex justify-content-center">
            <div class="col-md-12 text-center hero-text">
              
              <h1 data-aos="fade-up" data-aos-delay="">Here is the result of your search</h1>
              {{-- <p class="mb-5" data-aos="fade-up" data-aos-delay="100">December 13, 2019 &bullet; By <a href="#" class="text-white">Admin</a></p> --}}
            </div>
          </div>
        </div>
      </div>
    </div>

  </section>
  <div class="container">
    <div class="row">
       <div class="col-sm-12">
          <div class="row mt-4">
             <div class="col-sm-3">
                <div style="position:sticky;top:0;top:70px" class="card vh-100">
                   <h4 class="card-header"> Filter Search</h4>
                   <div class="filter-search card-body">
                    <form action="{{route('home.search')}}" class="form-group " method="POST">
                      @csrf
                      <input required name="search" placeholder="Start searching here ..." type="search" id="home_search" class="form-control" >
                      <button type="submit" class=" form-control btn btn-success mt-2" >Search for Service Provider</button>
                    </form>
                  
                   </div>
                </div>
             </div>
             <div class="col-sm-9">
           
                
             
                <div class="card shadow-none">
                   <div class="card-header">
                      <h4>Available Service Providers for <span class="fw-bold">{{$professions[0]->profession_name??'Given Service'}} </span></h4>
                   </div>
                @if ($professions->count() > 0)
                   <div class="row card-body">
                     @foreach ($professions->sortBy('distance') as $key => $profession )
                       
                      <div class="col-sm-12 mb-4">
                         <div class="shadow-sm card cardHover ">
                            <div class="row p-2">
                               <div class="col-8">
                                  <div class="profile-img d-inline-flex">
                                     <div class="profile-avatar">
                                       @if ($profession->user_avatar != null)
                                       <img src="{{url('provider_avatar/'.$profession->user_avatar)}}" alt="{{$profession->user_name}}" style="border-radius: 50%;" class=" img-thumbnail" width="100px" height="100px">
                                          @if ($profession->is_active == 1)
                                          <i class="bi bi-circle-fill text-success rounded-circle"></i>
                                          @else
                                             <i class="bi bi-circle-fill text-danger rounded-circle"></i>
                                          @endif
                                       @else
                                          <img src="{{url('provider_avatar/default.jpg')}}" alt="{{$profession->user_name}}" style="border-radius: 50%;" class=" img-thumbnail" width="100px" height="100px">
                                          @if ($profession->is_active == 1)
                                          <i class="bi bi-circle-fill text-success rounded-circle"></i>
                                          @else
                                             <i class="bi bi-circle-fill text-danger rounded-circle"></i>
                                          @endif
                                       @endif
                                        
                                      
                                     </div>
                                     <div class="profilesummary px-4">
                                        <ul class="list-unstyled">
                                           <li class="h4" style="color:#100000">{{$profession->user_name}}</li>
                                           <li class="position-relative">
                                              <small style="position:absolute; margin-top:-0.5rem">
                                              <span class="bi bi-check-circle-fill text-success"> Verified Profile</span> 
                                              </small>
                                           </li>
                                           <br>
                                           @php
                                               $url = "#";
                                           @endphp
                                           @if ($profession->current_latitude != null && $profession->current_longitude != null)
                                            @php
                                               
                                            $url = "https://maps.google.com/?q=".$profession->current_latitude.",".$profession->current_longitude;
                                          @endphp  
                                           @endif
                                           <li style="font-weight:100"><span class="bi bi-map-fill text-primary"></span>  <a  @if ($profession->current_latitude != null && $profession->current_longitude != null) target="_blank" @endif href="{{$url}}" style="color:blue;"> <b><em>Locate Now</em></b></a><strong class="text-dark"> ({{$profession->distance.' KM'??''}}) </strong></li>
                                           <li><span class="bi bi-telephone-fill text-primary"> : <a href="tel:{{$profession->phone??''}}" class="text-decoration-none text-dark">{{$profession->phone??'N/A'}}</a></li>
                                        </ul>
                                     </div>
                                  </div>
                                  <div class="pt-3 text-center d-none">
                                     <br>
                                     *****
                                  </div>
                               </div>
                               <div class="col-4  align-items-center">
                                    @if (Session::has('session_user'))
                                    <form action="{{route('request.service')}}" method="POST">
                                       @csrf
                                       <input type="hidden" name="user_latitude" value="{{$profession->current_user_lattitude??''}}">
                                       <input type="hidden" name="user_longitude" value="{{$profession->current_user_longitude??''}}">
                                       <input type="hidden" name="user_id" value="{{Session::get('session_user')->id}}">
                                       <input type="hidden" name="provider_id" value="{{$profession->provider_id}}">
                                       <button type="submit" class="btn btn-primary rounded-pill btn-block">Request Provider</button>
                                    </form>
                                    @else
                                    <p  class="text-danger text-center"><i class="bi bi-exclamation-circle"></i> You must login to use this feature 
                                       <a class="text-primary text-center" href="{{url('user-panel/login')}}"> Login Now</a>
                                    </p>
                                       
                                    @endif
                                     
                                     
                                  
                               </div>
                            </div>
                         </div>
                      </div>
                      @endforeach
                      <!-- <p class="text-center">End of results</p> -->
                   </div>
                @else
            
                   
                   <div class="card-body">
                     <p class="text-danger">
                        Couldn't found any results for your search.
                     </p>
                                  
                </div>
            @endif
                </div>
               
               

               
          </div>
       </div>
    </div>
 </div>
@endsection