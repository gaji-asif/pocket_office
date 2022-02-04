<html>
   <head>
      <?php 
      include 'includes/common_lib.php';
      include('storm-db-conn/conn.php');  
      ?>
      <?php
 
$display="";
if(!$_SESSION['ao_founder'])
{
    $display="style='display:none;'";
 die('Insufficient access rights!!');
}
?>
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
      </style>
   </head>
   <body>
      <div class="map-custom">
         <div class="container">
            <div class="row">
               <div class="col-lg-12 col-md-12 col-sm-12">
                  <div class="custom-map-pop">
                     <div id="map" style="height: 100%; width: auto;"></div>
                     <script type="text/javascript">
                        <?php
                        
                        
                        //mites to meter function
                            function milesToMeters($miles) {
                            $miles=$miles+.5;
                            $miles= $miles / 0.0006213711;
                            return round($miles, 1);
                            }
                            
                            
                            
                            function KmToMiles($km) {
                             $miles = $km * 0.62137; 
                             return round($miles,2);
                            }
                            
                            
                            //calculate distance between two places 
                            
                            function distance($lat1, $lon1, $lat2, $lon2) {   

                            $pi80 = M_PI / 180;
                            $lat1 *= $pi80;
                            $lon1 *= $pi80;
                            $lat2 *= $pi80;
                            $lon2 *= $pi80;
                        
                            $r = 6372.797; // mean radius of Earth in km
                            $dlat = $lat2 - $lat1;
                            $dlon = $lon2 - $lon1;
                            $a = sin($dlat / 2) * sin($dlat / 2) + cos($lat1) * cos($lat2) * sin($dlon / 2) * sin($dlon / 2);
                            $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
                            $km = $r * $c;
                        
                            //echo '<br/>'.$km;
                            return round($km,2);
                                            }
                            
                            
                            
                            
                            
                            
                            $sql  = "SELECT  *, ( 3959 * acos ( cos ( radians(".number_format($_REQUEST['latitude'], 2).") ) * cos( radians( BEGIN_LAT ) ) * cos( radians( BEGIN_LON ) - radians(".number_format($_REQUEST['longitude'], 2).") ) + sin ( radians('".number_format($_REQUEST['latitude'], 2)."') ) * sin( radians( BEGIN_LAT ) ) ) ) AS distance FROM storm_details where (`EVENT_TYPE`='HAIL' or `EVENT_TYPE`='Tornado')  and STATE='".strtoupper($_REQUEST['state'])."' and YEAR='".$_REQUEST['year']."' HAVING distance < ".$_REQUEST['distance']." ORDER BY distance"; 
                           $result = $conn->query($sql);
                           if ($result->num_rows > 0) {
                            
                            echo "var locations = ["; ?>
                                  ['Main Location : <?php echo strtoupper($_REQUEST['state']); ?>', <?php echo $_REQUEST['latitude']; ?>, <?php echo $_REQUEST['longitude']; ?>, 1 , "images/maincenter2.png"],
                        
                         <?php
                           while($row = $result->fetch_assoc()) {   
                               static $i=2;
                               if($row['EVENT_TYPE']=='Hail'){
                              	 ?>
                              ['Event : <?php echo $row['EVENT_TYPE']; ?> | Date & Time : <?php echo $row['BEGIN_DATE_TIME']; ?> | Magnitude : <?php echo $row['MAGNITUDE']; ?> | Location : <?php echo $row['STATE']; ?> | Year : <?php echo $row['YEAR']; ?> | Month : <?php echo $row['MONTH_NAME']; ?>', <?php echo $row['BEGIN_LAT']; ?>, <?php echo $row['BEGIN_LON']; ?>, <?php echo $i++; ?> , "img.php?deep=<?php echo $row['MAGNITUDE'];?>"],
                             	 <?php }else if ($row['EVENT_TYPE']=='Tornado') { ?>
                              ['Event : <?php echo $row['EVENT_TYPE']; ?>  Date & Time : <?php echo $row['BEGIN_DATE_TIME']; ?> | Location : <?php echo $row['STATE']; ?> | Year : <?php echo $row['YEAR']; ?> | Month : <?php echo $row['MONTH_NAME']; ?>', <?php echo $row['BEGIN_LAT']; ?>, <?php echo $row['BEGIN_LON']; ?>, <?php echo $i++; ?> , "images/tornado.png"],
                              <?php } else { ?>
                               ['Event : <?php echo $row['EVENT_TYPE']; ?>  Date & Time : <?php echo $row['BEGIN_DATE_TIME']; ?> | Location : <?php echo $row['STATE']; ?> | Year : <?php echo $row['YEAR']; ?> | Month : <?php echo $row['MONTH_NAME']; ?>', <?php echo $row['BEGIN_LAT']; ?>, <?php echo $row['BEGIN_LON']; ?>, <?php echo $i++; ?> , "images/wind.png"],
                              <?php }
                           }
                           echo " ];";
                           } else {
                           echo "0 results";
                           }
                           $distance=milesToMeters($_REQUEST['distance']);
                           ?>
                            
                        //http://www.stormersite.com/markers/wind.png
                        //http://www.stormersite.com/markers/tornado.png
                        //http://www.stormersite.com/markers/100.png
                            var map = new google.maps.Map(document.getElementById('map'), {
                                <?php
                                $d=$_REQUEST['distance'];
                                 switch(true) {
                                   case in_array($d, range(0,15)): //the range from range of 0-15
                                       echo "zoom: 10.5,";
                                   break;
                                   case in_array($d, range(15,30)): //the range from range of 15-30
                                       echo "zoom: 9.5,";
                                   break;
                                   case in_array($d, range(30,50)): //range of 30-50
                                      echo "zoom: 8.5,";
                                   break;
                                            }
                                            ?>
                              center: new google.maps.LatLng(<?php echo $_REQUEST['latitude']; ?>, <?php echo $_REQUEST['longitude']; ?>),
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
                                  radius: <?php echo $distance; ?>,    // 7.5 miles in metres
                                  fillColor: '#FFFF33'
                                });
                                circle.bindTo('center', marker, 'position');
                             }
                              google.maps.event.addListener(marker, 'click', (function(marker, i) {
                                return function() {
                                  infowindow.setContent(locations[i][0]);
                                  infowindow.open(map, marker);
                                }
                              })(marker, i));
                            }
                          /*  
                        var marker2 = new google.maps.Marker({
                          map: map,
                          position: new google.maps.LatLng(<?php echo $_REQUEST['latitude']; ?>, <?php echo $_REQUEST['longitude']; ?>),
                          MarkerLabel: 'asasas',
                          icon: "http://www.stormersite.com/markers/city.png",
                          
                        });
                        
                        
                        
                             var circle = new google.maps.Circle({
                                  map: map,
                                  radius: 12070.1,    // 7.5 miles in metres
                                  fillColor: '#AA0000'
                                });
                                circle.bindTo('center', marker, 'position');*/
                          
                     </script>
                  </div>
                  <?php if(!empty( $_REQUEST['address'])) { ?>
                  <h4>Storm Report for : <?php echo $_REQUEST['address']; ?></h4>
                  <?php } ?>
                  <table class="table table-striped" width="100%" cellpadding="0" cellspacing="5" align="center">
                      
                      
                     <tbody>
                       <?php
                            $result='';
                            $sql='';
                             $sql  = "SELECT  *, ( 3959 * acos ( cos ( radians(".number_format($_REQUEST['latitude'], 2).") ) * cos( radians( BEGIN_LAT ) ) * cos( radians( BEGIN_LON ) - radians(".number_format($_REQUEST['longitude'], 2).") ) + sin ( radians('".number_format($_REQUEST['latitude'], 2)."') ) * sin( radians( BEGIN_LAT ) ) ) ) AS distance FROM storm_details where (`EVENT_TYPE`='HAIL')  and STATE='".strtoupper($_REQUEST['state'])."' and YEAR='".$_REQUEST['year']."' HAVING distance < ".$_REQUEST['distance']." ORDER BY distance"; 
                            $result = $conn->query($sql);
                            $result->num_rows;
                           if ($result->num_rows > 0) { ?>
                        <tr>
                           <th colspan="10">Hail Reports (<a href="#">CSV</a>)&nbsp;(<a href="#">Raw Hail CSV</a>)(<a href="#">?</a>)</th>
                        </tr>
                        
                        <tr>
                           <th>Time</th>
                           <th> Size </th>
                           <th>Location</th>
                            <th>Date & Time</th>
                           <th>County</th>
                           <th>State</th>
                           <th>Lat</th>
                           <th>Lon</th>
                             <th>Distance from <br>input address (in miles)</th>
                           <th>Comments</th>
                        </tr>
                         <?php
                           while($row = $result->fetch_assoc()) {    ?>
                        <tr>
                           <td class="rpttext"><?php echo $row['BEGIN_TIME']; ?></td>
                           <td class="rpttext"><?php echo $row['MAGNITUDE']; ?></td>
                           <td class="rpttext"> <?php echo $row['BEGIN_LOCATION']; ?></td>
                           <td class="rpttext"><?php echo $row['BEGIN_DATE_TIME']; ?></td>
                           <td class="rpttext"><?php
                           if($row['CZ_NAME']!=$row['STATE']) 
                           {
                           echo $row['CZ_NAME'];
                           }else{
                           echo 'NA';
                           } 
                           ?></td>
                           
                           <td class="rpttext"><?php echo $row['STATE']; ?></td>
                           <td class="rpttext"><?php echo $row['BEGIN_LAT']; ?></td>
                           <td class="rpttext"><?php echo $row['BEGIN_LON']; ?></td>
                           <td class="rpttext" style="text-align:center"><?php echo KmToMiles(distance($_REQUEST['latitude'], $_REQUEST['longitude'], $row['BEGIN_LAT'], $row['BEGIN_LON'])); ?></td>
                           <td class="rpttext"><p> <?php echo $row['EPISODE_NARRATIVE']; ?> </p>                                                                                                                                           <a href="#">(TOP)</a></td>
                        </tr>
                       <?php 
                           }
                       } 
                       
                       ?>
                       
                       <?php
                            $result='';
                            $sql='';
                          $sql  = "SELECT  *, ( 3959 * acos ( cos ( radians(".number_format($_REQUEST['latitude'], 2).") ) * cos( radians( BEGIN_LAT ) ) * cos( radians( BEGIN_LON ) - radians(".number_format($_REQUEST['longitude'], 2).") ) + sin ( radians('".number_format($_REQUEST['latitude'], 2)."') ) * sin( radians( BEGIN_LAT ) ) ) ) AS distance FROM storm_details where (`EVENT_TYPE`='Tornado')  and STATE='".strtoupper($_REQUEST['state'])."' and YEAR='".$_REQUEST['year']."' HAVING distance < ".$_REQUEST['distance']." ORDER BY distance"; 
                              $result = $conn->query($sql);
                            $result->num_rows;
                           if ($result->num_rows > 0) { ?>
                        <tr>
                           <th colspan="10">Tornado Reports (<a href="#">CSV</a>)&nbsp;(<a href="#">Raw Tornado Reports CSV</a>)(<a href="#">?</a>)</th>
                        </tr>
                        <tr>
                           <th>Time</th>
                           <th> Speed </th>
                           <th>Location</th>
                            <th>Date & Time</th>
                           <th>County</th>
                           <th>State</th>
                           <th>Lat</th>
                           <th>Lon</th>
                            <th>Distance from <br>input address (in miles)</th>
                           <th>Comments</th>
                        </tr>
                         <?php
                           while($row = $result->fetch_assoc()) {    ?>
                        <tr>
                           <td class="rpttext"><?php echo $row['BEGIN_TIME']; ?></td>
                           <td class="rpttext"><?php echo $row['MAGNITUDE']; ?></td>
                           <td class="rpttext"> <?php echo $row['BEGIN_LOCATION']; ?></td>
                           <td class="rpttext"><?php echo $row['BEGIN_DATE_TIME']; ?></td>
                           <td class="rpttext"><?php
                           if($row['CZ_NAME']!=$row['STATE']) 
                           {
                           echo $row['CZ_NAME'];
                           }else{
                           echo 'NA';
                           } 
                           ?></td>
                           <td class="rpttext"><?php echo $row['STATE']; ?></td>
                           <td class="rpttext"><?php echo $row['BEGIN_LAT'] ? $row['BEGIN_LAT'] : 'NA'; ?></td>
                           <td class="rpttext"><?php echo $row['BEGIN_LON'] ? $row['BEGIN_LAT'] : 'NA'; ?></td>
                           <td class="rpttext" style="text-align:center"><?php echo KmToMiles(distance($_REQUEST['latitude'], $_REQUEST['longitude'], $row['BEGIN_LAT'], $row['BEGIN_LON'])); ?></td>
                           <td class="rpttext"><p> <?php echo $row['EPISODE_NARRATIVE']; ?> </p>                                                                                                                                           <a href="#">(TOP)</a></td>
                        </tr>
                       <?php 
                           }
                       } 
                       ?>
                       
                        <?php
                            $result='';
                            $sql='';
                            $sql  = "SELECT  * FROM storm_details where (`EVENT_TYPE`='High Wind')  and STATE='".strtoupper($_REQUEST['state'])."' and YEAR='".$_REQUEST['year']."' "; 
                            $result = $conn->query($sql);
                            $result->num_rows;
                           if ($result->num_rows > 0) { ?>
                        <tr>
                           <th colspan="10">Wind Reports (<a href="#">CSV</a>)&nbsp;(<a href="#">Raw Wind CSV</a>)(<a href="#">?</a>)</th>
                        </tr>
                        <tr>
                           <th>Time</th>
                           <th> Speed </th>
                           <th>Location</th>
                            <th>Date & Time</th>
                           <th>County</th>
                           <th>State</th>
                           <th>Lat</th>
                           <th>Lon</th>
                          
                           <th colspan="2">Comments</th>
                        </tr>
                         <?php
                           while($row = $result->fetch_assoc()) {    ?>
                        <tr>
                           <td class="rpttext"><?php echo $row['BEGIN_TIME']; ?></td>
                           <td class="rpttext"><?php echo $row['MAGNITUDE']; ?></td>
                           <td class="rpttext"> <?php echo $row['BEGIN_LOCATION']; ?></td>
                           <td class="rpttext"><?php echo $row['BEGIN_DATE_TIME']; ?></td>
                           <td class="rpttext"><?php
                           if($row['CZ_NAME']!=$row['STATE']) 
                           {
                           echo $row['CZ_NAME'];
                           }else{
                           echo 'NA';
                           } 
                           ?></td>
                           <td class="rpttext"><?php echo $row['STATE']; ?></td>
                           <td class="rpttext"><?php echo $row['BEGIN_LAT'] ? $row['BEGIN_LAT'] : 'NA'; ?></td>
                           <td class="rpttext"><?php echo $row['BEGIN_LON'] ? $row['BEGIN_LAT'] : 'NA'; ?></td>
                           
                           <td colspan="2" class="rpttext"><p> <?php echo $row['EPISODE_NARRATIVE']; ?> </p>                                                                                                                                           <a href="#">(TOP)</a></td>
                        </tr>
                       <?php 
                           }
                       } 
                       ?>
                       
                       
                       
                       
                       
                     </tbody>
                  </table>
               </div>
            </div>
         </div>
      </div>
       <?php 
               $sql = "SELECT date_format(str_to_date(END_YEARMONTH, '%Y%m'), '%M, %Y') as mdate FROM storm_details order by END_YEARMONTH desc LIMIT 0,1"; 
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
           $row  = $result->fetch_assoc()   ;
            }
         ?>
       <footer>
                <div class="ctext text-center">Last updated on: <?php echo $row ['mdate']; ?></div>
            </footer>
   </body>
</html>
<?php
                       $conn->close();
                       ?>