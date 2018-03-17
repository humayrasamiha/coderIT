<!DOCTYPE html>

<html lang="en">

<head>

   <meta charset="utf-8">

   <meta http-equiv="X-UA-Compatible" content="IE=edge">

   <meta name="viewport" content="width=device-width, initial-scale=1"> 

   <title>Home Page Two</title>

   <link rel="stylesheet" href="<?php bloginfo('template_directory');?>/css/bootstrap.min.css">

   <link rel="stylesheet" href="<?php bloginfo('template_directory');?>/css/font-awesome.min.css">

   <link rel="stylesheet" href="<?php bloginfo('template_directory');?>/css/animate.css">

   <link rel="stylesheet" href="<?php bloginfo('template_directory');?>/css/owl.carousel.css">

   <link rel="stylesheet" href="<?php bloginfo('template_directory');?>/css/owl.theme.css">

   <link rel="stylesheet" href="<?php bloginfo('template_directory');?>/css/style.css">

   <link rel="stylesheet" href="<?php bloginfo('template_directory');?>/css/responsive.css">

   <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
       <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
       <!--[if lt IE 9]>
         <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
         <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>

<body>

    <!-- .........header start ......... -->

    <section class="container-fluid" id="home">

        <div class="navbar navbar-fixed-top nav-1" role="navigation" id="nav-1">

                <div class="container">

                    <div class="navbar-header nav-1-header">

                      <button type="button" class="navbar-toggle navbar-button" data-toggle="collapse" data-target=".navbar-collapse">

                        <span class="icon-bar nav-1-menubar"></span>

                        <span class="icon-bar nav-1-menubar"></span>

                        <span class="icon-bar nav-1-menubar"></span>

                      </button>

                      <a class="navbar-brand nav-1-brand wow fadeInDown" href="#"><img src="<?php bloginfo('template_directory');?>/img/logo2.png" class="img-responsive" /></a>

                    </div>

                    <div class="navbar-collapse collapse menu">

                        <?php

                           wp_nav_menu(array(

                          'theam_location'=>'Primary Menu',

                          'menu'=>'coders',

                          'menu_class'=>'nav navbar-nav navbar-right wow fadeInDown'




                        ));


                    ?>


                    </div>

                </div>    
        </div>

    </section>

    <header id="header" >

        <div class="row fix ">

            <p class=" wow fadeInUp"><?php echo get_option_tree('home_sub_title'); ?></p>

            <h1 class=" wow fadeInUp"><?php echo get_option_tree('home_title'); ?></h1>

            <a href="<?php echo get_option_tree('slider_link'); ?>"><button class="butt wow fadeInUp"><?php echo get_option_tree('slider_button'); ?></button></a>

        </div>

    </header>

    <!-- .........header end ......... -->



    <!-- ............Services start...........-->
    <!-- ............Services start...........-->
    <section id="services" class="sectionc-service">

            <h1 class="title wow fadeInLeft"><?php echo get_option_tree('services_sub_title'); ?></h1>

            <p class="sub-title wow fadeInRight"><?php echo get_option_tree('services_title'); ?></p>

            <div class="service">

                    <div  id="owl-demol" class="owl-carousel owl-theme">

                        <?php

                           $args=array(

                            'post_type'=>'services',

                            'posts_per_page'=>6

                            );

                           $services=new WP_Query($args);

                           while($services->have_posts()):$services->the_post();

                        ?>

                        <div class="item wow fadeInUp">
                            
                                <a href=""><?php the_post_thumbnail();?></a>

                                <h3><?php the_title();?></h3>

                                <p><?php the_content();?></p>

                                <span>read more</span>

                               <!--  <span><?php read_more(19);?></span> -->

                        </div>

                        <?php endwhile;?>

                    </div>

                

            </div>

    </section>
    
<!-- ............Services start...........-->
    <hr>
<!-- ............about start...........-->

    <section id="about" class="about-service">

        <div class="about">

            <div class="row fix">

                <?php

                           $args=array(

                            'post_type'=>'about'

                            );

                           $about=new WP_Query($args);

                           while($about->have_posts()):$about->the_post();

                        ?>

                <div class="col-md-6 fix">

                    <?php the_post_thumbnail();?>

                </div>


                <div class="col-md-6">

                    <div class="about-textbox">

                        <h2 class="wow fadeInUp"><?php the_title();?></h2>

                        <b class="wow fadeInUp"><?php the_excerpt();?></b>

                        <p class="wow fadeInUp"><?php the_content();?></p>

                        <a href="<?php echo get_option_tree('slider2_link'); ?>"><button class="butt wow fadeInUp"><?php echo get_option_tree('slider2_button'); ?></button></a>

                    </div>

                </div>

                <?php endwhile;?>

            </div>
            
        </div>

    </section>

    <hr>
<!-- ............about start...........-->
  
<!-- ............about start...........-->

 <div id="team">

        <div class="sec_gap"></div>

        <div class="fix_container team_dtls">

            <div class="title_1">

                <h2 class="title wow fadeInLeft"><?php echo get_option_tree('team_sub_title'); ?></h2>

                <h2 class="sub-title wow fadeInRight"><?php echo get_option_tree('team_title'); ?></h2>

            </div>

            <div id="owl-demo2" class="team_list">

                <?php

                           $args=array(

                            'post_type'=>'team',

                            'posts_per_page'=>12

                            );

                           $team=new WP_Query($args);

                           while($team->have_posts()):$team->the_post();

                        ?>
                      
                <div class="item single-member ">

                    <?php the_post_thumbnail();?>

                    <p class="p_text p_txt1"><?php the_title();?></p>

                    <p class="p_text p_txt2"><?php the_content();?></p>

                </div>

                <?php endwhile;?>
                      
                <div class="owl-controls">

                    <div class="owl-pagination" id="ph">

                        <div class="owl-page active"><img src="<?php bloginfo('template_directory');?>/images/team_1.jpg"></div>

                        <div class="owl-page"><img src="<?php bloginfo('template_directory');?>/images/team_2.jpg"></div>

                        <div class="owl-page"><img src="<?php bloginfo('template_directory');?>/images/team_3.jpg"></div>

                        <div class="owl-page"><img src="<?php bloginfo('template_directory');?>/images/team_1.jpg"></div>

                        <div class="owl-page"><img src="<?php bloginfo('template_directory');?>/images/team_2.jpg"></div>

                        <div class="owl-page"><img src="<?php bloginfo('template_directory');?>/images/team_3.jpg"></div>

                    </div>

                </div>

                

            </div>

        </div>

    </div>
    <!-- ========== team end ========== -->

    <hr>
<!-- ............about start...........-->
<!-- ............about start...........-->

    <section id="portfolio" class="portfolio-service">

        <div class="portfolio">

             <h1 class="title wow fadeInLeft"><?php echo get_option_tree('portfolio_sub_title'); ?></h1>

            <p class="sub-title wow fadeInRight"><?php echo get_option_tree('portfolio_title'); ?></p>

            <div class="row fix">

               <div class="col-md-4 vv">

                <?php

                           $args=array(

                            'post_type'=>'portfolio_part1'

                            

                            );

                           $portfolio_part1=new WP_Query($args);

                           while($portfolio_part1->have_posts()):$portfolio_part1->the_post();

                        ?>

                    <div class="imag-cover  wow fadeInDown">

                        <a href=""><?php the_post_thumbnail();?></a> 

                   </div>

                  <?php endwhile;?>

               </div>



               <div class="col-md-8">

                    <div class="row fix ">

                        <?php

                           $args=array(

                            'post_type'=>'portfolio_part2',

                            'posts_per_page'=>6

                            );

                           $portfolio_part2=new WP_Query($args);

                           while($portfolio_part2->have_posts()):$portfolio_part2->the_post();

                        ?>

                        <div class="col-md-4">

                            <div class="imag-cover  wow fadeInUp">

                                <a href=""><?php the_post_thumbnail();?></a>

                            </div>

                        </div>

                        <?php endwhile;?>

                </div>


               </div>

            </div>

            <a href=""><button>read more<i class="fa fa-arrow-right"></i></button></a>

            <!-- <a href=""><button><?php read_more(19);?><i class="fa fa-arrow-right"></i></button></a> -->

        </div>

    </section>

    <hr>
<!-- ............about start...........-->





<!-- ............about start...........-->

    <section id="test" class="test-service">

        <div class="test">

            <h1 class="title  wow fadeInLeft"><?php echo get_option_tree('testimonial_sub_title'); ?></h1>

            <p class="sub-title  wow fadeInRight"><?php echo get_option_tree('testimonial_title'); ?></p>

            <div class="test-img">

                 <div id="owl-demo5" class="owl-carousel owl-theme">

                    <?php

                           $args=array(

                            'post_type'=>'testimonial',

                            'posts_per_page'=>3

                            );

                           $testimonial=new WP_Query($args);

                           while($testimonial->have_posts()):$testimonial->the_post();

                        ?>

                    <div class="item">

                       <?php the_post_thumbnail();?>

                        <div class="test-text  wow fadeInUp">

                            <p><?php the_content();?></p>

                            <span><?php the_title();?></span>

                            <b><?php the_excerpt();?></b>

                        </div>

                    </div>

                    <?php endwhile;?>

                </div>

            </div>

        </div>

    </section>
<!-- ............about start...........-->


<!-- ............about start...........-->

<section class="subscribe-section"  id="input-box">

            <div class="contact_dtls">

                <div class="sec_gap"></div>

                <h2 class="title wow fadeInDown"><?php echo get_option_tree('contract_sub_title'); ?></h2>

                <p class="sub-title wow fadeInUp"><?php echo get_option_tree('contract_title'); ?></p>

                   <?php if ( ! dynamic_sidebar('footer_three') ) : ?>

                

                   <?php endif; ?> 

                <div class="sec_gap"></div>

            </div>

        </section>
<!-- ............about start...........-->

<!-- ............about start...........-->

<footer>

    <div class="footer-section">

        <div class="row fix">

            <div class="col-md-3">

                <?php

                           $args=array(

                            'post_type'=>'contract_logo'

                            

                            );

                           $contract_logo=new WP_Query($args);

                           while($contract_logo->have_posts()):$contract_logo->the_post();

                        ?>

                <div class="fot-imag">

                    <a href=""><?php the_post_thumbnail();?></a>

                </div>

                <?php endwhile;?>

            </div>

            <div class="col-md-3">

                <?php if ( ! dynamic_sidebar('footer_one') ) : ?>

                <?php endif; ?>

            </div>

            <div class="col-md-3">

              <?php if ( ! dynamic_sidebar('footer_two') ) : ?>

              <?php endif; ?>


                

            </div>

            <div class="col-md-3">

                <ul >

                    <li class="pull-right wow fadeInUp">

                        <h4>Follow Us</h4>

                        <a href="<?php echo get_option_tree('twitter_link'); ?>"><i class="fa fa-<?php echo get_option_tree('twitter_image'); ?>" aria-hidden="true"></i></a>
                        <a href="<?php echo get_option_tree('facebook_link'); ?>"><i class="fa fa-<?php echo get_option_tree('facebook_image'); ?>" aria-hidden="true"></i></a>
                        <a href="<?php echo get_option_tree('instagram_link'); ?>"><i class="fa fa-<?php echo get_option_tree('instagram_image'); ?>" aria-hidden="true"></i></a>
                        <a href="<?php echo get_option_tree('linkedin_link'); ?>"><i class="fa fa-<?php echo get_option_tree('linkedin_image'); ?>" aria-hidden="true"></i></a>

                    </li>

                    <li ></li>

                </ul>

            </div>

        </div>

        <hr>

        <div class="row fix">

            <p class="copyright">Copyright@CodersIT 2016, All Rights Reserved</p>

        </div>

    </div>

</footer>
<!-- ............about start...........-->

    <script src="<?php bloginfo('template_directory');?>/js/jquery.min.js"></script>

    <script src="<?php bloginfo('template_directory');?>/js/bootstrap.min.js"></script>

    <script src="<?php bloginfo('template_directory');?>/js/wow.min.js"></script>

    <script src="<?php bloginfo('template_directory');?>/js/owl.carousel.min.js"></script>

    <script src="<?php bloginfo('template_directory');?>/js/custom.js"></script>

    



</body>

</html>