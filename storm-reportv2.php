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
      <title>Location</title>
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
      <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
      <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker.css" rel="stylesheet" type="text/css" />
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
         .txt1, .rs-tooltip-text .txt2 {
    font-size: 12px;
}
.rs-tooltip-text .val {
    font-size: 12px;
    margin-top: 10px;
}
div#slider1:after {
    content: " ";
    display: block;
    height: calc(100% - 40px);
    width: calc(100% - 40px);
    position: absolute;
    top: 20px;
    left: 20px;
    z-index: 9;
    border-radius: 1000px;
    box-shadow: 0 0 10px -2px;
    
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
         margin: 30px 0;
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
         margin-bottom: 65px;
         }
         .output {
         width: 50px;
         color: #EC8500;
         font-weight: 600;
         text-align: center;
         margin: -20px auto 30px auto;
         }
         .select-form{margin-top:40px;}
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
         padding: 0 20px;
         border: 1px solid #ccc;
         border-radius:5px;
         }
         div#res a[target=_blank] b{
         color: #212121 !important;opacity:1; cursor: pointer;
         }
         div#res a{display:block;}
         .enadd{margin-bottom:20px;}
div#slider1 {
    margin: 10px auto 40px;
}

input.sbt {
    padding: 15px 40px;
    background: #263F78;
    color: #fff;
    border: none;
    border-radius: 30px;
    font-size: 20px;
    margin: 20px auto;
    display: block;
}
select.eventType.sel-event-type {
    width: 100%;
    
    padding: 6px;
    border-radius: 7px;
}

.description {
    margin: 39px auto;
    border-top: 1px solid #ccc;
    padding-top: 30px;
}
.left-bl{position:relative;}
.col-lg-6.col-sm-12.col-xs-12.text-center.left-bl:after {
    content: "";
    position: absolute;
    right: 0;
    background: #007ec6;
    height: 340px;
    width: 2px;
    top: -19px;
    display:none;
}
.col-lg-6.col-sm-12.box-st-cs {
    box-shadow: 1px 1px 12px 1px rgba(0,0,0,0.2);
    padding: 20px;
    background: #fff;
}
         @media (max-width:992px){div#locationField span {
    display: block;
    font-size: 30px;
    font-weight: bold;
    text-align: center;
    margin-bottom: 60px;
}}
      </style>
       <script>
       
       
         function validate()
         {
             
             var long= $(".long").val( );
             var lat=  $(".lat").val( );
             var state= $(".state").val( );
             var sdate= $(".start-date").val( );
             var edate= $(".end-date").val( );
             var  eventType =$(".eventType").val( );
             var distance=$(".val").html();
             $(".distance").val(distance);
             
            var date1 = new Date(sdate);
            var date2 = new Date(edate);
            var diffDays = parseInt((date2 - date1) / (1000 * 60 * 60 * 24), 10); 
            
             
            
             var location= $("#autocomplete").val( );
             
             
             if (location  === '') {
                    alert('Please enter your location.');
                    $( "#autocomplete" ).focus();
                    return false;
             }
             else if (eventType  === '') {
                    alert('Please choose event type');
                     $( ".eventType" ).focus();
                    return false;
             }
             
             else if (sdate  === '') {
                    alert('Please enter start date');
                     
                    return false;
             }
              else if (edate  === '') {
                    alert('Please enter end date');
                    return false;
             }
             else if( Date.parse(edate) <= Date.parse(sdate)){
                alert("Start date must be lesser");
                return false;
             }
             else if(diffDays >= 30)
             {
                alert("Date difference within 30 days only!!");
                return false; 
             }
             else
             {
                 return true;
             }
             
                
             return false;
         }
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
                            
                            $(".long").val(longitude);
                            $(".lat").val(latitude);
                            $(".state").val(state);
                            
                         }
                         else
                         {
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
         <!--<div class="logo"><img src="image/noaa.png"></div>-->
         <h2 class="main-heading text-center">Storm Report</h2>
         <div id="locationField">
        <form action="map2.php" class="form-horizontal" onsubmit="return validate()" method="post">
            <div class="row">
                
               <div class="col-lg-6 col-sm-12 col-xs-12 text-center left-bl">
                  <div id="slider1"></div>
               </div>
               <div class="col-lg-6 col-sm-12 box-st-cs">
                  <label class="enadd">Enter your Address :</label> <input id="autocomplete" class="form-control"
                     placeholder="Enter your address"
                     onFocus="geolocate()"
                     type="text" name="location" readonly/>
                  <div id="res">
                  </div>
               </div>
            </div>
            <div class="row">
               <div class="col-lg-12 col-sm-12 select-form">
                   <div class="form-group">
                        <label class="control-label col-sm-2 enadd sel-event" for="date">
                    Select Event Type :
                     
                     </label>
                   <div class="col-sm-10">
                  <select class="eventType sel-event-type" name="eventType"  >
                     <option value="">Select Event</option>
                     <option value="nx3hail">Hail</option>
                     <option value="nx3structure">High Wind</option>
                     <option value="nx3tvs">Tornado</option>
                  </select>
                  </div>
               </div>
               
                    <div class="form-group ">
                     <label class="control-label col-sm-2 requiredField" for="date">
                    * Start Date
                     
                     </label>
                     <div class="col-sm-10">
                        <div class="input-group">
                           <div class="input-group-addon">
                              <i class="fa fa-calendar">
                              </i>
                           </div>
                           <input class="form-control start-date datefield" autocomplete="off"   id="date" name="date" placeholder="'yyyy/mm/dd'" type="text"/>
                        </div>
                     </div>
                  </div>
              
              
                     <div class="form-group ">
                     <label class="control-label col-sm-2 requiredField" for="date">
                     * End Date
                     
                     </label>
                     <div class="col-sm-10">
                        <div class="input-group">
                           <div class="input-group-addon">
                              <i class="fa fa-calendar">
                              </i>
                           </div>
                           <input class="form-control end-date datefield" autocomplete="off" id="date2" name="date2" placeholder="'yyyy/mm/dd'" type="text"/>
                        </div>
                     </div>
                     <div class="col-sm-10">
                        <input type="hidden" class="state" name="state"  value=""/>
                        <input type="hidden" class="lat" name="lat"  value=""/>
                        <input type="hidden" class="long" name="long"  value=""/>
                        <input type="hidden" class="distance" name="distance"  value=""/>
                     </div>
                  </div>
              
               </div>
           
            </div>
            
            <div class="row">
               <div class="col-lg-12 col-sm-12">
            <input type="submit" class="sbt"  name="submit"  value="Enter"/>
              </div>
      </div>
        </form>
        <div class="description">
                      <p class="">This module is maintained by NOAA's National Weather Service (NWS). 
                      Due to changes in the data collection and processing procedures over time, there are unique periods of record available depending on the event type. 
                      NCEI has performed data reformatting and standardization of event types but has not changed any data values for locations, 
                      fatalities, injuries, damage, narratives and any other event specific information. Please refer to NOAA site for more information.</p>
                  </div>
         </div>
      </div>
      <footer>
         <div class="ctext text-center">Powered by Xactbid</div>
      </footer>
   </body>
   <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <!-- <script src="js/jquery-slider-min.js"  defer></script>-->
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
      
      $(function() {
      	 
      //	$('.preview3').createSlide({ output: '.output', maxvalue: 50 });
      	$("#autocomplete").click(function(){
      	   
      	     var distance = $(".val").html( );
      	     if(distance ==  null || distance!=0){
      	          
                   $("#autocomplete").attr("readonly", false); 
                   }
                   else
                   {
                       alert("Please choose distance!");   
                   }
      });
      	 
      });
   </script>
   <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA1gf0GrUztgVDDVapXnJlcyYMCilJLubQ&libraries=places&callback=initAutocomplete"
      async defer></script>
   <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
   <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/js/bootstrap-datepicker.js"></script>
   <script>
      $(document).ready(function(){
        $(".datefield").datepicker({ 
             autoclose: true, 
             todayHighlight: true
       }).datepicker('update', new Date());
      })
   </script>    
</html>