<?php
 include 'includes/common_lib.php';
$display="";
if(!$_SESSION['ao_founder'])
{
    $display="style='display:none;'";
      die('Insufficient access rights!!');
}
else
{
  
}
?>
 
<!DOCTYPE html>
<html>
   <head>
      <title>Storm Report</title>
      <?php include('storm-db-conn/conn.php');  ?>
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
      <style>
         /* Presentation CSS */
         body {
         margin: 0;
         padding: 0;
         font-family: 'Roboto Condensed',Helvetica,Arial,sans-serif;
         background-color: #fafafa;
         }
         .logo img {
         width: 200px;
         margin: 0 auto;
         }
         .logo {
         text-align: center;
         }
         h2.main-heading.text-center:first-letter {
         background: #007EC6;
         padding: 0px 10px;
         color: #fff;
         border-radius: 5px;
         margin-right: 5px;
         }
         .ctext.text-center {
         background: #263F78;
         color: #fff;
         padding: 10px;
         font-size: 18px;
         }
         .wrap {
         max-width: 500px;
         padding-bottom: 30px;
         margin: 150px auto 0 auto;
         }
         .heading {
         color: #009481;
         text-align: center;
         margin: 0 0 40px 0;
         }
         .heading a {
         color: #FFA11B;
         }
         .explain {
         margin: 0 0 40px 0;
         padding: 30px 0 0 0;
         border-top: 1px solid #ccc;
         }
         .explain span {
         display: block;
         width: 100%;
         padding: 7px 15px;
         margin: 8px 0 0 0;
         font-size: 15px;
         color: #394873;
         background-color: #ECECEC;
         box-sizing: border-box;
         }
         .explain:first-child {
         border: none;
         }
         .container1{width:80%; margin:30px auto;}
         div#locationField span {
         display: block;
         font-size: 30px;
         font-weight: bold;
         text-align: center;
         }
         div#locationField {
         margin: 50px 0;
         }
         span.output {
         margin-top: 20px;
         border: 1px solid;
         }
         h2.main-heading.text-center {
         font-size: 40px;
         font-weight: 500;
         border-bottom: 3px solid #212121;
         width: max-content;
         margin: auto;
         padding: 10px;
         margin-top:30px;
         }
         .output {
         width: 50px;
         color: #EC8500;
         font-weight: 600;
         text-align: center;
         margin: -20px auto 30px auto;
         }
         div:focus {
         outline: none;
         }
         .ui-loader {
         display: none;
         }
         /* Plugin CSS */
         .container {
         height: 5px;
         margin: 0 auto 20px auto;
         background-color: #DADADA;
         }
         .dragger {
         width: 40px;
         height: 40px;
         color: #FFF;
         font-weight: 600;
         line-height: 40px;
         background-color: #333;
         border-radius: 25px;
         left:0;
         }
         .dragging {
         background-color: #FFC168;
         box-shadow: inset 0px 0px 20px #222;
         }
         .progress {
         width: 0px;
         background-color: #222;
         }
         div#res a b {
         color: #000;
         font-weight: 500;
         line-height: 25px;
         font-size: 15px;
         position: relative;
         padding-left: 20px;
         opacity:0.6;
         cursor: initial;
         text-decoration: none;
         
         }
         div#res a b:hover{text-decoration: none;}
         div#res a b:before {
         background: #ccc;
         position: absolute;
         content: "";
         height: 7px;
         width: 7px;
         top: 4px;
         left: 0;
         }
         div#res {
         background: #dfeff9;
         margin: 30px 0 0;
         padding: 0 20px 0px;
         border: 1px solid #ccc;
         border-radius:5px;
         }
         div#res a[target=_blank] b{
         color: #212121 !important;opacity:1; cursor: pointer;
         }
         div#res a{display:block;}
         .enadd{margin-bottom:20px; margin-top:60px;}
         #slider2 .rs-handle {
         background-color: #f3f3f3;
         box-shadow: 0px 0px 4px 0px #000;
         }
         #slider2 .rs-tooltip-text {
         font-size: 25px;
         font-weight: bold;
         }
         div#slider1{margin:auto;}
         @media (max-width:992px){div#locationField span {
         display: block;
         font-size: 30px;
         font-weight: bold;
         text-align: center;
         margin-bottom: 60px;
         }}
         .rs-range-color {
         background-color: #33B5E5;
         }
         .rs-path-color {
         background-color: #C2E9F7;
         }
         .rs-handle {
         background-color: #C2E9F7;
         padding: 7px;
         border: 2px solid #C2E9F7;
         }
         .rs-handle.rs-focus {
         border-color: #33B5E5;
         }
         .rs-handle:after {
         border-color: #33B5E5;
         background-color: #33B5E5;
         }
         .rs-border {
         border-color: transparent;
         }
         .rs-tooltip-text {
         font-family: Roboto, sans-serif;
         border-radius: 7px;
         transition: background 0.02s ease-in-out;
         color: #33B5E5;
         }
         .rs-tooltip-text .val {
         font-size: 12px;
         margin-top: 10px;
         }
         .rs-tooltip-text .txt1 {
         margin-top: -5px;
         }
         .rs-tooltip-text .txt2 {
         margin-top: 10px;
         }
         .container{
         position: absolute;
         z-index: 2;
         top: 50%;
         left: 50%;
         transform: translate(-50%, -50%);
         font-family: Roboto, sans-serif;
         padding: 20px;
         border: 1px solid;
         }
         /* Solution for inner circle with shadow */
         #slider1:after {
         content: " ";
         display: block;
         height: calc(100% - 40px); /* here 40 is the gap between the outer and inner circle */
         width: calc(100% - 40px);
         position: absolute;
         top: 20px;  /* divide the gap value by 2 */
         left: 20px;
         z-index: 9; /* tooltip z-index is 10, so we put less than that value */
         border-radius: 1000px;
         box-shadow: 0 0 10px -2px;
         }
         #slider1 .rs-overlay {
         height: calc(50% + 5px);
         width: calc(50% + 5px);
         top: -5px;
         left: -5px;
         border-radius: 1000px 0 0 0;
         }
         .txt1, .rs-tooltip-text .txt2 {
    font-size: 12px;
}
.left-bl{position:relative;    margin-top: 20px;}
.col-lg-6.col-sm-12.col-xs-12.text-center.left-bl:after {
    content: "";
    position: absolute;
    right: 0;
    background: #007ec6;
    height: 340px;
    width: 2px;
    top: -19px;
}
br {
    margin-top: -53px;
    content: "";
    position: relative;
}
.description {
    
    margin: 39px auto;
    border-top: 1px solid #ccc;
    padding-top: 30px;
}
.description p {
    font-size: 14px;
    line-height: 24px;
    font-weight: normal;
}

.description p a {
    color: #263F78;
    font-weight: 600;
}
a.reset {
    background: #263F78;
    color: #fff;
    padding: 10px 30px;
    margin: 30px auto 0;
    display: block;
    width: max-content;
    border-radius: 30px;
    font-size: 17px;
}
      </style>
      <script>
         var placeSearch, autocomplete;
         
         var componentForm = {
           street_number: 'short_name',
           route: 'long_name',
           locality: 'long_name',
           administrative_area_level_1: 'short_name',
           country: 'long_name',
           postal_code: 'short_name'
         };
         
         function initAutocomplete() {
           autocomplete = new google.maps.places.Autocomplete(
           document.getElementById('autocomplete'), {types: ['geocode']});
           autocomplete.setFields(['address_component']);
           autocomplete.addListener('place_changed', update_coordinates);
         }
         
         function update_coordinates() {
           var place = autocomplete.getPlace();
           var geocoder = new google.maps.Geocoder();
           var address = document.getElementById('autocomplete').value;
           var distance = $(".val").html( );  
           geocoder.geocode({ 'address': address }, function (results, status) {
                     if (status == google.maps.GeocoderStatus.OK) {
                         var latitude = results[0].geometry.location.lat();
                         var longitude = results[0].geometry.location.lng();
                         for (var i = 0; i < place.address_components.length; i++) {
                                 if (place.address_components[i].types[0] == "administrative_area_level_1") {
                                   var state = place.address_components[i]['long_name'];
                                 }
                             }
                         if((latitude != null ) && (longitude != null ) && (state != null )){
                             if(distance ===  null || distance==0){
                                alert("Distance cannot be null!");   
                             }else{ 
                                
                           $(".rec").show(  );   
                         $("#res").html( 'Loading...!!!' );  
                        $.ajax({
                           method: "POST",
                           url: "storm-ajax/ajax.php",
                           data: { latitude: latitude, longitude: longitude, state: state, distance :distance, address: address}
                         })
                           .done(function( msg ) {
                                 
                            $("#res").html( msg );
                          
                           });
                         
                             }
                             
                          
                           
                            
                         }
                         else
                         {
                              $(".rec").hide(  );
                             alert('Invalid Location!!');
                         }
                     }
                 });
         }
         
         function geolocate() {
           if (navigator.geolocation) {
             navigator.geolocation.getCurrentPosition(function(position) {
               var geolocation = {
                 lat: position.coords.latitude,
                 lng: position.coords.longitude
               };
               var circle = new google.maps.Circle(
                   {center: geolocation, radius: position.coords.accuracy});
               autocomplete.setBounds(circle.getBounds());
             });
           }
         }
             
      </script>
   </head>
   <body>
      <div class="container1">
        <!-- <div class="logo"><img src="image/noaa.png"></div> -->
         <h2 class="main-heading text-center">Storm Report</h2>
         <div id="locationField">
            <div class="row">
               <div class="col-lg-6 col-sm-12 col-xs-12 text-center left-bl">
                  <div id="slider1"></div>
                  <!--<span class="output">0</span>-->
                   <label class="enadd">Enter your Address :</label> <input id="autocomplete"  class="form-control"
                     placeholder="Enter your address"
                     onFocus="geolocate()"
                     type="text" readonly/>
               </div>
               <div class="col-lg-6 col-sm-12 col-xs-12">
                 <h3 class="rec" style="display:none;">Storm Events Year-wise Records</h3>
                  <div id="res">
                      Please provide your inputs.
                  </div>
                <a href="" style="display:none;" class="reset">Reset</a>
               </div>
                 
            </div>
            <?php 
           $sql = "SELECT date_format(str_to_date(END_YEARMONTH, '%Y%m'), '%M, %Y') as mdate FROM storm_details order by END_YEARMONTH desc LIMIT 0,1"; 
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
            $row  = $result->fetch_assoc()   ;
            }
            
            ?>
            
            <?php $sql = "SELECT * FROM updated_date where id =1"; 
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
            $row  = $result->fetch_assoc()   ;
            } ?>
            
            <div class="description">
                      <p>The database currently contains data from January 2013 to <?php echo $row ['updatedate']; ?>, as maintained by NOAA's National Weather Service (NWS). 
                      Due to changes in the data collection and processing procedures over time, there are unique periods of record available depending on the event type. 
                      NCEI has performed data reformatting and standardization of event types but has not changed any data values for locations, 
                      fatalities, injuries, damage, narratives and any other event specific information. Please refer to NOAA site for more information.</p>
                  </div>
         </div>
      </div>
      <footer>
         
         <div class="ctext text-center">Last updated on: <?php echo $row ['updatedate']; ?></div>
         <?php
            $conn->close();
         ?>
      </footer>
   </body>
   <!--<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>-->
   <!--<script src="js/jquery-slider-min.js"  defer></script>-->
   <script src="https://code.jquery.com/jquery-3.2.1.js"></script>
   <link href="https://cdn.jsdelivr.net/npm/round-slider@1.6.1/dist/roundslider.min.css" rel="stylesheet" />
   <script src="https://cdn.jsdelivr.net/npm/round-slider@1.6.1/dist/roundslider.min.js"></script>
   <script>
      $("#slider1").roundSlider({
      radius: 80,
      width: 8,
      min: 0,
      max: 50,
      handleSize: "+16",
      circleShape: "pie",
      handleShape: "dot",
      sliderType: "min-range",
      startAngle: 315,
      value: 0,
      disabled: false,
      editableTooltip: false,
      change: function (args) {
              $('.val').html(this.getValue());
                 if(args.value ==  null || args.value!=0){
                     $(".reset").show(); 
                  $("#autocomplete").attr("readonly", false); 
                  $("#autocomplete").focus();
                 }else{
                     $("#autocomplete").attr("readonly", true);
                     $(".reset").show(); 
                 }
           } ,
      drag: function (event, ui) {
              $('.val').html(this.getValue());
                
          },
          tooltipFormat: function (e) {
        return `
           <div class='txt1'> DISTANCE </div>
           <div class='val'> ${e.value} </div>
           <div class='txt2'> Miles </div>
        `;
      }
      });
      
      /* tooltipFormat: function (e) {
        return `
          <div class='txt1'> COOLING </div>
          
          <div class='val'> ${e.value} </div>
          
          <div class='txt2'> 19 </div>
        `;
      }*/
          
   </script>
   <script>
      $(function() {
        $("#autocomplete").click(function(){
           
             var distance = $(".val").html( );
             if(distance ==  null || distance!=0){
                  
                   $("#autocomplete").attr("readonly", false); 
                   }
                   else
                   {
                       alert("Please choose distance!"); 
                       $("#autocomplete").attr("readonly", true); 
                   }
      });
      });
   </script>
   <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA1gf0GrUztgVDDVapXnJlcyYMCilJLubQ&libraries=places&callback=initAutocomplete"
      async defer></script>
</html>