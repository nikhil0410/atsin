<?php
require_once('vendor/php-excel-reader/excel_reader2.php');
require_once('vendor/SpreadsheetReader.php');

if (isset($_POST["import"]))
{
    
    
  $allowedFileType = ['application/vnd.ms-excel','text/xls','text/xlsx','application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];
  
  if(in_array($_FILES["file"]["type"],$allowedFileType)){

        $targetPath = 'uploads/'.$_FILES['file']['name'];
        move_uploaded_file($_FILES['file']['tmp_name'], $targetPath);
        
        $Reader = new SpreadsheetReader($targetPath);
        $saving_data = array(
            'loginId'=>'HYDENTRY',
            'password'=>'PASSWORD',
            'orgId'=>'1',
            'oprId'=>'49',
            'lotId'=>'492017111332',
            'sampleId'=>'1'
        );
        $saving_data = http_build_query($saving_data,'','&');
        
        $sheetCount = count($Reader->sheets());
        $saving_data .= '&dtlList:[';
        for($i=0;$i<$sheetCount;$i++)
        {
            // [{srNo:1,paramId:2016120011490000128,qtyUomId:2016040220980000001,prodId:2016040291220000007,type:N,nValue:5,maxVal:5.00,minVal:0.00}]

            $Reader->ChangeSheet($i);
            print(count($Reader));
            foreach ($Reader as $Row)
            {
                // for Moisture
          
                $name = "";
                $sr_no = 0;
                if(isset($Row[8])) {
                    if(strtoupper($Row[2]) == 'MUSTARDS'){
                        // $name = mysqli_real_escape_string($conn,$Row[0]);
                        if($Row['8'] != 'Moisture %'){
                            $saving_data .= '{"srNo":'.$sr_no.',"paramId":"2016070011490000073","qtyUomId":"2016070011220000103","prodId":"2016070011220000103","type":"N","nValue":"'.$Row[8].'","maxVal":"7.50","minVal":"0.00"},';
                        }elseif($Row['8'] != 'Oil As is %'){
                            $saving_data .= '{"srNo":'.$sr_no.',"paramId":"2016070011490000093","qtyUomId":"2016070011220000103","prodId":"2016070011220000103","type":"N","nValue":"'.$Row[8].'","maxVal":"100.00","minVal":"36.00"},';
                        }
                    }
                    elseif (strtolower($Row[2]) == 'soybeans') {
                        if($Row['8'] != 'Moisture %'){
                            $saving_data .= '{"srNo":'.$sr_no.',"paramId":"2016110011490000067","qtyUomId":"2016070011220000103","prodId":"2016080011220000021","type":"N","nValue":"'.$Row[8].'","maxVal":"12.00","minVal":"0.00"},';
                        }elseif($Row['8'] != 'Oil As is %'){
                            $saving_data .= '{"srNo":'.$sr_no.',"paramId":"2016110011490000073","qtyUomId":"2016070011220000103","prodId":"2016080011220000021","type":"N","nValue":"'.$Row[8].'","maxVal":"50.00","minVal":"15.00"},';
                        }
                    }
                }
                $sr_no +=1;
             }
        
         }
         $saving_data .= ']';

                print($saving_data);
                echo 'saving data \n';
         $make_call = callAPI('POST', 'http://train.enam.gov.in/NamWebSrv/rest/assaying/getAssayingParamDtl', $saving_data);
        // $response = json_decode($make_call, true);
        echo 'Nikhil';
        echo '<pre>';
        // print_r($response);
        die;
  }
  else
  { 
        $type = "error";
        $message = "Invalid File Type. Upload Excel File.";
  }
}

function callAPI($method, $url, $data){
   $curl = curl_init();

   switch ($method){
      case "POST":
         curl_setopt($curl, CURLOPT_POST, 1);
         if ($data)
            curl_setopt($curl, CURLOPT_POSTFIELDS, "prodId=2016070011220000103&orgId=1");
         break;
      case "PUT":
         curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
         if ($data)
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);                              
         break;
      default:
         if ($data)
            $url = sprintf("%s?%s", $url, http_build_query($data));
   }

   // OPTIONS:
   curl_setopt($curl, CURLOPT_URL, $url);
   curl_setopt($curl, CURLOPT_HTTPHEADER, array(
      'Content-Type: application/x-www-form-urlencoded',
   ));
   // curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
   // curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

   // EXECUTE:
   $result = curl_exec($curl);
   if(!$result){die("Connection Failure");}
   curl_close($curl);
   return $result;
}

$data_array =  array(
     'prodId'=>'2016070011220000103',
     'orgId'=>'1'
);
$sr=1;
$data_array = http_build_query($data_array,'','&');
$data_array .= '[{srNo'.$sr.'';


// $make_call = callAPI('POST', 'http://train.enam.gov.in/NamWebSrv/rest/assaying/getAssayingParamDtl', $data_array);
// $response = json_decode($make_call, true);
// echo '<pre>';
// print_r($response);die;
?>

<!DOCTYPE html>
<html>    
<head>
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

div#response.display-block {
    display: block;
}
</style>
</head>

<body>
    <h2>Import Excel File into ENAM.GOV.IN</h2>
    
    <div class="outer-container">
        <form action="" method="post"
            name="frmExcelImport" id="frmExcelImport" enctype="multipart/form-data">
            <div>
                <label>Choose Excel
                    File</label> <input type="file" name="file"
                    id="file" accept=".xls,.xlsx">
                <button type="submit" id="submit" name="import"
                    class="btn-submit">Import</button>
        
            </div>
        
        </form>
        <button onclick="test()">request data</button>
        
    </div>
    <div id="response" class="<?php if(!empty($type)) { echo $type . " display-block"; } ?>"><?php if(!empty($message)) { echo $message; } ?></div>
    


</body>
<script type="text/javascript">


function test(){
var url = 'http://train.enam.gov.in/NamWebSrv/rest/assaying/getAssayingParamDtl';
var data = {
        prodId: '2016070011220000103',
        orgId: '1'
    };
const toUrlEncoded = obj => Object.keys(obj).map(k => encodeURIComponent(k) + '=' + encodeURIComponent(obj[k])).join('&');

fetch(url, {
  method: 'POST', // or 'PUT'
  body: toUrlEncoded(data), // data can be `string` or {object}!
  headers:{
    'Content-Type': 'application/x-www-form-urlencoded'
  }
}).then(res => res.json())
.then(response => console.log('Success:', JSON.stringify(response)))
.catch(error => console.error('Error:', error));
}





</script>
</html>