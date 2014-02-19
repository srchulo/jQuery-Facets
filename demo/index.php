<?php
	$db = null;

	function get_db() { 
		if(!$db)
			$db = new PDO('sqlite:cars.db');

		return $db;
	}

	function get_cars () { 
		$file_db = get_db();
		$id_where = "";
		$where = "";
		$vals = array();
		$id_vals = array();

		if($_GET['search'] != "")  {
		 	$words = explode(" ", $_GET['search']);
			
			$id_where = " WHERE ";
			#VERY rudimentary BAD search function for search queries. Also, very inefficient when scaled.
			#just for purposes of demo
			foreach($words as $word) { 
				$word = "%$word%";
				$id_where .= "make LIKE ? OR model LIKE ? OR year LIKE ? OR condition LIKE ? OR color LIKE ? OR ";
				array_push($id_vals, $word, $word, $word, $word, $word);
			}

			$id_where = substr($id_where, 0, -3);
		}

		#inefficient to select all matches and then filter down. purely for demo and to
		#have faceted query on its own for demonstration. Look into elasticsearch or solr for efficient faceting!
		$stmt = $file_db->prepare("SELECT id FROM cars $id_where");
		$stmt->execute($id_vals);

		$ids_list = "";
		while($car = $stmt->fetch()) { 
			$ids_list .= $car['id'] . ",";	
		}
		$ids_list = substr($ids_list, 0, -1);

		#return if we got no results from search!
		if($ids_list == "") { 
			return $stmt;
		}

		unset($_GET['search']);

		#if we have facets
		if(count($_GET) > 0) { 

			#handle specialCases
			if($_GET['onlyPhotos'] == "1") { 
				$where .= " AND image!='' ";	
				unset($_GET['onlyPhotos']);
			}

			if($_GET[minPrice] != "") { 
				$where .= " AND price >= $_GET[minPrice] ";
			}

			if($_GET[maxPrice] != "") { 
				$where .= " AND price <= $_GET[maxPrice] ";
			}

			unset($_GET[minPrice]);
			unset($_GET[maxPrice]);

			if($_GET[minYear] != "") {
				$where .= " AND year >= $_GET[minYear] ";
			}


			if($_GET[maxYear] != "") {
				$where .= " AND year <= $_GET[maxYear] ";
			}

			unset($_GET[minYear]);
			unset($_GET[maxYear]);



			foreach($_GET as $key=>$value) {
				if(is_array($value)) {
					$where .= " AND $key IN (";
					foreach($value as $el) { 
						$where .= "?,";
						array_push($vals, $el);
					}
					$where = substr($where, 0, -1);
					$where .= ") ";
				}
				else { 
					$where .= " AND $key=? ";
					array_push($vals, $value);
				}
			}
		}

		$stmt = $file_db->prepare("SELECT * FROM cars WHERE id IN ($ids_list) $where");
		$stmt->execute($vals);

		return $stmt;
	}

	function grid_view($stmt) { 
		$string = "";
		while ($car = $stmt->fetch()) {
			$conds = explode("_", $car[condition]);
			
			$condition = ucfirst($conds[0]);
			if(count($conds) > 1) { 
				$condition .= " " . ucfirst($conds[1]);
			}

			$image = $car[image];
			if($image == "") { 
				$image = "no_image.jpg";
			}

			$string .= "
				<div class='col-md-4 col-sm-6'>
                            <div class='thumbnail'>
                                        <img data-src='holder.js/300x200' alt='300x200' src='images/$image'>
                                    <span class='label label-success'>$" . number_format($car[price]) . "</span>
                                    <div class='caption'>
                                        <h4 class='title'><a href=''>" . ucfirst($car[color]) . " $car[make] $car[model] $car[year]</a></h4>
                                        <ul class='list-unstyled'>
                                            <li><span><strong>Condition:</strong> " . $condition . "</span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
								</div>
								";
		}	

		if($string == "") { 
			$string = "<h3 style='text-align:center'>NO RESULTS</h3>";
		}

		return $string;
	}

	function get_models () { 
		$file_db = get_db();
		return $file_db->query('SELECT DISTINCT make FROM cars ORDER BY make ASC');
	}

	if($_GET["ajax"] == "true") {
		unset($_GET['ajax']);
		echo grid_view(get_cars());
		exit;
	}


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>jQuery Facets Demo</title>

    <!-- Bootstrap core CSS -->
    <link href="assets/css/bootstrap.css" rel="stylesheet">




    <!-- Custom CSS Assets -->

    <link href="assets/css/scojs.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/jquery.fs.picker.css">
    <link href="assets/css/jquery.fs.selecter.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/jquery.fs.scroller.css">
    <link rel="stylesheet" href="assets/css/font-awesome.css">
    <link href="assets/css/theme.css" rel="stylesheet">


</head>

<body>


    <!-- /Wrap -->
    <div id="wrap">

        <div id="login" class="collapse">
            <div class="container">
                <div class="top-form-inner">

                    <form class="form-inline" role="form">
                        <div class="form-group">
                            <label class="sr-only" for="exampleInputEmail2">Email address</label>
                            <input type="email" class="form-control" id="exampleInputEmail2" placeholder="Enter email">
                        </div>
                        <div class="form-group">
                            <label class="sr-only" for="exampleInputPassword2">Password</label>
                            <input type="password" class="form-control" id="exampleInputPassword2" placeholder="Password">
                        </div>
                        <a href="#" class="btn btn-primary">Login</a>
                    </form>
                </div>
            </div>
        </div>

        <nav class="navbar  navbar-default" role="navigation">
            <div class="container">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="index.php"><span class="glyphicon glyphicon-dashboard"></span> Auto<span class="sec-brand">Market.</span></a>
                </div>

                <!-- Collect the nav links, forms, and other content for toggling -->
                <div class="collapse navbar-collapse navbar-ex1-collapse">
                    <form class="navbar-form navbar-left" role="search" action="/jquery/jquery_facets/">
                        <div class="form-group">
							<input name="search" type="text" class="form-control input-lg" placeholder="Search..." value="<?php echo $_GET['search'] ?>">
                        </div>
                    </form>


                    <div class="btn-toolbar pull-right">
                    	<p class="navbar-text pull-right"><a class="navbar-link" href="http://srchulo.com/jquery_plugins/jquery_facets.html">Back to jQuery Facets</a></p>
                    </div>





                </div>
                <!-- /.navbar-collapse -->
            </div>
            <!-- /.container -->
        </nav>


        <div id="top">
            <nav class="secondary navbar navbar-default" role="navigation">
                <div class="container">
                    <!-- Brand and toggle get grouped for better mobile display -->
                    <div class="navbar-header">
                        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex5-collapse">
                            <span class="sr-only">Toggle navigation</span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                    </div>

                    <!-- Collect the nav links, forms, and other content for toggling -->
                    <div class="collapse navbar-collapse navbar-ex5-collapse">
                        <ul class="nav navbar-nav">
                            <li class="active"><a href="#">Home</a>
                            </li>
                            <li><a href="#">Find Cars</a>
                            </li>
                            <li><a href="#">Car Dealers</a>
                            </li>
                            <li><a href="#">Parts</a>
                            </li>

                        </ul>
                    </div>
                    <!-- /.navbar-collapse -->
                </div>
            </nav>
        </div>





        <div class="container">





            <div class="row">

                <div class="col-md-3 col-sm-4"> 
                    <!-- left sec -->
                    <div class="left-sec">



                        <div class="left-cont">
                            <h6><span class="glyphicon glyphicon-search"></span> Refine Search</h6>
			   <form class="filter-sec" id="facets">

                                <select name="make" class="form-control">
                                    <option value="">Make</option>
									<?php
										$result = get_models();
										
										foreach ($result as $m) {
									?>
											<option value="<?php echo $m['make'] ?>"><?php echo $m['make'] ?></option>
									<?php
										}
									?>
                                </select>

				<h5>Condition:</h5>
				  <div class="input-control">

                                    <input name="condition[]" class="checkbox" id="new" type="checkbox" value="new" />
                                    <label for="new">New</label>

                                    <input name="condition[]" class="checkbox" id="like_new" type="checkbox" value="like_new" />
                                    <label for="like_new">Like New</label>

                                    <input name="condition[]" class="checkbox" id="used" type="checkbox" value="used" />
                                    <label for="used">Used</label>

                                    <input name="condition[]" class="checkbox" id="really_used" type="checkbox" value="really_used" />
                                    <label for="really_used">Really Used</label>

                                </div>

                                <h5>Price:</h5>
                                <div class="row">
                                    <div class="col-lg-6 col-sm-6">
                                        <input name="minPrice" id="minPrice" type="text" class="form-control input-sm" placeholder="Low">
                                    </div>
                                    <div class="col-lg-6 col-sm-6">
                                        <input name="maxPrice" id="maxPrice" type="text" class="form-control input-sm" placeholder="High">
                                    </div>
                                </div>



                                <h5>Year:</h5>
                                <div class="row">
                                    <div class="col-lg-6 col-sm-6">
                                        <input name="minYear" id="minYear" type="text" class="form-control input-sm" placeholder="1999">
                                    </div>
                                    <div class="col-lg-6 col-sm-6">
                                        <input name="maxYear" id="maxYear" type="text" class="form-control input-sm" placeholder="2013">
                                    </div>
                                </div>

				<h5>Color:</h5>
				  <div class="input-control">

                                    <input name="color[]" class="checkbox" id="red" type="checkbox" value="red" />
                                    <label for="red">Red</label>


                                    <input name="color[]" class="checkbox" id="blue" type="checkbox" value="blue" />
                                    <label for="blue">Blue</label>

                                    <input name="color[]" class="checkbox" id="green" type="checkbox" value="green" />
                                    <label for="green">Green</label>

                                    <input name="color[]" class="checkbox" id="yellow" type="checkbox" value="yellow" />
                                    <label for="yellow">Yellow</label>

                                    <input name="color[]" class="checkbox" id="silver" type="checkbox" value="silver" />
                                    <label for="silver">Silver</label>

                                    <input name="color[]" class="checkbox" id="white" type="checkbox" value="white" />
                                    <label for="white">White</label>

                                    <input name="color[]" class="checkbox" id="black" type="checkbox" value="black" />
                                    <label for="black">Black</label>

                                </div>




                                <div class="input-control">

                                    <input name="onlyPhotos" class="checkbox" id="onlyPhotos" type="checkbox" value="1" />
                                    <label for="onlyPhotos">Only Include Listings w/ Photos</label>


                                </div>

                            </form>
                        </div>





                        <div class="left-cont">
                            <h4>Subscribe for updates </h4>
                            <p>nec eu singulis petentium. Ea quo modus officiis, tation mucius conclusionemque an vix. Ad vix deserunt consequat quaerendum.</p>
                            <form action="valid.json" id="valid_form" role="form">
                                <div class="form-group">
                                    <input type="text" class="form-control" placeholder="Name">
                                    <br>
                                    <input type="text" class="form-control" placeholder="Your Email Address">

                                    <br>
                                    <button id="message_trigger_ok" type="button" class="btn btn-warning  btn-block">Subscribe</button>


                                </div>
                            </form>

                        </div>




                    </div>
                    <!-- /left sec -->
                </div>


                <div class="col-md-9 col-sm-8">
                    <div class="right-sec">


                        <div class="top">
                            <ul class="nav nav-tabs tooltip-demo">
                                <li class="active"><a href="#">All Listings  <strong>432</strong></a>
                                </li>
                                <li><a href="#">Most Popular </a>
                                </li>
                                <li><a href="#">Recently Posted</a>
                                </li>
                            </ul>
                        </div>




                        <div class="row" id="searchCont">

							<?php echo grid_view(get_cars()); ?>


                            <hr>


                        </div>
                    </div>
                </div>
            </div>



            <hr>






            <hr>



        </div>
        <!-- /.container -->





        <div class="sub-foot">



    </div>

    <!-- /Wrap -->



    <!-- Footer -->

    <div id="footer">
        <div class="container">

            <ul class="list-inline">
                <li><a href="index.php">Home</a>
                </li>
                <li><a href="#">Terms</a>
                </li>
                <li><a href="#">About</a>
                </li>
                <li><a href="#">Our Blog</a>
                </li>
                <li><a href="#">Contact Us</a>
                </li>
                <li><a href="#">List your Vehicle</a>
                </li>
                <li><a href="browse-listings-grid.html">Browse Vehicles</a>
                </li>
            </ul>
            <p class="text-muted credit">&copy; 2013 <strong>AutoMarket</strong> &middot; 
            </p>
        </div>
    </div>

    <!-- /Footer -->




    <!-- javascript -->
    <script src="js/jquery.min.js"></script>
    <script type='text/javascript' src='js/jquery.deserialize.js'></script> 
    <script type='text/javascript' src='js/jquery.facets.js'></script>
    <script type='text/javascript' src='js/demo.js'></script>






    <script src="assets/js/jquery.fs.selecter.js"></script>
    <script src="assets/js/jquery.fs.picker.js"></script>
    <script src="assets/js/jquery.fs.scroller.js"></script>

    <script src="assets/js/bootstrap.js"></script>
    <script src="assets/js/theme.js"></script>
    <script src="assets/js/sco.modal.js"></script>
    <script src="assets/js/sco.confirm.js"></script>
    <script src="assets/js/sco.ajax.js"></script>
    <script src="assets/js/sco.collapse.js"></script>
    <script src="assets/js/sco.countdown.js"></script>
    <script src="assets/js/sco.message.js"></script>




    <script>
        $(document).ready(function (e) {




            $(".selecter_label_1").selecter({
                defaultLabel: "Select a Make"
            });

            $(".selecter_label_2").selecter({
                defaultLabel: "Select A Model"
            });

            $(".selecter_label_3").selecter({
                defaultLabel: "Condition"
            });

            $(".selecter_label_4").selecter({
                defaultLabel: "Transmission"
            });

            $("input[type=checkbox], input[type=radio]").picker();

        });
    </script>
    <!-- /Javascript -->



</body>

</html>
