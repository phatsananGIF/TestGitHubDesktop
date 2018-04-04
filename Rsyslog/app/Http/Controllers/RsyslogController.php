<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use File;
use App\im_cus_radius;
use DB;
use App\Http\Requests\RsyslogRequest;

class RsyslogController extends Controller
{
    
    public function index(){

        $lengthselect = null;
        $namefileselect = null;
        $arrayMerge = [];
        $dir = config('ima.log_path');
        
        //เช็คค่าที่ f.viewfile ส่งมา
        if(session()->has('arrayMerge')){
            $arrayMerge = session('arrayMerge');
        }
        if(session()->has('namefile')){
            $namefileselect = session('namefile');
            $lengthselect = session('length');
        }
        

        
        //อ่านไฟล์ในโฟเดอ//
        $contents = File::allFiles($dir, $hidden = false);

        foreach ($contents as $infofile) {
            //เช็คนามสกุลไฟล์ เอาแค่ไฟล์ log
            if(pathinfo($infofile,PATHINFO_EXTENSION)=="log"){
                $file = pathinfo($infofile,PATHINFO_BASENAME);
                $files[$file] = $file.' ('.date( "d/m/Y H:i:s", filemtime($dir.$file)).')';
            } 
        }



        
         /*
        echo '<pre>';
        print_r($files);
        echo  '</pre>';
        */

        $length = config('ima.length_row');

        
        return view('rsyslog.index',['contents'=>$files, 'length'=>$length, 'lengthselect'=>$lengthselect, 'namefileselect'=>$namefileselect, 'arrayMerge'=>$arrayMerge]);

        

    }//f.index


    public function viewfile(RsyslogRequest $request){
        $dir = config('ima.log_path');
        $namefile = $request->namefile;
        $length = $request->length;

        //อ่านไฟล์จากล้างขึ้นบน
        $file = fopen($dir.$namefile, 'r');
        $line = '';
        $countlines = 1;
        $cursor = 0;

        fseek($file, $cursor--, SEEK_END);
        $char = fgetc($file);

        while ($countlines <= $length) {

            if( $char !== false && $char !== "\n" && $char !== "\r"){
                $line = $char . $line;

            }else if($char == "\n" && $line!=""){
                $arrline = explode(" ",$line,4);

                //เช็ครูปแบบวันที่
                $myDate = $arrline[0];
                if (date_create($myDate) !== FALSE) {
                    $date=date_create($myDate);
                    $arrline[0]=date_format($date,"Y/m/d H:i:s");
                }
                $row[] = $arrline;
                $line = '';
                $countlines++;
            }
            
            $pointer= fseek($file, $cursor--, SEEK_END);
            $char = fgetc($file);

            if($pointer=='-1'){
                if($line!=""){
                    $arrline = explode(" ",$line,4);

                    //เช็ครูปแบบวันที่
                    $myDate = $arrline[0];
                    if (date_create($myDate) !== FALSE) {
                        $date=date_create($myDate);
                        $arrline[0]=date_format($date,"Y/m/d H:i:s");
                    }
                    $row[] = $arrline;
                }
                break;
            }
        }
        fclose($file);



        //query ข้อมูลลูกค้าออกมา เอาไว้เทียบ ip กับข้อมูลในไฟล์
        $results = DB::select('SELECT im_cus_radius.id as id_radius, im_cus_radius.cus_code as cid,
                im_customer.name as name_customer, im_cusgroup.name as name_cusgroup,
                im_cus_radius.rad_ipaddress
                FROM im_cus_radius
                LEFT JOIN im_customer ON im_cus_radius.customer_id = im_customer.id
                LEFT JOIN im_cusgroup ON im_customer.cusgroup_id = im_cusgroup.id');

        $results = json_decode(json_encode($results), True);


        //วนลูปหา customer
        $arrayMerge=null;
        foreach( $row as $valfile){
            if(count($valfile)>=2){
                $Merge ="";
                foreach( $results as $resultDB){
                    if( $valfile[1] == $resultDB['rad_ipaddress']){

                        $Merge = array_merge($valfile, $resultDB);     

                        break;
                    }
                }

                if( isset($Merge['name_cusgroup']) ){
                    $arrayMerge[] = $Merge;
                }else{
                    $Merge = array_merge($valfile, ['name_cusgroup'=>'-', 'cid'=>'-']); 
                    $arrayMerge[] = $Merge;
                }


            }
        }


       //return view('rsyslog.viewfile',['namefile'=>$namefile, 'arrayMerge'=>$arrayMerge]);

       return redirect()->action('RsyslogController@index')->with( ['arrayMerge' => $arrayMerge, 'namefile' => $namefile,'length' => $length] );//รีหน้าแ้วส่งข้อมูลไปด้วย


    }//f.viewfile

    public function downloadfile($namefile){
        $dir = config('ima.log_path');
        return response()->download($dir.$namefile);
    }//f.download

    public function deletefile($namefile){
        $dir = config('ima.log_path');
        //ลบไฟล์ 
        File::delete($dir.$namefile);
        return redirect()->action('RsyslogController@index');
    }//f.deletefile




    /*
    public function show(Request  $request){
        $namefile = $request->namefile;
        return $namefile;
    }//f.show
    


    public function testread(){

        
        //อ่านไฟล์
        $strFileName = 'rsyslog/log/'.$namefile;
        $objFopen = fopen($strFileName, 'r');

        if ($objFopen) {
            $n=1;
            while ( !feof($objFopen) ) {
                if($n > $length){
                    break;
                }

                    $line = trim(fgets($objFopen));
                    $row[] = explode(" ",$line,4);

                $n++;
                
            }
            fclose($objFopen);
        }
        

    }//f.testread
    */


    
}
