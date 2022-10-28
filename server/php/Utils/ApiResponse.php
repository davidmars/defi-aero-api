<?php


namespace Utils;

use JsonSerializable;

/**
 * Une classe de base pour gérer les réponses d'API
 *
 * @package Pov\System
 * @author David Marsalone
 */
class ApiResponse implements JsonSerializable  {
    /**
     * @var float Durée d'exécution
     */
    public $execTime=0.0;
    /**
     * @var bool Sera false si il y a eu des erreurs
     */
    public $success=true;
    /**
     * @var array Des éventuels messages
     */
    public $messages=[];
    /**
     * @var array Les éventuels messages d'erreur
     */
    public $errors=[];

    /**
     * Moment où la requête a été faite
     * @var \DateTime
     */
    public $when;

    private $_usePayload=false;
    /**
     * Soit $_REQUEST soit le payload
     * @var array
     */
    public $requestData=[];

    public function __construct(){
        $this->when=new \DateTime();
        $this->requestData=$_REQUEST;
        if(!the()->apiResponse){
            the()->apiResponse=$this;
        }
    }

    //----------------------errors--------------------------------

    /**
     * Y a il des erreurs
     * @return bool
     */
    public function hasErrors(){
        if(count($this->errors)>0){
            return true;
        }
        return false;
    }
    /**
     * Ajoute un message d'erreur et place succes sur false
     * @param string $message
     */
    public function addError($message){
        $this->errors[]=$message;
        $this->errors=array_unique($this->errors);
        $this->success=false;
    }

    //-----------------messages--------------------------------

    /**
     * Ajoute un message
     * @param string $message The message to add
     */
    public function addMessage($message){
        $this->messages[]=$message;
        $this->messages=array_unique($this->messages);
    }

    /**
     * @var mixed L'objet de réponse
     */
    public $body=[];

    /**
     * Quand défini sur true dit que la source de données n'est plus REQUEST mais le fileinput json
     * Ce mode est à utiliser avec les requêtes POST de axios par exemple
     * @param bool $use
     */
    public function usePayload($use=true){
        $this->_usePayload=$use;
        $request_body = file_get_contents('php://input');
        if($request_body){
            $this->requestData= utils()->array->fromObject(json_decode($request_body));
        }
    }

    /**
     * Test if the given parameters are set in the $_REQUEST. If not add errors to the object.
     * @param array $requestParameters Coma separated $_REQUEST parameters to test. Something like "myfield1,myfield2,etc"
     */
    public function testMandatoryRequest($requestParameters){
        foreach($requestParameters as $p){
            if(!isset($this->requestData[$p])){
                $this->addError("The \$_REQUEST[$p] parameter is mandatory");
            }
        }
    }

    /**
     * Renvoie un paramètre que l'on peut trouver dans $_REQUEST
     * @param string $queryParam Le paramètre Get ou Post
     * @param mixed   $ifNull La valeur à renvoyer si null ou indéfini
     * @return string|array|null
     */
    public function getRequest($queryParam,$ifNull=null)
    {
        if(isset($this->requestData[$queryParam])){
            if(is_array($ifNull)){
                $checked = $_REQUEST[$queryParam];
                $ret=[];
                for($i=0; $i < count($checked); $i++){
                    $ret[]=$checked[$i];
                }
                return $ret;
            }
            return $this->requestData[$queryParam];
        }
        return $ifNull;
    }

    /**
     * Test if the given parameter is set and returns it.
     * If the provided parameter doesn't exist add an error to the object
     * @param string $requestParameter the $_REQUEST[something] to test and get
     * @param string $errorMessageIfNotSet error message to push if not set. If not defined a deffault error message will be displayed.
     * @param bool $errorIfEmpty set it to true to display error on empty fields
     * @return null|mixed
     */
    public function testAndGetRequest($requestParameter,$errorMessageIfNotSet=null,$errorIfEmpty=false){

        $isDefined=isset($this->requestData[$requestParameter]);
        $isEmpty=false;
        if($isDefined){
            if(is_string($this->requestData[$requestParameter]) && trim($this->requestData[$requestParameter])==""){
                $isEmpty=true;
            }
        }
        if(!$isDefined || ($isEmpty && $errorIfEmpty)){
            if($errorMessageIfNotSet){
                $this->addError($errorMessageIfNotSet);
            }else{
                $this->addError("The \$_REQUEST[$requestParameter] parameter is mandatory");
            }
            return null;
        }else{
            return $this->requestData[$requestParameter];
        }
    }

    /**
     * Test if the given parameter is set and returns it.
     * If the provided parameter doesn't exist add an error to the object
     * @param array $array The array to look into...
     * @param string $varName the variable to test and get
     * @param string $errorMessageIfNotSet error message to push if not set. If not defined a deffault error message will be displayed.
     * @param bool $errorIfEmpty set it to true to display error on empty fields
     * @return string|null
     */
    public function testAndGetInArray($array,$varName,$errorMessageIfNotSet=null,$errorIfEmpty=false){
        $isDefined=isset($array[$varName]);
        $isEmpty=false;
        if($isDefined){
            if(is_string($array[$varName]) && trim($array[$varName])==""){
                $isEmpty=true;
            }
        }
        if(!$isDefined || ($isEmpty && $errorIfEmpty)){
            if($errorMessageIfNotSet){
                $this->addError($errorMessageIfNotSet);
            }else{
                $this->addError("The SomethingArray[$varName] parameter is mandatory");
            }
            return null;
        }else{
            return $array[$varName];
        }
    }


    /**
     * Test the given parameter and returns it.
     * If the provided parameter doesn't exist add an error to the object
     * @param array $array The array to look into...
     * @param string $varName the variable to test and get
     * @return string|null
     */
    public function getInArray($array,$varName){
        $isDefined=isset($array[$varName]);
        if($isDefined){
            return $array[$varName];
        }else{
            return null;
        }
    }


    /**
     * Add stuff to the body response
     * @param string $variableName
     * @param mixed $value
     * @return ApiResponse The self object
     */
    public function addToBody($variableName, $value){
        $this->body[$variableName]=$value;
        return $this;
    }

    public function jsonSerialize()
    {
        $this->execTime=the()->time();
        return $this;
    }


}