<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Raleway:900">
<style type="text/css">


    #imageLink:hover {
        position: relative;
    }

    #imageLink[data]:hover:after {
        content: attr(data);
        padding: 4px 8px;
        color: white;
        position: absolute;
        left: 75px;
        top: 40px;
        white-space: nowrap;
        z-index: 2;
        border-radius: 20px;
        background: rgba(0, 0, 0, 1);
        border: 1px solid white;
        font-family: 'Raleway';
        font-size: 14px;
    }

    table td,
    table th {
        padding: 10px;
        text-align: center;
        font-size: 18px;
    }

    .quote-card {
        background: #fff;
        color: #222222;
        padding: 20px;
        padding-left: 50px;
        box-sizing: border-box;
        box-shadow: 0 2px 4px rgba(34, 34, 34, 0.12);
        position: relative;
        overflow: hidden;
        min-height: 120px;
    }

    .quote-card p {
        font-size: 22px;
        line-height: 1.5;
        margin: 0;
        max-width: 80%;
    }

    .quote-card cite {
        font-size: 16px;
        margin-top: 10px;
        display: block;
        font-weight: 200;
        opacity: 0.8;
    }

    .quote-card:before {
        font-family: Georgia, serif;
        content: "“";
        position: absolute;
        top: 10px;
        left: 10px;
        font-size: 5em;
        color: rgba(238, 238, 238, 0.8);
        font-weight: normal;
    }

    .quote-card:after {
        font-family: Georgia, serif;
        content: "”";
        position: absolute;
        bottom: -110px;
        line-height: 100px;
        right: -32px;
        font-size: 25em;
        color: rgba(238, 238, 238, 0.8);
        font-weight: normal;
    }

    @media (max-width: 640px) {
        .quote-card:after {
            font-size: 22em;
            right: -25px;
        }

</style>


<!-- Start slider section -->
<div class="slider" id="home">
    <h1 class="main-h1">Welcome to <span class="colored">Optimal Web Assistant</span></h1>
    <p style="font-size: 18px;"><span class="">Successful and unsuccessful people do not vary greatly in their abilities. They vary in their desires to reach their potential. – John Maxwell.</span><br>We are very happy to have you here and we wish you can spend a lot of time enjoyig our services to help you grow your business</p>
    <a href="<?= base_url('') ?>" target="_blank" class="anchor">Subscribe Now!</a>
</div>
<!-- end of slider section -->

<!-- Start About Section -->
<section class="about-us text-center" id="features">
    <div class="container">
        <div class="sub-info">
            <span class="icon icon-basic-sheet-pen"></span>
            <h2>What is<span class="colored">Optimal Web Assistant?</span></h2>
            <p class="centering">Optimal Web Assistant is an AI web assistant that help manage, organize and provide support for a website.</p>
            <p class="centering colored" style="font-size: 20px;"><b>Available now! Get your own web assistant.</b> <b style="color: #f89a1d"> <a href="<?= base_url('') ?>">Subscribe now</a></b></p>
        </div>
        <div class="row">
            <div class="col-lg-3 col-md-6">
                <div class="about-box">
                    <span class="icon icon-basic-lightbulb"></span>
                    <h6><b>Chatbot</b></h6>
                    <p>It contains a chatbot to help you deal with customers.</p>
                    <b class="colored">Coming Soon</b>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="about-box">
                    <span class="icon icon-basic-pencil-ruler-pen"></span>
                    <h6><b>Enhance User Experience</b></h6>
                    <p>Assistant can provide you with intelligent solutions to enhance user experience.</p>
                    <b class="colored">Coming Soon</b>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="about-box">
                    <span class="icon icon-basic-question"></span>
                    <h6><b>Ask Questions</b></h6>
                    <p>User can ask questions any time and get the reply.
                    <b class="colored">Coming Soon</b>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="about-box">
                    <span class="icon icon-basic-archive-full"></span>
                    <h6><b>Reach your product</b></h6>
                    <p>It's now easy for the user to get his product usign bot help.</p>
                    <b class="colored">Coming Soon</b>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- End About Section -->

<!-- Start departments -->
<section class="departments text-center" id="departments">
    <div class="container">
        <div class="sub-info">
            <span class="icon icon-basic-laptop"></span>
            <h2>Optimal <span class="colored">Solutions</span></h2>
            <p class="centering">We design and develop Enterprise Solutions like ERP, HRM and CRM systems in addition to custom applications for enterprises of different industry domains.
            </p>
        </div>
        <div class="row">
            <div class="col-xl-6 col-lg-12">
                <div class="row">
                    <div class="col-md-6">
                        <div class="department-box">
                            <span class="icon icon-basic-gear"></span>
                            <h4>Operational Efficiency</h4>
                            <p>Ensure smooth execution of every solution we develop to benefit from the full potential of our solutions.</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="department-box">
                            <span class="icon icon-basic-joypad"></span>
                            <h4>End-to-End Solutions</h4>
                            <p>Starting from concept & strategy to system design & implementation.</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="department-box">
                            <span class="icon icon-basic-pencil-ruler-pen"></span>
                            <h4>24/7 Support</h4>
                            <p>Our work doesn’t stop once your site goes live. We provide the updates and enhancements you need to keep growing.
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="department-box">
                            <span class="icon icon-basic-video"></span>
                            <h4>90 Days Guarantee </h4>
                            <p>We guarantee the work of our solutions till 90 days in which you can change the functions you didn’t like.</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-6 col-lg-12">
                <div class="img">
                    <img src="<?=base_url()?>styles/images/cover2.jpg" alt="geeks image">
                </div>
            </div>
        </div>
    </div>
</section>
<!-- End departments -->

<!-- start want work section-->
<section class="volunteer text-center">
    <div class="container">
        <div class="info">
            <span class="icon icon-basic-mixer2"></span>
            <h2>Want to join our team ?</h2>
            <p class="centering">If our hopes of building a better and a safer world are to become more than wishful thinking, we will need the engagement of you more than ever.</p>
            <a href="#" target="_blank" class="anchor">Join Our Team Now!</a>
        </div>
    </div>
</section>
<!-- End want work section-->

<!-- start scroll top -->
<div class="scroll-top" style="z-index: 1000;cursor:pointer">
    <i class="fas fa-chevron-up"></i>
</div>
<!-- end scroll top -->



<script>
    var string = `Da7i7a is one of Geeks projects which target the Academic section in Geeks student Activity.
Geeks is a multi-branched student activity Connecting different Communities`;
    var array = string.split("");
    var timer;

    function framelooper() {
        if (array.length > 0) {
            document.getElementById("loogo").innerHTML += array.shift();
        } else {
            clearTimeout(timer);
            return;
        }

        looptimer = setTimeout('framelooper()', 40);
    }

    framelooper();

    function welcome() {
        alert("you are welcomed");
    }

</script>
