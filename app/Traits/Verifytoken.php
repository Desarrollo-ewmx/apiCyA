<?php
/*
*   01.02.2021
*   my traits.php
*/
namespace App\Traits;

trait Verifytoken{



    public function verifica($token){
        $tokenin="3RRZ4Czrz9KDMMG5Xo3IzaCU5WV7ZluKDYhNiw9lNZvUdRgFDnNUePyByJF8LVgIXPEE5gzJgQrzqa5RFaPu69oK893wNFWpY6xEoVLtzmNH3seFecjKBCHrjJXkTFo0DjDrR13NKF1R4uTxhxDnSw";
if ($tokenin==$token) {
    return 1;

}else{
    return 0;
}

  }









}

