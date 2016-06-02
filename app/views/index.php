<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Tweeter API & Google Map API</title>

    <link rel="stylesheet" href="<?php echo URL::to('bootstrap-3.3.4/css/bootstrap.min.css');?>">
	<link rel="stylesheet" href="<?php echo URL::to('bootstrap-3.3.4/css/blog-post.css');?>">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
	<script src="http://maps.googleapis.com/maps/api/js?v=3.exp&libraries=places&key=AIzaSyBc7Nk1fAWbFxlfyUEg-loQU6NlqNoz95g"></script>
	
	<script>
		
		function getTweets(){
			if($('#q').val()){
				$('#q').removeClass("input_error");
				geocoder = new google.maps.Geocoder();
				var address = $('#q').val();
				geocoder.geocode( { 'address': address}, function(results, status) {
				  if (status == google.maps.GeocoderStatus.OK) {
					$('#lat').val(results[0].geometry.location.lat());
					$('#lon').val(results[0].geometry.location.lng());
					console.log($('#lat').val(), ',',$('#lon').val());
					$.ajax({
					  type: "POST",
					  url: "<?php echo URL::to('/');?>",
					  data: { q: $('#q').val(), lat: $('#lat').val(), lon:$('#lon').val() },
					  success: function( data ) {
						  initialize($('#lat').val(), $('#lon').val());
						  for (i = 0; i < data.statuses.length; i++) {
						  	console.log(data.statuses[i].coordinates); 
							if(data.statuses[i].coordinates != null){
								console.log($.format.date(data.statuses[i].created_at,"yyyy-MM-dd HH:mm:ss"));
								addMarker(data.statuses[i].coordinates.coordinates[1], data.statuses[i].coordinates.coordinates[0], data.statuses[i].user.profile_image_url_https, data.statuses[i].text, $.format.date(data.statuses[i].created_at,"yyyy-MM-dd HH:mm:ss"));
							}	
						  }
						  
						  center = bounds.getCenter();
						  map.fitBounds(bounds);
						  $('#tweet_search h3').text('TWEETS ABOUT '+$('#q').val());
					  },
					  async: true,
					  dataType: 'json'
					});
				  }
				  else {
					alert("Geocode was not successful for the following reason: " + status);
					
				  }
				});
			
				
			} else {
				$('#q').addClass('input_error');
				$('#q').focus();
			}	
		}
		var lattitude = 13.7627575;
		var lontitude = 100.5370849;
		function initialize() {
		  map = new google.maps.Map(document.getElementById("googleMap"), {
                    center: new google.maps.LatLng(lattitude,lontitude),
                    zoom: 14,
                    mapTypeId: google.maps.MapTypeId.ROADMAP,
                    mapTypeControl: false,
                    mapTypeControlOptions: {
                    style: google.maps.MapTypeControlStyle.HORIZONTAL_BAR
                    },
                    navigationControl: true,
                    navigationControlOptions: {
                     style: google.maps.NavigationControlStyle.SMALL
                    }
                });
                
		}
		google.maps.event.addDomListener(window, 'load', initialize);
		
		var center = null;
		var map = null;
		var currentPopup;
		var bounds = new google.maps.LatLngBounds();
		
		function addMarker(lat, lon, profile_image, tweet_msg, tweet_created_at) {
			
			var pt = new google.maps.LatLng(lat,lon);
			bounds.extend(pt);
			
			var icon = new google.maps.MarkerImage(profile_image,
				   new google.maps.Size(50, 50), new google.maps.Point(0, 0),
				   new google.maps.Point(16, 32));
				   
			var marker = new google.maps.Marker({
				position: pt,
				icon: icon,
				map: map
			});
			var popup = new google.maps.InfoWindow({
				content: 'Tweet: ' + tweet_msg + '<br>When: ' + tweet_created_at,
				maxWidth: 500
			});
			google.maps.event.addListener(marker, "click", function() {
				if (currentPopup != null) {
					currentPopup.close();
					currentPopup = null;
				}
				popup.open(map, marker);
				currentPopup = popup;
			});
			google.maps.event.addListener(popup, "closeclick", function() {
				map.panTo(center);
				currentPopup = null;
			});
		}
		
		
		
	</script>
	<style type="text/css">
		.input_search{
			padding:0;
			border-radius:0;
			padding:7px;
			font-size:12px;
		}
		.input_error{
			background-color:#f2dede;
		}
		#googleMap{
			position: relative;
			padding-bottom: 56.25%;
			padding-top: 30px;
			height: 0;
			overflow: hidden;
		}	
		#googleMap iframe{
			position: absolute;
			top: 0;
			left: 0;
			width: 100%;
			height: 100%;
		}
	</style>
</head>

<body>

    <!-- Navigation -->
    <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
        <div class="container">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
				<a class="navbar-brand" href="#">Tweeter API & Google Map API</a>
            </div>
        </div>
        <!-- /.container -->
    </nav>

    <!-- Page Content -->
    <div class="container">

        <div class="row">

            <!-- Blog Post Content Column -->
            <div class="col-lg-9">
				
				<h1>Search Page</h1>
				
				<hr>
				<div class="row">
					
					<div style="z-index: 9999; position: absolute; padding-top: 10px; width: 100%; text-align: center;color: #5bc0de;" id="tweet_search"><h3></h3></div>
					<div id="googleMap"></div>
				</div>	
				<div class="row">
					<form id="search_form">	
						<input type="text" name="q" class="col-md-6 input_search" id="q" placeholder="City name" value="">
						<input type="hidden" name="lat" id="lat" value="">
						<input type="hidden" name="lon" id="lon" value="">
						<button class="btn btn-info col-md-3 input_search" type="submit">SEARCH</button>
						<button class="btn btn-info col-md-3 input_search" type="button">HISTORY</button>
					</form>	
				</div>
				
            </div>

        </div>
        <!-- /.row -->

        <!-- Footer -->
        <footer>
            <div class="row">
                <div class="col-lg-9">
                    <p>Copyright &copy; 2016</p>
                </div>
            </div>
            <!-- /.row -->
        </footer>

    </div>
    <!-- /.container -->

    <!-- jQuery -->
    <script type="text/javascript" src="<?php echo URL::to('bootstrap-3.3.4/js/jquery.js');?>"></script>
	<script type="text/javascript" src="<?php echo URL::to('bootstrap-3.3.4/js/date_format.js');?>"></script>
	<script type="text/javascript" src="<?php echo URL::to('bootstrap-3.3.4/js/bootstrap.min.js');?>"></script>
	
	<script type="text/javascript">
		function get_geo(){
			geocoder = new google.maps.Geocoder();
			var address = $('#q').val();
			geocoder.geocode( { 'address': address}, function(results, status) {
			  if (status == google.maps.GeocoderStatus.OK) {
				$('#lat').val(results[0].geometry.location.lat());
				$('#lon').val(results[0].geometry.location.lng());
				console.log($('#lat').val(), ',',$('#lon').val());
			  }
			  else {
				alert("Geocode was not successful for the following reason: " + status);
				
			  }
			});
			return false;
		}
		
		$(function() {
			
			$('#search_form').submit(function(){
				getTweets();
				return false;
			});
			
			<?php 
			if(!empty($q)){
			?>
				$('#search_form').submit();
			<?php
			}
			?>
		});

         
	</script>

</body>

</html>
