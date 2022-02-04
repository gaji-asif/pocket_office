<?php
$display="";
if(!$_SESSION['ao_founder'])
{
    $display="style='display:none;'";
 die('Insufficient access rights!!');
}
   $miles=$_REQUEST["distance"];
   function milesToKilometers($miles){
       return  $miles * 1.60934;
   }
   
   function milesToMeters($miles) {
        $miles=$miles+.5;
       $miles= $miles / 0.0006213711;
       return  $miles;
   }
$distance = milesToKilometers($miles);
$earthRadius = 6371;
$lat1 = deg2rad($_REQUEST["lat"]);
$lon1 = deg2rad($_REQUEST["long"]);
$bearing = deg2rad(0);
$lat2 = asin(sin($lat1) * cos($distance / $earthRadius) + cos($lat1) * sin($distance / $earthRadius) * cos($bearing));
$lon2 = $lon1 + atan2(sin($bearing) * sin($distance / $earthRadius) * cos($lat1), cos($distance / $earthRadius) - sin($lat1) * sin($lat2));
$lat2=  round(rad2deg($lat2));
$lon2 =    round(rad2deg($lon2));

   //https://www.ncdc.noaa.gov/swdiws/json/nx3tvs/20060506:20060507?tile=-102.12,32.62 
   
   //https://www.ncdc.noaa.gov/swdiws/json/nx3structure/20160719:20160720?bbox=-77,38,-76,39
   
   //working //https://www.ncdc.noaa.gov/swdiws/json/nx3hail/20060606:20060630?tile=-105,40,-105,39
  
   //https://www.ncdc.noaa.gov/swdiws/json/nx3hail/20060606:20060630?bbox=-105,40,-105,39
   
     $date=array_reverse(explode("/",$_POST['date']));
     $date2=array_reverse(explode("/",$_POST['date2'])); 
     $date =$date[0].$date[2].$date[1];
     $date2 =$date2[0].$date2[2].$date2[1]; 
     $url = 'https://www.ncdc.noaa.gov/swdiws/json/'.$_POST["eventType"].'/'.$date.':'.$date2.'?tile='.$lon2.','.$lat2.','.round($_POST["long"]).','.round($_POST["lat"]); 
     //$url = 'https://www.ncdc.noaa.gov/swdiws/json/nx3hail/20060606:20060630?tile=-105,40,-105,39';             
        
                     $crl = curl_init();
                     curl_setopt($crl, CURLOPT_URL, $url);
                     curl_setopt($crl, CURLOPT_FRESH_CONNECT, true);
                     curl_setopt($crl, CURLOPT_RETURNTRANSFER, true);
                    $response = curl_exec($crl);
                    $jsn=json_decode($response);
                     if(!$response){
                        // die('Error: "' . curl_error($ch) . '" - Code: ' . curl_errno($ch));
                     }
                     curl_close($crl);
   ?>
<html>
   <head>
      <title>XactBid Storm Reports</title>
      <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
      <script src="https://maps.google.com/maps/api/js?key=AIzaSyA1gf0GrUztgVDDVapXnJlcyYMCilJLubQ" type="text/javascript"></script>
      <style>
         th {
         background: #cccccc;
         font-family: Helvetica, Arial, sans-serif;
         font-weight: bold;
         text-align: center;
         font-size: 15px;
         line-height: 30px;
         }
         .rpttext {
         font-family: monospace;
         font-size: 8pt;padding: 8px;
         }
         div#map {
         margin-bottom: 30px;
         }
         a[target]{color:#880;}
         .ctext.text-center {
         background: #263F78;
         color: #fff;
         padding: 10px;
         font-size: 18px;
         }
         .cs-print {
    width: 60%;
    margin: 30px auto;
    padding: 20px;
}
p.lower-footer {
    padding: 20px 0;
    text-align: center;
    background: #d6f0f8;
}
p.something {
    padding: 20px;
    text-align: center;
    width: 40%;
    margin: 20px auto;
    box-shadow: 
      </style>
   </head>
   <body>
      <div class="map-custom">
         <div class="container">
            <div class="row">
               <div class="col-lg-12 col-md-12 col-sm-12">
                  <div class="custom-map-pop">
                     <?php if(!empty($jsn->swdiJsonResponse->columnTypes)){ ?>
                     <div id="map" style="height: 100%; width: auto;"></div>
                     <script type="text/javascript">
                        var locations = [                                 
                        ['Main Location : <?php echo $_REQUEST["location"]; ?>',  <?php echo $_REQUEST["lat"]; ?>, <?php echo $_REQUEST["long"]; ?>, 1 , "images/maincenter2.png"],
                        <?php
                           foreach( $jsn->result as $value) { 
                            $latlngstr=str_replace("POINT (","",$value->SHAPE );
                            $latlngstr=str_replace(")","",$latlngstr);
                            $latlngstr=explode(" " ,$latlngstr);
                            if($_POST['eventType']=='nx3hail'){
                             	   echo "['Event : ". $_POST['eventType']." | Date & Time : ". $value->ZTIME." | Magnitude : ". $value->MAXSIZE." | Location : ". $_POST['state']." ', ". $latlngstr[1].", ".$latlngstr[0].", 2 , 'img.php?deep=". $value->MAXSIZE."'],";
                            	   }
                            	    else if ($_POST['eventType']=='nx3structure') 
                            	   {  
                                    echo "['Event : ". $_POST['eventType']." | Date & Time : ". $value->ZTIME." | Magnitude : ". $value->MAXSIZE." | Location : ". $_POST['state']." ', ". $latlngstr[1].", ".$latlngstr[0].", 2 , 'images/wind.png'],";
                                   } 
                                    else 
                                  {  
                               echo "['Event : ". $_POST['eventType']." | Date & Time : ". $value->ZTIME." | Magnitude : ". $value->MAXSIZE." | Location : ". $_POST['state']." ', ". $latlngstr[1].", ".$latlngstr[0].", 2 , 'images/tornado.png'],";
                                 }
                             }                    
                            ?>
                            ];                            
                            var map = new google.maps.Map(document.getElementById('map'), {
                              zoom: 9,
                              center: new google.maps.LatLng(<?php echo floor($lat2).','.floor($lon2); ?>),
                              mapTypeId: google.maps.MapTypeId.ROADMAP,
                              styles: [
                                    {elementType: 'geometry', stylers: [{color: '#242f3e'}]},
                                    {elementType: 'labels.text.stroke', stylers: [{color: '#242f3e'}]},
                                    {elementType: 'labels.text.fill', stylers: [{color: '#746855'}]},
                                    {
                                      featureType: 'administrative.locality',
                                      elementType: 'labels.text.fill',
                                      stylers: [{color: '#d59563'}]
                                    },
                                    {
                                      featureType: 'poi',
                                      elementType: 'labels.text.fill',
                                      stylers: [{color: '#d59563'}]
                                    },
                                    {
                                      featureType: 'poi.park',
                                      elementType: 'geometry',
                                      stylers: [{color: '#263c3f'}]
                                    },
                                    {
                                      featureType: 'poi.park',
                                      elementType: 'labels.text.fill',
                                      stylers: [{color: '#6b9a76'}]
                                    },
                                    {
                                      featureType: 'road',
                                      elementType: 'geometry',
                                      stylers: [{color: '#38414e'}]
                                    },
                                    {
                                      featureType: 'road',
                                      elementType: 'geometry.stroke',
                                      stylers: [{color: '#212a37'}]
                                    },
                                    {
                                      featureType: 'road',
                                      elementType: 'labels.text.fill',
                                      stylers: [{color: '#9ca5b3'}]
                                    },
                                    {
                                      featureType: 'road.highway',
                                      elementType: 'geometry',
                                      stylers: [{color: '#746855'}]
                                    },
                                    {
                                      featureType: 'road.highway',
                                      elementType: 'geometry.stroke',
                                      stylers: [{color: '#1f2835'}]
                                    },
                                    {
                                      featureType: 'road.highway',
                                      elementType: 'labels.text.fill',
                                      stylers: [{color: '#f3d19c'}]
                                    },
                                    {
                                      featureType: 'transit',
                                      elementType: 'geometry',
                                      stylers: [{color: '#2f3948'}]
                                    },
                                    {
                                      featureType: 'transit.station',
                                      elementType: 'labels.text.fill',
                                      stylers: [{color: '#d59563'}]
                                    },
                                    {
                                      featureType: 'water',
                                      elementType: 'geometry',
                                      stylers: [{color: '#17263c'}]
                                    },
                                    {
                                      featureType: 'water',
                                      elementType: 'labels.text.fill',
                                      stylers: [{color: '#515c6d'}]
                                    },
                                    {
                                      featureType: 'water',
                                      elementType: 'labels.text.stroke',
                                      stylers: [{color: '#17263c'}]
                                    }
                                  ]
                            });
                             
                             
                             
                            var infowindow = new google.maps.InfoWindow();
                        
                            var marker, i;
                        
                            for (i = 0; i < locations.length; i++) { 
                              marker = new google.maps.Marker({
                                position: new google.maps.LatLng(locations[i][1], locations[i][2]),
                                map: map,
                                icon: locations[i][4]
                              });
                             if(i==0)
                             {
                                  var circle = new google.maps.Circle({
                                  map: map,
                                  radius: <?php echo milesToMeters($miles); ?>,    // 7.5 miles in metres
                                  fillColor: '#FFFF33'
                                });
                               // circle.bindTo('center', marker, 'position'); 
                             }
                              google.maps.event.addListener(marker, 'click', (function(marker, i) {
                                return function() {
                                  infowindow.setContent(locations[i][0]);
                                  infowindow.open(map, marker);
                                }
                              })(marker, i));
                            }
                     </script>
                     <?php }else { ?>
                     <p class="something">Something went wrong!!!</p>
                     <?php } ?>
                  </div>
                  <?php if(!empty($jsn->swdiJsonResponse->columnTypes)){ ?>
                  <table class="table table-striped" width="100%" cellpadding="0" cellspacing="5" align="center">
                     <tbody>
                        <tr>
                           <th colspan="<?php echo count( (array) $jsn->swdiJsonResponse->columnTypes); ?>"> <?php echo $_POST['eventType']; ?></th>
                        </tr>
                        <tr>
                           <?php foreach($jsn->swdiJsonResponse->columnTypes as $key => $val) { ?>
                           <th><?php echo $key; ?></th>
                           <?php } ?>
                        </tr>
                        <tr>
                           <?php foreach($jsn->result  as   $val) { ?>
                           <?php foreach($val  as   $data) { ?>                
                           <td class="rpttext"><?php echo $data; ?></td>
                           <?php } ?>
                           <?php } ?>
                        </tr>
                     </tbody>
                  </table>
                  <?php } ?>    
               </div>
            </div>
         </div>
      </div>
      <div class="cs-print"><pre><b>Raw API Responce : </b><?php  print_r(json_decode($response)); if(!$response){
                        echo 'Error: "' . curl_error($ch) . '" - Code: ' . curl_errno($ch);
                     } ?></pre></div>
      <footer>
          
         <div class="ctext text-center">Powered by Xactbid</div>
         <p class="lower-footer">
            'nx3tvs'       - (Point)   NEXRAD Level-3 Tornado Vortex Signatures
            'nx3meso'      - (Point)   NEXRAD Level-3 Mesocyclone Signatures 
            'nx3structure' - (Point)   NEXRAD Level-3 Storm Cell Structure Information
         </p>
      </footer>
   </body>
</html>