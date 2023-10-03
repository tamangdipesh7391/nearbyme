@section('footer')

</main><!-- End #main -->

<!-- ======= Footer ======= -->
<footer class="footer" role="contentinfo" id="contact">
  <div class="container">
    <div class="row">
      <div class="col-md-4 mb-4 mb-md-0">
        <h3 class="text-primary footer-heading">About NearByMe</h3>
        <p class="text-dark">NearByMe is a platform where anyone sale and buy skill and services. It can help to find nearest service provider from users location which not only save the time also make serve affordable.</p>
        <p class="social">
          <a href="#"><span class="bi bi-twitter"></span></a>
          <a href="#"><span class="bi bi-facebook"></span></a>
          <a href="#"><span class="bi bi-instagram"></span></a>
          <a href="#"><span class="bi bi-linkedin"></span></a>
        </p>
      </div>
      <div class="col-md-8 ms-auto">
        <div class="row site-section pt-0">
          <div class="col-md-8 mb-4 mb-md-0">
            <h3 class="text-primary footer-heading">Find Us</h3>
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d56516.31625951286!2d85.29111323130881!3d27.708955944406398!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x39eb198a307baabf%3A0xb5137c1bf18db1ea!2sKathmandu%2044600!5e0!3m2!1sen!2snp!4v1656315415921!5m2!1sen!2snp" width="auto" height="200" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>

          </div>
          <div class="col-md-4 mb-4 mb-md-0">
            <h3 class="text-primary footer-heading">Contact Us</h3>
            <ul class="list-unstyled footer-link">
              <li>
                <a href="#">
                  <span class="d-inline-block mr-2">
                    <i class="bi bi-map-fill text-primary"></i>
                  </span>
                  Kathmandu, Nepal
                </a>
              </li>
              <li>
                <a href="#">
                  <span class="d-inline-block mr-2">
                    <i class="bi bi-phone-fill text-primary"></i>
                  </span>
                  +977-984-064-8984
                </a>
              </li>
              <li>
                <a href="#">
                  <span class="d-inline-block mr-2">
                    <i class="bi bi-envelope-fill text-primary"></i>
                  </span>
                  nearbyme@gmail.com
                </a>
              </li>
              
            </ul>

          </div>
        </div>
      </div>
    </div>



  </div>
</footer><hr>
<!-- End Footer -->
<div class="container mt-2">
  <div class="row">
    <div class="col-md-12 text-center text-dark">
      <p>
        &copy; {{ date('Y') }} NearByMe. All rights reserved. Designed by <a href="https://facebook.com/tamangdipesh7391" target="_blank">Dipesh Tamang</a>
      </p>

  </div>
</div>
<hr>

<a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

<!-- Vendor JS Files -->
<script src="{{url('frontend/assets/vendor/aos/aos.js')}}"></script>
<script src="{{url('frontend/assets/vendor/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
<script src="{{url('frontend/assets/vendor/swiper/swiper-bundle.min.js')}}"></script>
<script src="{{url('frontend/assets/vendor/php-email-form/validate.js')}}"></script>

<!-- Template Main JS File -->
<script src="{{url('frontend/assets/js/main.js')}}"></script>

</body>

</html>
@endsection