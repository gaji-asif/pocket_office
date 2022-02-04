<?php 

function getNavCount($nav_id)
{
  if($nav_id!='')
  {
    $sql = "select query from navigation where navigation_id='".$nav_id."' limit 1";
    $res = DBUtil::query($sql);
    list($query)=mysqli_fetch_row($res);
    if($query!='')
    {
      $hooks = array("[>USERID<]", "[>LEVEL<]", "[>ACCOUNT<]");
      $values = array($_SESSION['ao_userid'], $_SESSION['ao_level'], $_SESSION['ao_accountid']);
      $query = str_replace($hooks, $values, $query);
      eval("\$query = \"$query\";");  
      
      $res = DBUtil::query($query);
      $count = mysqli_num_rows($res); 
      echo " <span style='font-size: 10px;'>(".$count.")</span>";
    }
  }
}

?>