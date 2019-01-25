
<?php
// Turn off all error reporting
error_reporting(0);
?>
<!DOCTYPE html>
<html>    
<head>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
    
        <style>    
        body {
        	font-family: Arial;
        	width: 550px;
        }


        .outer-container {
        	background: #F0F0F0;
        	border: #e0dfdf 1px solid;
        	padding: 40px 20px;
        	border-radius: 2px;
        }

        .btn-submit {
        	background: #333;
        	border: #1d1d1d 1px solid;
            border-radius: 2px;
        	color: #f0f0f0;
        	cursor: pointer;
            padding: 5px 20px;
            font-size:0.9em;
        }

        .tutorial-table {
            margin-top: 40px;
            font-size: 0.8em;
        	border-collapse: collapse;
        	width: 100%;
        }

        .tutorial-table th {
            background: #f0f0f0;
            border-bottom: 1px solid #dddddd;
        	padding: 8px;
        	text-align: left;
        }

        .tutorial-table td {
            background: #FFF;
        	border-bottom: 1px solid #dddddd;
        	padding: 8px;
        	text-align: left;
        }

        #response {
            padding: 10px;
            margin-top: 10px;
            border-radius: 2px;
            display:none;
        }

        .success {
            background: #c7efd9;
            border: #bbe2cd 1px solid;
        }

        .error {
            background: #fbcfcf;
            border: #f3c6c7 1px solid;
        }

        .tg  {border-collapse:collapse;border-spacing:0;border-color:#999;margin:0px auto;}
        .tg td{font-family:Arial, sans-serif;font-size:14px;padding:4px 19px;border-style:solid;border-width:1px;overflow:hidden;word-break:normal;border-color:#999;color:#444;background-color:#F7FDFA;}
        .tg th{font-family:Arial, sans-serif;font-size:14px;font-weight:normal;padding:4px 19px;border-style:solid;border-width:1px;overflow:hidden;word-break:normal;border-color:#999;color:#fff;background-color:#26ADE4;}
        .tg .tg-0lax{text-align:left;vertical-align:top}

        div#response.display-block {
            display: block;
        }
        </style>
</head>

<body>
<h2>Import Excel File into ENAM.GOV.IN</h2>
    
    <div class="outer-container">
        <form action="" method="post" name="frmExcelImport" id="frmExcelImport" enctype="multipart/form-data">
            <div>
                <label>Choose Excel File</label> <input type="file" name="file"id="file" accept=".xls,.xlsx">
                <button type="submit" name="import" class="btn-submit">Upload to ENAM</button>
        
            </div>
        
        </form>
        <!-- <button onclick="test()">request data</button> -->
        
    </div>
    <div id="response" class="<?php if(!empty($type)) { echo $type . " display-block"; } ?>"><?php if(!empty($message)) { echo $message; } ?></div>
    


<?php
require_once('vendor/php-excel-reader/excel_reader2.php');
require_once('vendor/SpreadsheetReader.php');

if (isset($_POST["import"]))
{?>

<table class="tg">
  <tr>
    <th class="tg-0lax">srNo</th>
    <th class="tg-0lax">Product</th>
    <th class="tg-0lax">Feature</th>
    <th class="tg-0lax">Value</th>
  </tr>

<?php
    
    
  $allowedFileType = ['application/vnd.ms-excel','text/xls','text/xlsx','application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];
  
  if(in_array($_FILES["file"]["type"],$allowedFileType)){

        $targetPath = 'uploads/'.$_FILES['file']['name'];
        move_uploaded_file($_FILES['file']['tmp_name'], $targetPath);
        
        $Reader = new SpreadsheetReader($targetPath);
        $saving_data = array(
            'loginId'=>'RJ2024N00001',
            'password'=>'PASSWORD',
            'orgId'=>'1',
            'oprId'=>'163',
            'lotId'=>'492017111332',
            'sampleId'=>'1'
        );
        $saving_data = http_build_query($saving_data,'','&');
        
        $sheetCount = count($Reader->sheets());
        $saving_data .= '&dtlList=[';
        $sr_no = 0;
        for($i=0;$i<$sheetCount;$i++)
        {
            // [{srNo:1,paramId:2016120011490000128,qtyUomId:2016040220980000001,prodId:2016040291220000007,type:N,nValue:5,maxVal:5.00,minVal:0.00}]

            
            $Reader->ChangeSheet($i);
            foreach ($Reader as $Row)
            {
                // for Moisture
          
                $name = "";
                if(isset($Row[8])) {
                    $product = strtoupper($Row[2]);

                    if(strtoupper($Row[2]) == 'MUSTARDS'){
                        // $name = mysqli_real_escape_string($conn,$Row[0]);
                            $saving_data .= '{"srNo":'.$sr_no.',"paramId":"2016070011490000073","qtyUomId":"2016070011220000103","prodId":"2016070011220000103","type":"N","nValue":"'.$Row[8].'","maxVal":"7.50","minVal":"0.00"},';
                        
                    }elseif (strtolower($Row[2]) == 'soybeans') {
                            $saving_data .= '{"srNo":'.$sr_no.',"paramId":"2016110011490000067","qtyUomId":"2016070011220000103","prodId":"2016080011220000021","type":"N","nValue":"'.$Row[8].'","maxVal":"12.00","minVal":"0.00"},';
                    }

                    ?>
                    <tr>
                        <td class="tg-0lax"><? echo $sr_no; ?></td>
                        <td class="tg-0lax"><? echo $product; ?></td>
                        <td class="tg-0lax"><? echo 'Moisture'; ?></td>
                        <td class="tg-0lax"><? echo $Row[8]; ?></td>
                    </tr>
                    <?php 
                    $sr_no +=1;
                }
                if(isset($Row[8])) {
                    $product = strtoupper($Row[2]);

                    if(strtoupper($Row[2]) == 'MUSTARDS'){
                            $saving_data .= '{"srNo":'.$sr_no.',"paramId":"2016070011490000093","qtyUomId":"2016070011220000103","prodId":"2016070011220000103","type":"N","nValue":"'.$Row[8].'","maxVal":"100.00","minVal":"36.00"},';
                        
                    }elseif (strtolower($Row[2]) == 'soybeans') {
                            $saving_data .= '{"srNo":'.$sr_no.',"paramId":"2016110011490000073","qtyUomId":"2016070011220000103","prodId":"2016080011220000021","type":"N","nValue":"'.$Row[8].'","maxVal":"50.00","minVal":"15.00"},';
                    }

                    ?>
                    <tr>
                        <td class="tg-0lax"><? echo $sr_no; ?></td>
                        <td class="tg-0lax"><? echo $product; ?></td>
                        <td class="tg-0lax"><? echo 'Oil As is %' ?></td>
                        <td class="tg-0lax"><? echo $Row[9]; ?></td>
                    </tr>
                    <?php 
                    $sr_no +=1;
                }
             }
         }
         $saving_data .= ']';

        $url = 'http://train.enam.gov.in/NamWebSrv/rest/assaying/submitAssayingDtl';
        $data = array('key1' => 'value1', 'key2' => 'value2');

        // use key 'http' even if you send the request to https://...
        $options = array(
            'http' => array(
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => $saving_data
                )
            );
        $context  = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        // $result = json_encode($result);
        $result = json_decode($result, true);
        if($result['statusMsg'] == 'S'){
            ?>
            <div class="alert alert-success">
                <strong>Success!</strong> Data is uploaded <a href="#" class="alert-link">Successfully</a>.
            </div>
            <?
        }else{
            ?>
            <div class="alert alert-danger">
                <strong>Failure!</strong> <a href="#" class="alert-link">Data was not uploaded</a>.
              </div>
              <?
        }
  }
  else
  { 
        $type = "error";
        $message = "Invalid File Type. Upload Excel File.";
  }
}


$data_array =  array(
     'prodId'=>'2016070011220000103',
     'orgId'=>'1'
);
$sr=1;
$data_array = http_build_query($data_array,'','&');
$data_array .= '[{srNo'.$sr.'';
?>
</table>
<button onclick="savedData()">Show Response</button>
<div id='response-data' style="display: none;">
    <?php print($saving_data); ?>
</div>
    


</body>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
<script type="text/javascript">
// document.getElementById('frmExcelImport').addEventListener('submit', test, false                     )
function savedData() {
  var x = document.getElementById("response-data");
  if (x.style.display === "none") {
    x.style.display = "block";
  } else {
    x.style.display = "none";
  }
}

function test(e){
    e.preventDefault();
    var url = 'http://train.enam.gov.in/NamWebSrv/rest/assaying/getSampleAssayingLots';
    var data = {
            loginId:'RJ2024N00001',
            password:'PASSWORD',
            orgId:'1',
            oprId:'163',
            fromDate:'2018-11-27',
            toDate:'2018-11-28'
        };
    const toUrlEncoded = obj => Object.keys(obj).map(k => encodeURIComponent(k) + '=' + encodeURIComponent(obj[k])).join('&');

    fetch(url, {
      method: 'POST', // or 'PUT'
      body: toUrlEncoded(data), // data can be `string` or {object}!
      headers:{
        'Content-Type': 'application/x-www-form-urlencoded'
      }
    }).then(res => res.json())
    .then(function(response){
        console.log('Success:', (response))
            var resp = (response);
            console.log(resp['statusMsg']);
            document.getElementById('frmExcelImport').submit();
    } 
        )
    .catch(error => console.error('Error:', error));
}

// function getData() {
//     fetch('')
// }





</script>
</html>