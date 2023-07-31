<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

use App\Models\VerifyLog;
use App\Models\User;

class VerifyController extends Controller
{
    const INVALID_USER = 'invalid_user';
    const INVALID_ISSUER = 'invalid_issuer';
    const INVALID_SIGNATURE = 'invalid_signature';
    const INVALID_JSON = 'invalid_json';
    const INVALID_FILE_SIZE = 'size_exceed_2MB';
    const INVALID_ISSUER_NAME = 'invalid_issuer_name';
    const VERIFIED = 'verified';
    const INPUT_FILE_NAME = 'oaFile';

    private string $issuer='';

    public function verify(Request $request){



        if($request->hasFile($this::INPUT_FILE_NAME)){
            if(!$this->verifyFile($request)) return response($this->createResponse($this::INVALID_FILE_SIZE),200);

            $file = $request->file($this::INPUT_FILE_NAME);
            $content = $file->get();
        } else {
            $content = $request->load; 
        }           

        if(!$this->verifyJSON($content)) return response($this->createResponse($this::INVALID_JSON),200);        

        $data = json_decode($content, TRUE);    
    
        //flattens to dot notation
        $flatData=Arr::dot($data['data']);
        
        if(!$this->verifyIssuerName($data)) return response($this->createResponse($this::INVALID_ISSUER_NAME),200);

        $this->issuer = $flatData['issuer.name'];

        //validate for invalid_user error
        if(!$this->verifyUser($data)) return response($this->createResponse($this::INVALID_USER),200);
        
        //validate for invalid_issuer
        if(!$this->verifyIssuer($data)) return response($this->createResponse($this::INVALID_ISSUER),200);
        
        //validate for invalid_signature
        if(!$this->verifySignature($data)) return response($this->createResponse($this::INVALID_SIGNATURE),200);
       
        return response($this->createResponse($this::VERIFIED),200);
    }

    public function verifyJSON($data): bool {
        return Str::isJson($data);        
    }

    public function verifyFile($request): bool {
        
        $validator = Validator::make($request->all(),[
            $this::INPUT_FILE_NAME => 'size:2048',
        ]);
        
        return !$validator->fails();
    }

    public function verifyIssuerName($data): bool {
        $validator = Validator::make($data,['data.issuer.name'=> 'required']);
        return !$validator->fails();
    }

    public function verifyUser($data): bool {
        
        $invalidUserRules = [
            'data.recipient.name' => 'required',
            'data.recipient.email' => 'required|email'
        ];

        $validator = Validator::make($data,$invalidUserRules);
        
        return !$validator->fails();            
        
    }

    public function verifyIssuer($data): bool {
        $flatData=Arr::dot($data['data']);

        $identityKey = $flatData['issuer.identityProof.key'];
        $identityHost = $flatData['issuer.identityProof.location'];

        $dnsTXTRecords = dns_get_record($identityHost,DNS_TXT); //return Array or False
        
        if($dnsTXTRecords) {
            foreach($dnsTXTRecords AS $dnsTXTRecord){
                $did = $dnsTXTRecord['txt'];
                $testResult = false;
                if(stripos($did,$identityKey)!==FALSE){
                    $testResult = true; 
                    break;
                }
            }           
        }
        return $testResult;
    }

    public function verifySignature($data): bool {
        $flatData=Arr::dot($data['data']);

        foreach($flatData AS $key => $value) {
            $dataHash[] = hash("sha256",'{"'.$key.'":"'.$value.'"}');
        }

        sort($dataHash);

        $signature = hash("sha256",'["'.implode('","',$dataHash).'"]');        

        return $data['signature']['targetHash']==$signature;
    }
    
    private function createResponse($result): array {
        $response = array(
            "data" => array(
                "issuer" => $this->issuer,
                "result" => $result
            )
        );
        $this->storeLogs($result); //need to move this to model
        return $response;
    }

    private function storeLogs($result): void {
        $log = new VerifyLog();
        $log->userid = 1;
        $log->result = $result;
        $log->created_at = time();
        $log->save();
    }
}