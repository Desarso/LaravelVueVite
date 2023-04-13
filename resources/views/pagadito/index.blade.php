
@extends('layouts/contentLayoutMaster')

@section('title', 'Pricing')

@section('page-style')
{{-- Page Css files --}}
<link rel="stylesheet" type="text/css" href="{{asset('css/pages/pricing.css')}}">
@endsection

@section('content')
<!-- pricing table -->
<div id="pricing-table">
  <!-- pricing table wrapper -->
  <div class="pricing-table-wrapper mt-2 pt-2">
    <div class="row">

      <div class="col-xl-9 col-lg-12 col-xxl-9 pl-xl-0">
        <!-- pricing plans starts here -->
        <div class="pricing-plans">
          <div class="row">
            <div class="col-md-12 col-lg-4 col-xl-4 pr-lg-0">
              <!-- basic plan card -->
              <div class="card left-card">
                <div class="card-content text-center">
                  <!-- basic plan starts here -->
                  <div class="basic-plan">
                    <div class="basic-plan-inner text-center pt-4 px-2 pb-2">
                      <!-- basic plan card top area -->
                      <div class="card-top-area-wrapper">
                        <div class="card-top-area">
                          <svg id="Layer_1" class="common-svg" style="enable-background:new 0 0 512 512;" version="1.1"
                            viewBox="0 0 512 512" xml:space="preserve" xmlns="http://www.w3.org/2000/svg"
                            xmlns:xlink="http://www.w3.org/1999/xlink">
                            <style type="text/css"></style>
                            <g>
                              <path class="st0"
                                d="M274.3,139.6v-6.3c13.4-1.3,22.4-8.9,22.4-20.5c0-3.5-0.6-6.5-2-9.1c-3.1-5.9-9.7-9.8-20.8-12.6V70.9   c3.6,0.8,7.1,2.4,10.8,4.6c1.1,0.6,2.1,1,3.2,1c3.3,0,5.9-2.5,5.9-5.8c0-2.5-1.5-4.1-3.2-5.1c-4.8-3-10.1-5.1-16.3-5.8v-2.3   c0-2.5-2-4.5-4.5-4.5c-2.5,0-4.6,2-4.6,4.5v2.2c-4.1,0.3-7.9,1.3-11,2.9c-3.9,1.9-7,4.8-8.9,8.3c-1.4,2.6-2.2,5.6-2.2,8.9   c0,4.8,1.2,8.6,3.6,11.7c3.5,4.6,9.8,7.6,18.9,9.9v20.7c-5.8-1.1-10.6-3.5-15.6-7c-1-0.7-2.2-1.1-3.5-1.1c-3.3,0-5.8,2.5-5.8,5.8   c0,2.2,1.1,3.9,2.8,5c6.4,4.5,13.7,7.4,21.6,8.2v6.5c0,2.5,2.1,4.5,4.6,4.5C272.3,144.1,274.3,142.1,274.3,139.6z M273.9,103.5   c0.2,0.1,0.3,0.1,0.5,0.2c7.7,2.5,9.9,5.3,9.9,9.8c0,5-3.7,8.4-10.4,9.2V103.5z M265.6,88.9c-1.6-0.5-3-1.1-4.2-1.6c0,0,0,0,0,0   c-4.6-2.2-5.9-4.6-5.9-8.2c0-4.6,3.4-8.2,10.1-8.8V88.9z" />
                              <path class="st0"
                                d="M225.2,209.3c-26.8-10.5-57.4-5.8-79.9,12.2c-2,1.6-2.9,4.1-2.6,6.5c4.4,28.5,23.7,52.6,50.5,63.1   c9.5,3.7,19.4,5.5,29.3,5.5c13.6,0,27.1-3.4,39.2-10.1v52.4l-53.9-9.9c-1.9-0.3-3.8,0.1-5.4,1.2L139,376.5v-11.3c0-3.9-3.1-7-7-7   H65.1c-3.9,0-7,3.1-7,7v124c0,3.9,3.1,7,7,7H132c3.9,0,7-3.1,7-7v-19.3l216.8,10.3c0.1,0,0.2,0,0.3,0c2.2,0,4.2-1,5.5-2.7   L448.9,365c3.2-4.2,5-9.2,5-14.5c0-8.5-4.6-16.4-12-20.6c-8.4-4.7-18.6-4-26.2,1.9c-0.9,0.7-1.8,1.5-2.7,2.4l-67.6,71.5l-62.9-17.1   l52.3-8.6c3-0.5,5.3-2.8,5.8-5.8l2.1-13.6c0.6-3.7-1.9-7.3-5.7-8l-61.5-11.3v-55c12.1,6.6,25.6,10.1,39.2,10.1   c9.9,0,19.9-1.8,29.3-5.5c26.8-10.5,46.1-34.6,50.5-63.1c0.4-2.5-0.6-5-2.6-6.5c-22.5-18-53.1-22.6-79.9-12.2   c-14.9,5.8-27.6,16-36.6,28.7v-57c42.4-3.6,75.8-39.2,75.8-82.4c0-12.1-2.5-23.8-7.6-34.6c-1.4-3-2.9-5.8-4.5-8.4   c-0.1-0.2-0.2-0.4-0.3-0.6c-11.1-17.8-28.3-30.7-48.5-36.2c-7.1-1.9-14.4-2.9-21.9-2.9c-28.8,0-55.1,14.6-70.3,39.1   c-0.1,0.2-0.2,0.4-0.3,0.6c-1.2,1.9-2.3,3.9-3.3,5.9C188.9,73,186,85.5,186,98.6c0,2.5,0.1,4.8,0.3,6.8   c2.1,26.1,16.8,49.9,39.2,63.7c11,6.8,23.4,10.8,36.3,11.9v56.9C252.7,225.2,240.1,215.1,225.2,209.3z M198.3,278   c-20.9-8.2-36.3-26.4-41-48.1c18.2-12.8,41.9-15.7,62.8-7.6s36.3,26.4,41,48.1C242.8,283.2,219.2,286.2,198.3,278z M125,482.2H72.1   v-110H125v18.1v72.3V482.2z M327.7,367.1L248.5,380c-3.3,0.5-5.7,3.3-5.9,6.5c-0.2,3.3,2,6.3,5.2,7.1l98.1,26.7   c2.5,0.7,5.1-0.1,6.9-1.9l70.4-74.5c0.3-0.4,0.7-0.7,1.1-1c3.1-2.4,7.3-2.7,10.7-0.8c3.1,1.7,4.9,4.9,4.9,8.4c0,2.1-0.7,4.2-2,5.9   L352.8,466L139,455.9v-62.1l69.3-50.5l119.7,22L327.7,367.1z M317.4,222.3c20.9-8.2,44.5-5.2,62.8,7.6c-4.7,21.8-20.1,40-41,48.1   c-20.9,8.2-44.5,5.2-62.8-7.6C281.1,248.7,296.5,230.4,317.4,222.3z M200.2,104.2c-0.2-1.7-0.2-3.5-0.2-5.6   c0-10.9,2.5-21.3,7.3-30.9c0.9-1.8,1.8-3.5,2.8-5.1c0.1-0.1,0.2-0.2,0.2-0.4c12.7-20.3,34.5-32.4,58.4-32.4   c6.2,0,12.3,0.8,18.2,2.4c16.7,4.6,31,15.2,40.2,30c0.1,0.1,0.1,0.2,0.2,0.3c1.4,2.2,2.7,4.6,3.9,7.2c4.2,9,6.3,18.7,6.3,28.8   c0,37.9-30.8,68.8-68.8,68.8c-12.7,0-25.2-3.5-35.9-10.1C214.2,145.7,202,126,200.2,104.2z" />
                            </g>
                          </svg>
                          <div class="font-medium-5 font-weight-bold mt-2 primary">
                            Basic
                          </div>
                          <div class="annual-plan">
                            <div class="plan-price mt-2">
                              <sup class="font-medium-5 font-weight-bold text-muted">$</sup>
                              <span class="font-large-1 font-weight-bold">79</span>
                              <sup class="text-muted font-medium-2 font-weight-bold">90</sup>
                              <span class="d-block font-weight-bold">per year</span>
                            </div>
                          </div>
                          <div class="monthly-plan hide">
                            <div class="plan-price mt-2">
                              <sup class="font-medium-5 font-weight-bold text-muted">$</sup>
                              <span class="font-large-1 font-weight-bold">15</span>
                              <sup class="text-muted font-medium-2 font-weight-bold">00</sup>
                              <span class="d-block font-weight-bold">per month</span>
                            </div>
                          </div>
                        </div>
                      </div>
                      <!-- basic plan top area ends -->

                      <!-- basic plan limit value starts-->
                      <div class="plan-limits mt-2 pt-25">
                        <div class="common-height-limit d-flex justify-content-center align-items-center">
                          <span class="font-weight-bold font-medium-4 text-truncate">100MB</span>
                        </div>
                        <span class="d-block d-xl-none font-weight-bold mb-2">Monthly Bandwidth</span>
                        <div
                          class="keyword-suggestion-val common-height-limit d-flex justify-content-center align-items-center">
                          <span class="font-weight-bold font-medium-4 text-truncate mr-25">100GB</span>
                          <sub class="text-muted font-weight-bold"> 0f 700GB</sub>
                        </div>
                        <span class="d-block d-xl-none font-weight-bold mb-2">Monthly storage</span>
                        <div class="common-height-limit d-flex justify-content-center align-items-center">
                          <span class="font-weight-bold font-medium-4 text-truncate">10 MySQL Database</span>
                        </div>
                        <span class="d-block d-xl-none font-weight-bold mb-2">MySQL Database</span>
                        <div class="common-height-limit d-flex justify-content-center align-items-center">
                          <span class="font-weight-bold font-medium-4 text-truncate">$10 Ads</span>
                        </div>
                        <span class="d-block d-xl-none font-weight-bold mb-2">AD Credit</span>
                        <div class="common-height-limit d-flex justify-content-center align-items-center">
                          <span class="font-weight-bold font-medium-4 text-truncate">100 Email Accounts</span>
                        </div>
                        <span class="d-block d-xl-none font-weight-bold mb-2">Email Accounts</span>
                        <div class="common-height-limit d-flex justify-content-center align-items-center">
                          <span class="font-weight-bold font-medium-4 text-truncate">False<i
                              class="danger feather icon-remove"></i></span>
                        </div>
                        <span class="d-block d-xl-none font-weight-bold mb-2">Root Access</span>
                        <div class="common-height-limit d-flex justify-content-center align-items-center">
                          <span class="font-weight-bold font-medium-4 text-truncate">---</span>
                        </div>
                        <span class="d-block d-xl-none font-weight-bold mb-2 text-truncate">Free Domain Names</span>
                        <form action="pagaditoPayment" method="post" >
                          @csrf
                          <input type="text" id="cost" name="cost"><br><br>
                          <div class="btn-subscribe-now">
                            <button type="submit" class="btn btn-outline-primary btn-md">Subscribe now</button>
                          </div>
                        </form>

                      </div>
                    </div>
                    <!-- basic plan limit value ends -->
                  </div>
                  <!-- basic plan ends here -->
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>


@endsection

@section('page-script')
{{-- Page js files --}}
<script src="{{asset('js/scripts/pages/pricing.js')}}"></script>
@endsection