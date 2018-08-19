<?php
namespace common\components;
 
use Yii;
use yii\base\Component;
use yii\httpclient\Client;  
use yii\httpclient\Request;  
use yii\httpclient\RequestEvent;  
 
class ResultClient extends Component {
    public $protocol;
    public $host;
    public $port;
    public $Authorization;
    public $ssl_verify;
    public $rootPath;
    public $httpclient;
    public $request;

    public function createRequest($path,$method = 'get') {
        $this->rootPath = $this->protocol.'://'.$this->host.':'.$this->port;
        $this->httpclient = new Client([
            'baseUrl' => $this->rootPath,
            'transport' => 'yii\httpclient\CurlTransport',
        ]);
        $this->request = $this->httpclient->createRequest();
        if(isset($this->Authorization)){
            $this->request->addHeaders(['Authorization' => $this->Authorization]);
        }
        if($this->ssl_verify==false && $this->protocol == 'https'){
            $this->request->addOptions([
                'sslVerifyPeer' => false,
            ]);
        }
        $this->request
             ->setMethod($method)
             ->setUrl($path);
        return $this->request;
    }

    public function get($path)
    {
        $request = $this->createRequest($path);
        return $request->send();
    }

    public function post($path,$postdata,$files = [])
    {
        $request = $this->createRequest($path,'post');
        $request->addHeaders(['Content-Type' => 'application/json']);
        if(count($files)){
            foreach ($files as $file) {
                $request->addFile('file',$file['tmp_name'],[
                    'fileName' => $file['name'],
                    'mimeType' => $file['type']
                ]);
            }
        }else{
            $request->setContent($postdata);
        }
        return $request->send();
    }

    public function put($path,$putdata)
    {
        $request = $this->createRequest($path,'put');
        $request->addHeaders(['Content-Type' => 'application/json']);
        $request->setContent($putdata);
        return $request->send();
    }

    public function delete($path)
    {
        $request = $this->createRequest($path,'delete');
        return $request->send();
    }

}