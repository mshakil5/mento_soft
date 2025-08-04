@extends('frontend.master')

@section('content')

    <section class="hero  " id="home">
        <video id="videoAdjust" class=" " autoplay="true" width="100%" loop="loop" muted="muted" playsinline="true">
            <source src="./videos/bg.mp4" type="video/mp4">
        </video>
        <div class="container-fluid px-0 h-100 ">
            <div class=" h-100 hero-content  ">
                <div class="  text-center mx-auto py-5">
                    <h2 class="text-uppercase title-font mb-4 wow zoomInDown">welcome to <span class="txt-ternary">mento
                            software</span>
                    </h2>
                    <h1 class="fw-bold display-3 wow fadeIn" style="color: #963434;">Transforming Business with
                        <br>innovative it
                        solutions
                    </h1>
                    <p class="w-75 fs-3 text-center mx-auto wow fadeIn">we are seasoned team of it solution experts,
                        dedicated to
                        turning challenges into opportunities &
                        crafting innovative solution for your digital success.</p>
                    <div class="my-4">
                        <h4 class="txt-bold title-font wow fadeIn">Our Services: <span
                                class="typed txt-ternary fw-bold "> </span>
                        </h4>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <section class="default choose-us position-relative" id="about">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center">
                    <h2 class="secTtile text-light text-uppercase fw-bold title-font wow bounce"> Why Choose Us ?</h2>
                </div>
            </div>
            <div class="row mt-5 text-center text-md-start">
                <div class='col-lg-6 text-white wow fadeIn'>
                    <p>
                        We design and build mobile-friendly websites that will get your business easily searchable
                        online and also increase both your enquiry and sales. Whether you’re a start-up company or
                        you’re searching for a re-design, we will help turn your traffic into real customers.
                    </p>
                    <p>
                        We can make your website so that it can be easily found online while at the same time making
                        sure it stays accessible across all platforms and devices. Each of our websites are tailored to
                        fit the business goals you have in mind, making sure they work for you and achieve the results
                        you’d like them to.
                    </p>
                    <p>
                        After all, a well-designed website is still useless if there’s no one to visit it, and we can
                        cause the traction you’re looking for.
                    </p>
                </div>
                <div class="col-lg-6">
                    <div class="row g-3" id="counter">
                        <div class="col-sm-12 col-md-6 col-lg-6">
                            <div class="bg-light box-gradient-counter txt-ternary p-3 rounded-3 d-flex align-items-center gap-3 box-model wow fadeInRight"
                                data-wow-delay=".5s">
                                <div>
                                    <span class="display-3 fw-bold count" data-number="10"></span>
                                    <span class="display-3 fw-bold ">X</span>
                                    <br> <span class="fs-5 text-dark">Take <br> Faster Delivery </span>
                                </div>
                                <iconify-icon icon="bitcoin-icons:rocket-outline" width="90" height="90"></iconify-icon>
                            </div>

                        </div>
                        <div class="col-sm-12 col-md-6 col-lg-6">

                            <div class="bg-light box-gradient-counter txt-ternary p-3 rounded-3 d-flex align-items-center gap-3 box-model wow fadeInRight"
                                data-wow-delay=".50s">
                                <div>
                                    <span class="display-3 fw-bold count" data-number="11"> </span>
                                    <span class="display-3 fw-bold"> + </span>
                                    <br> <span class="fs-5 text-dark"> Years of <br> Experience </span>
                                </div>
                                <iconify-icon icon="lets-icons:sun-light" width="90" height="90"></iconify-icon>
                            </div>

                        </div>
                        <div class="col-sm-12 col-md-6 col-lg-6">

                            <div class="bg-light box-gradient-counter txt-ternary p-3 rounded-3 d-flex align-items-center gap-3 box-model wow fadeInRight"
                                data-wow-delay=".25s">
                                <div>
                                    <span class="display-3 fw-bold count" data-number="30"></span>
                                    <span class="display-3 fw-bold ">+</span>
                                    <br> <span class="fs-5 text-dark"> Customers <br>Worldwide </span>
                                </div>
                                <iconify-icon icon="arcticons:world-geography-alt" width="90"
                                    height="90"></iconify-icon>
                            </div>

                        </div>
                        <div class="col-sm-12 col-md-6 col-lg-6">

                            <div class="bg-light box-gradient-counter txt-ternary p-3 rounded-3 d-flex align-items-center gap-3 box-model wow fadeInRight"
                                data-wow-delay=".35s">
                                <div>
                                    <span class="display-3 fw-bold count" data-number="99"> </span>
                                    <span class="display-3 fw-bold  "> % </span>
                                    <br> <span class="fs-5 text-dark">Our <br> Sucess Rate </span>
                                </div>
                                <iconify-icon icon="ph:certificate-thin" width="90" height="90"></iconify-icon>
                            </div>

                        </div>
                        <!--  
                        
                        <div class="col-lg-6">
                            <div class="bg-light txt-ternary p-3 rounded-3 ">
                                <span class="display-3"> 100%</span>
                                <br> <span class="fs-5 ">Sucess Rate</span>
                            </div>
                        </div> -->


                    </div>
                </div>
            </div>

        </div>
    </section>

    <section class="cta ">
        <div class="container">
            <div class="row  px-2  text-center  ">
                <h2 class="secTtile text-dark text-uppercase fw-bold title-font wow bounce mb-0 wow fadeInUp">our flexible development
                    approach</h2>
                <h5 class="text-center w-75 mx-auto mt-3">Lorem ipsum dolor sit, amet consectetur adipisicing elit.
                    Dignissimos blanditiis ea repudiandae voluptatibus vero temporibus? Quas numquam, molestias iusto
                    neque debitis ut cum ipsam ab?</h5>
            </div>
            <div class="row mt-5">
                <div class="inner text-center ">
                    <div class="items fadeInLeft wow">
                        <img src="./images/ico1.svg" alt="">
                        <h3 class="wow fadeInUp">Initiation</h3>
                    </div>
                    <div class="items fadeInLeft wow">
                        <img src="./images/ico2.svg" alt="">
                        <h3 class="wow fadeInUp">Discovery</h3>
                    </div>
                    <div class="items fadeInRight wow">
                        <img src="./images/ico3.svg" alt="">
                        <h3 class="wow fadeInUp">Development</h3>
                    </div>
                    <div class="items fadeInRight wow">
                        <img src="./images/ico4.svg" alt="">
                        <h3 class="wow fadeInUp">Support</h3>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="default what-we-do">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12 text-center mb-4">
                    <h2 class="secTtile text-light text-uppercase fw-bold title-font wow bounce">What we do?</h2>
                </div>
            </div>
            <div class="inner">
                <div class="circle fadeIn wow "> </div>
                <div class="ecommerce">
                    ecommerce <br>
                    solution
                </div>
                <div class="app">
                    Mobile <br>
                    Application
                </div>
                <div class="customSoftware">
                    Custom <br>
                    Software
                </div>
                <div class="graphics">
                    graphics <br>
                    Solution
                </div>
                <div class="seo">
                    SEO / <br>
                    Digital Marketing
                </div>
                <div class="web">
                    Web Design <br> and <br>
                    Development
                </div>
            </div>
        </div>
    </section>

    <section class="our-products  ">
        <div class="row  px-2  text-center py-5">
            <h2 class="secTtile text-dark text-uppercase fw-bold title-font wow bounce mb-0">our products</h2>
        </div>
        <div class="row g-0">
            <div class="col-lg-6">
                <div class="innerbox accounting">
                    <a href="product-details.html" class="productitle">
                        
                        Accounts <br> management
                    
                    </a>
                    <div class="one fadeInUp wow">cash flow</div>
                    <div class="two">Ledger</div>
                    <div class="three bg-dark text-light">Receivable</div>
                    <div class="four">Payable</div>
                    <div class="five bg-dark text-light">Banking</div>
                    <div class="six">Inventory </div>
                    <div class="seven bg-dark text-light">Payroll </div>
                    <div class="eight">Tax </div>
                </div>

            </div>
            <div class="col-lg-6">
                <div class="innerbox ai">
                    <a href="product-details.html" class="productitle ">
                        Artificial <br> Intelligence
                    </a>
                    <div class="one bg-dark text-light">ChatGPT</div>
                    <div class="two bg-dark text-light">robotics</div>
                    <div class="three">Data Science</div>
                    <div class="four bg-dark text-light">retouch</div>
                    <div class="five">Calculation</div>
                    <div class="six bg-dark text-light">Analysis </div>
                    <div class="seven">Algotithm </div>
                    <div class="eight">AI </div>
                </div>

            </div>
            <div class="col-lg-6">
                <div class="innerbox accounting">
                    <a href="product-details.html" class="productitle">
                        
                        Accounts <br> management
                    
                    </a>
                    <div class="one fadeInUp wow">cash flow</div>
                    <div class="two">Ledger</div>
                    <div class="three bg-dark text-light">Receivable</div>
                    <div class="four">Payable</div>
                    <div class="five bg-dark text-light">Banking</div>
                    <div class="six">Inventory </div>
                    <div class="seven bg-dark text-light">Payroll </div>
                    <div class="eight">Tax </div>
                </div>

            </div>
            <div class="col-lg-6">
                <div class="innerbox ai">
                    <a href="product-details.html" class="productitle ">
                        Artificial <br> Intelligence
                    </a>
                    <div class="one bg-dark text-light">ChatGPT</div>
                    <div class="two bg-dark text-light">robotics</div>
                    <div class="three">Data Science</div>
                    <div class="four bg-dark text-light">retouch</div>
                    <div class="five">Calculation</div>
                    <div class="six bg-dark text-light">Analysis </div>
                    <div class="seven">Algotithm </div>
                    <div class="eight">AI </div>
                </div>

            </div>
            <div class="col-lg-6">
                <div class="innerbox accounting">
                    <a href="product-details.html" class="productitle">
                        
                        Accounts <br> management
                    
                    </a>
                    <div class="one fadeInUp wow">cash flow</div>
                    <div class="two">Ledger</div>
                    <div class="three bg-dark text-light">Receivable</div>
                    <div class="four">Payable</div>
                    <div class="five bg-dark text-light">Banking</div>
                    <div class="six">Inventory </div>
                    <div class="seven bg-dark text-light">Payroll </div>
                    <div class="eight">Tax </div>
                </div>

            </div>
            <div class="col-lg-6">
                <div class="innerbox ai">
                    <a href="product-details.html" class="productitle ">
                        Artificial <br> Intelligence
                    </a>
                    <div class="one bg-dark text-light">ChatGPT</div>
                    <div class="two bg-dark text-light">robotics</div>
                    <div class="three">Data Science</div>
                    <div class="four bg-dark text-light">retouch</div>
                    <div class="five">Calculation</div>
                    <div class="six bg-dark text-light">Analysis </div>
                    <div class="seven">Algotithm </div>
                    <div class="eight">AI </div>
                </div>

            </div>
        </div>
    </section>

    <section class="default testimonial-section wow fadeIn" id="testimonial">
        <div class="container">
            <div class="row  px-2">
                <div class="col-lg-12 text-center">
                    <h2 class="secTtile text-dark text-uppercase fw-bold title-font wow bounce">Happy Clients Says</h2>
                </div>
            </div>
            <div class="row mt-3">
                <div class="testimonial">
                    <div class="p-3 fadeInUp wow">
                        <div class="blogBox">
                            <div class="content text-center text-light">
                                <img src="./images/testimonial/2.jpg" class="rounded-circle mx-auto mb-3" width="90px">
                                <p class="m-0">
                                <h5>Arbaz Arshad - </h5>
                                <iconify-icon icon="bxs:quote-left" width="25" height="25"
                                    style="color: #e11919"></iconify-icon>
                                A group of young passionate and professional developers with big ambitions which
                                guarantees a high quality work to be delivered. It has been a pleasure working with
                                these guys. I was very pleased with the amount of care.
                                <iconify-icon icon="bxs:quote-right" width="25" height="25"
                                    style="color: #e11919"></iconify-icon>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="p-3 fadeInUp wow">
                        <div class="blogBox">
                            <div class="content text-center text-light">
                                <img src="./images/testimonial/3.jpg" class="rounded-circle mx-auto mb-3" width="90px">
                                <p class="m-0">
                                <h5>Monila Makren - </h5>
                                <iconify-icon icon="bxs:quote-left" width="25" height="25"
                                    style="color: #e11919"></iconify-icon>
                                They want me to see a successful businessman, rather than acting purely as an outsourced
                                developer doing daily task. They completed my Django project the way I wanted also
                                improved the way my end users expected.
                                <iconify-icon icon="bxs:quote-right" width="25" height="25"
                                    style="color: #e11919"></iconify-icon>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="p-3 fadeInUp wow">
                        <div class="blogBox">
                            <div class="content text-center text-light">
                                <img src="./images/testimonial/1.jpg" class="rounded-circle mx-auto mb-3" width="90px">
                                <p class="m-0">
                                <h5>John Landish - </h5>
                                <iconify-icon icon="bxs:quote-left" width="25" height="25"
                                    style="color: #e11919"></iconify-icon>
                                We will work on next projects together as well. Time-management, communication and the
                                level of execution was professional. They just went through it so much faster than I
                                expected. The way they improvise is mind blowing.
                                <iconify-icon icon="bxs:quote-right" width="25" height="25"
                                    style="color: #e11919"></iconify-icon>
                                </p>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>

    <section class="default contact-section wow fadeIn " id="contact">
        <div class="container">
            <div class="row  px-2">
                <div class="col-lg-12 text-center">
                    <h2 class=" text-light text-uppercase fw-bold title-font wow bounce mb-0 secTtile">
                        Get in touch
                    </h2>
                </div>
            </div>
            <div class="row mt-5">
                <div class="col-lg-6 d-flex align-items-center text-light">
                    <div class="text-center text-md-start mb-4">
                        <h1 class="display-4 text-capitalize fw-bold fadeInLeft  wow">ready to build better software faster ?</h1>
                        <h6 class="lh-3">Lorem ipsum dolor,amet consectetur adipisicing sitamet consectetur
                            adipisicing<br> elit. Illum, laboriosam.</h6>
                        <div class="my-3 d-flex gap-3 flex-wrap align-items-center w-100 text-center fs-6">
                            <span class="d-flex gap-2  align-items-center fadeInUp wow  "><iconify-icon icon="iconamoon:email-thin"
                                    width="25" height="25" style="color: #ff961d"></iconify-icon>
                                info@mentosoftware.co.uk</span>
                            <span class="d-flex gap-2  align-items-center fadeInUp wow  "><iconify-icon icon="lets-icons:phone-light"
                                    width="25" height="25" style="color: #ff961d"></iconify-icon> 07745975978</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 ">
                    <form action="mailer.php" method="post" class="form-style fadeInUp">
                        <div class="row ">
                            <h3 class="text-white">Speak to an expert</h3>
                            <div class="col-md-6 form-group">
                                <input type="text" placeholder="First Name *" class="form-control" name="fname"
                                    required="required">
                            </div>
                            <div class="col-md-6 form-group">
                                <input type="text" placeholder="Last Name *" class="form-control" name="lname"
                                    required="required">
                            </div>
                            <div class="col-md-6 form-group">
                                <input type="email" placeholder="E-mail *" class="form-control" name="email"
                                    required="required">
                            </div>
                            <div class="col-md-6 form-group">
                                <input type="number" min="1" placeholder="Phone *" class="form-control" name="phone"
                                    required="required">
                            </div>

                            <div class="col-12 form-group">
                                <textarea placeholder="Message*" class="textarea form-control" name="message"
                                    id="form-message" rows="3" cols="5" required="required"></textarea>
                            </div>
                            <div class="col-12 form-group margin-b-none">
                                <button type="submit" name="submit"
                                    class="btn-theme mt-4 border-0 d-block rounded-3 w-100  fs-5 text-uppercase text-black">sent
                                    Message</button>

                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </section>

@endsection

@section('script')

@endsection