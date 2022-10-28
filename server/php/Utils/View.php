<?php
namespace Utils;

/**
 * Class View
 *
 * @package Utils
 * @author David Marsalone & Clément Charmet
 */
class View {

    /**
     * @var \Exception[]
     */
    static $exceptions=[];


    /**
     * @var $_view View the view inside the template
     */

    /**
     *
     * @var mixed Here are the variables used in the view.
     * Inside the template use $_vars to retrieve it.
     * It's an object, so it should be strict and it should be precise.
     */
    public $viewVariables;
    /**
     *
     * @var string Path to the template without view folder and without ".php".
     * @example a templates located at "_app/mvc/v/a-folder/hello-world.php" should be "a-folder/hello-world"
     */
    public $path;
    /**
     * @var View the View object that called the current view via render or inside...so it can be null too.
     */
    public $caller;
    /**
     *
     * @var string Contains the sub-templates that have called the inside function. So this will be set only if the current view is a kind of layout.
     */
    public $insideContent;
    /**
     *
     * @var View a view outside this view, in practice this view is a layout
     */
    private $outerView;

    /**
     * @param string $path Url path of the template you want to play with
     * @param mixed $viewVariables The object that will be used in this view
     */
    public function __construct( $path,$viewVariables=null ){
        $this->path = $path;
        if(!$viewVariables){
            $viewVariables=[];
        }
        $this->viewVariables=$viewVariables;
    }

    /**
     * To get a View object in one line, do the same as the constructor
     * @param string $path Url path of the template you want to play with
     * @param mixed $viewVariables The object that will be used in this view
     * @param bool $strict when set to true trow exception if the templates path is invalid
     * @return View
     * @throws \Exception
     */
    public static function get($path,$viewVariables=null,$strict=false){
        if($strict){
            if(!self::isValid($path)){
                throw(new \Exception("invalid template: '$path'"));
            }
        }
        $view =new View($path,$viewVariables);
        return $view;
    }


    /**
     * Try to return a valid path for a template file.
     * @param string $path a relative path to the template file without .php
     * @return string|bool the correct path or false if there is no file that match.
     */
    public static function getRealPath($path){
        $path=trim($path,"/");
        if(!preg_match("#\.php$#",$path)){
            $path.=".php";
        }
        foreach(array_reverse(self::$possiblesPath) as $base){
            $scriptPath = $base."/".$path;
            if(file_exists($scriptPath)){
                return $scriptPath;
            }
        }
        return false;
    }

    /**
     * @var array Root directories to find templates.
     */
    public static $possiblesPath=[];


    /**
     * Process the template with the current properties.
     *
     * @throws \Exception
     * @return string The HTML (or text, xml etc...)
     */
    private function run(){

        $scriptPath = self::getRealPath($this->path);

        if(!$scriptPath){
            if($this->caller){
                $from=self::getRealPath($this->caller->path);
            }else{
                $bt=debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT)[1];
                $from=$bt["file"]." @line ".$bt["line"];
            }

            $path=$this->path;
            $mess="Impossible de trouver une Vue pour  '".$path."' ( appelé depuis ".$from." )<br>";
            throw new \Exception("$mess");
            $mess.="<ul>";
            $mess.=implode("<li>",self::$possiblesPath)."<br>";
            $mess.="</ul>";
            return("<div style='font-size:11px;color:#f00;font-family:monospace;'>$mess</div>");
        }
        //$bp=Nestor::log("run template ".$this->path,$scriptPath,"",NESTOR_GROUP_VIEW);
        //declare the variables in the template
        /* @var $_vars mixed */
        /** @noinspection PhpUnusedLocalVariableInspection */
        $_vars=$vv=$this->viewVariables;
        /** @noinspection PhpUnusedLocalVariableInspection */
        $view=$this;
        /** @noinspection PhpUnusedLocalVariableInspection */
        $_content=$this->insideContent;
        /** @noinspection PhpUnusedLocalVariableInspection */
        $_view=$this;

        ob_start();
        /** @noinspection PhpIncludeInspection */
        try{
            include $scriptPath;
            $content = ob_get_contents();
        }catch (\Exception $e){
            $content=$e->getMessage();
            //die();
            self::$exceptions[]=$e;
        }



        //convertit qques machins
        //$content=the()->fileSystem->___pub___toRealPath($content);


        ob_end_clean();
        //$bp->stop();
        if($this->outerView){
            $this->outerView->insideContent=$content;
            return $this->outerView->run();
        }else{
            return $content;
        }
    }




    /**
     * Process the template and return the result.
     * @param string|resource $path path to the template to execute or insert.
     * @param mixed $viewVariables Object that will be available in the view
     * @return String The template result after execution
     */
    function render( $path=null , $viewVariables=null ){

        $viewVariables=isset($viewVariables) ? $viewVariables : $this->viewVariables;
        if($path){
            $path=$this->resolve($path);
            $view = new View($path,$viewVariables);
            $view->caller=$this;
            return $view->run();
        }else{
            if(!$path){
                //$this->preventRecursion();
            }
            $this->viewVariables=$viewVariables;
            return $this->run();
        }

    }
    /**
     * Process the template and return the result and do not display error message if the template is not valid
     * @param String $path path to the template to execute or insert.
     * @param mixed $viewVariables Object that will be available in the view
     * @return String The template result after execution
     */
    function renderIfValid($path=null , $viewVariables=null){
        $path=$this->resolve($path);
        if(!$path){
            $path=$this->path;
        }
        if(self::isValid($path)){
            return $this->render($path,$viewVariables);
        }
        return "";
    }

    /**
     * @throws \Exception if the path is empty and the file caller is a view.
     */
    private function preventRecursion(){
        $bt =  debug_backtrace();
        $i=1;

        foreach(self::$possiblesPath as $p){
            if(preg_match(
                "#^".preg_quote($p)."#",
                $bt[$i]['file']
            )){
                $message="Oups! View path argument is empty! have a look to ".$bt[$i]['file']."@".$bt[$i]['line'];
                throw (new \Exception($message));
            }
        }
    }

    /**
     * resolve path starting with ./ or ../
     * @param string $path
     * @return null|string|string[]
     */
    private function resolve($path){
        if(preg_match("@^\./*+@",$path)){
            $path=preg_replace("@^\./@",dirname($this->path)."/",$path);
        }
        if(preg_match("@^\.\./*+@",$path)){
            $path=preg_replace("@^\.\./@",dirname(dirname($this->path))."/",$path);
        }
        return $path;
    }

    /**
     * Insert the current template inside an other template.
     * In the layout template use the variable $_content to display the current template.
     * @param String $path path to the template file
     * @param mixed $viewVariables the data object given to the outer view, if not given, the object will be the current strictParams
     */
    function inside( $path, $viewVariables=null ){
        $path=$this->resolve($path);
        $this->outerView = new View($path, $viewVariables);
        $this->outerView->caller=$this;
    }

    /**
     *
     * @param string $path
     * @return bool will be true if $path is a valid template url.
     */
    public static function isValid($path){
        if(self::getRealPath($path)){
            return true;
        }else{
            return false;
        }
    }


    private $_uniqueId;

    /**
     * Utility method to get an unique id for a given view
     * @param string $suffix optionaly you can add a suffix
     * @return string something like
     */
    public function uniqueId($suffix=null)
    {
        if(!$this->_uniqueId){
            $this->_uniqueId=uniqid("view");
        }
        $r=$this->_uniqueId;
        if($suffix){
            $r.=$suffix;
        }
        return $r;
    }


} 