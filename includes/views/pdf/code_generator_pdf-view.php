
<html>
<body>
    <style>
    .infocontainernopadding p {width: 580px !important;}
    .infocontainernopadding img {width: 570px !important;}
    </style>
    <?php $count=0;
    foreach($code_data as $row){?>
    <?php if($count!=0){?>
    <div style="page-break-after: always;">&nbsp;</div>
    <?php }?>
    <table style="margin-bottom: 20px;" class="data-table-header" cellpadding="0" cellspacing="0" border="0" width="640" align="center"  >
        <tr valign="center">
            <td  style="font-size: 30px;float: left;padding:40px;"><?php echo $row['name'];?></td>
        </tr>
    </table>
    <table   cellspacing="0" cellpadding="0" align="center" class="infocontainernopadding"  width="640" >
          <tr>
              <td style="padding:20px; text-overflow"><?php echo $row['description'];?></td>
          </tr>
    </table>
    <?php $count++; }?>
</body>
</html>
