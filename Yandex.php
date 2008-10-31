<?php
/**
 * Class Yandex
 *
 * yandex search class
 *
 * @author   Anton Shevchuk <AntonShevchuk@gmail.com>
 * @link     http://anton.shevchuk.name
 * @access   public
 * @package  Yandex.class.php
 * @created  Thu Aug 14 12:12:54 EEST 2008
 */
class Yandex 
{
    /**
     * Response
     *
     * @var SimpleXML
     */
    public $result;
    
    /**
     * Query
     *
     * @var string
     */
    protected $query;
    
    /**
     * Host
     *
     * @var string
     */
    protected $host;
    
    /**
     * Number of page   
     *
     * @var integer
     */
    protected $page = 0;
    
    /**
     * Number of results per page   
     *
     * @var integer
     */
    protected $limit = 10;
    
    /**
     * Sort By   rlv | tm
     * 
     * @see http://help.yandex.ru/xml/?id=316625#sort
     * @var string
     */
    protected $sortby = 'rlv';
    
    /**
     * Options of search
     *
     * @var array
     */
    protected $options = array(
        'maxpassages'           => null , // from 2 to 5
        'groupings'             => null , // http://help.yandex.ru/xml/?id=316625#group <d> <geo> <cat> <>
        'max-title-length'      => null , // 
        'max-headline-length'   => null , // 
        'max-passage-length'    => null , // 
        'max-text-length'       => null , // 
    
    );
    
    /**
     * Error code
     *
     * @var integer
     */
    public $error = null;
    
    /**
     * Errors in response
     *
     * @var array
     */
    // TODO: add all errors code
    protected $errors = array(
        1 => 'Синтаксическая ошибка — ошибка в языке запросов',
        2 => 'Задан пустой поисковый запрос — элемент query не содержит данных',
        8 => 'Зона не проиндексирована — обратите внимание на корректность параметров зонно-атрибутивного поиска',
        9 => 'Атрибут не проиндексирован — обратите внимание на корректность параметров зонно-атрибутивного поиска',
       10 => 'Атрибут и элемент не совместимы — обратите внимание на корректность параметров зонно-атрибутивного поиска',
       12 => 'Результат предыдущего запроса уже удален — задайте запрос повторно, не ссылаясь на идентификатор предыдущего запроса',
       15 => 'Найдётся всё. Со временем',
       18 => 'Ошибка в XML-запросе — проверьте валидность отправляемого XML и корректность параметров',
       19 => 'Заданы несовместимые параметры запроса — проверьте корректность группировочных атрибутов',
       20 => 'Неизвестная ошибка — при повторяемости ошибки обратитесь к разработчикам с описанием проблемы',
    );
    
	/**
	 * Constructor of Yandex
	 *
	 * @access  public
	 */
	function __construct() 
	{
		
	}

	/**
	 * Destructor of Yandex 
	 *
	 * @access  public
	 */
	 function __destruct()
	 {
	 	
	 }
	
	 /**
	  * query
	  *
	  * @access  public
	  * @param   string   $query
	  * @return  Yandex
	  */
	 public function query($query) 
	 {
	    $this->query = $query;
	 	return $this;
	 }
	 
	 /**
	  * getQuery
	  *
	  * @access  public
	  * @return  string
	  */
	 public function getQuery() 
	 {
	    return $this->query;
	 }
	
	 /**
	  * page
	  *
	  * @access  public
	  * @param   integer   $page
	  * @return  Yandex
	  */
	 public function page($page) 
	 {
	    $this->page = $page;
	 	return $this;
	 }
	 
	 /**
	  * getPage
	  *
	  * @access  public
	  * @return  integer
	  */
	 public function getPage() 
	 {
	    return $this->page;
	 }
	 
	 /**
	  * limit
	  *
	  * @access  public
	  * @param   integer   $limit
	  * @return  Yandex
	  */
	 public function limit($limit) 
	 {
	    $this->limit = $limit;
	 	return $this;
	 }
	 
	 /**
	  * getLimit
	  *
	  * @access  public
	  * @return  integer
	  */
	 public function getLimit() 
	 {
	    return $this->limit;
	 }
	 
	 /**
	  * host
	  *
	  * @access  public
	  * @param   integer   $string
	  * @return  Yandex
	  */
	 public function host($host) 
	 {
	    $this->host = $host;
	 	return $this;
	 }
	 
	 /**
	  * getHost
	  *
	  * @access  public
	  * @return  string
	  */
	 public function getHost() 
	 {
	    return $this->host;
	 }
	 
	 /**
	  * sortby
	  *
	  * @access  public
	  * @param   integer   $limit
	  * @return  Yandex
	  */
	 public function sortby($sortby) 
	 {
        if ($sortby == 'rlv' || $sortby == 'tm')
            $this->sortby = $sortby;
        return $this;
	 }
	 
	 /**
	  * getSortby
	  *
	  * @access  public
	  * @return  string
	  */
	 public function getSortby() 
	 {
	    return $this->sortby;
	 }
	 
	 /**
	  * set
	  *
	  * @access  public
	  * @param   string   $option
	  * @param   mixed    $value
	  * @return  Yandex
	  */
	 public function set($option, $value = null) 
	 {
	    $this->options[$option] = $value;
	 	return $this;
	 }
	 
	 /**
	  * request
	  *
	  * send request
	  *
	  * @access  public
	  * @return  Yandex  
	  */
	 public function request() 
	 {
	     if (empty($this->query)) {
	         throw new Exception('Query is empty');
	     }
	     
	 	$request  = '<?xml version="1.0" encoding="utf-8"?>
	 	             <request>';	 	
	 	// add query to request
	 	$query    = $this->query;
	 	
	 	// if isset "host"
	 	if ($this->host) {
	 	    $query .=  '<< host="'.$this->host.'"';	 	    
	 	}
	 	
	 	$request .= '<query><![CDATA['.$query.']]></query>';
	 	
	 	if ($this->page) {
	 	    $request .= '<page>'.$this->page.'</page>';
	 	}
	 	
	 	$request .= '<groupings>
                        <groupby  attr="" mode="flat" groups-on-page="'.$this->limit.'" docs-in-group="1" curcateg="-1" />
                    </groupings>';
	 	
	 	$request .= '<sortby order="descending" priority="yes">'.$this->sortby.'</sortby>';
	 	
	 	// TODO: add groupings and sortby realisation
	 	/*
	 	<sortby order="descending" priority="no">rlv</sortby>
	 	
	 	<groupings>
            <groupby attr="d" mode="deep" groups-on-page="10" docs-in-group="1" curcateg="-1"/>
        </groupings>
        
        <groupings>
            <groupby attr="" mode="flat" groups-on-page="10" docs-in-group="1" />
        </groupings>
	 	*/
	 	
	 	if ($this->options['maxpassages']) {
	 	    $request .= '<maxpassages>'.$this->options['maxpassages'].'</maxpassages>';
	 	}
	 	
	 	if ($this->options['max-title-length']) {
	 	    $request .= '<max-title-length>'.$this->options['max-title-length'].'</max-title-length>';
	 	}
	 	
	 	if ($this->options['max-headline-length']) {
	 	    $request .= '<max-headline-length>'.$this->options['max-headline-length'].'</max-headline-length>';
	 	}
	 	
	 	if ($this->options['max-passage-length']) {
	 	    $request .= '<max-passage-length>'.$this->options['max-passage-length'].'</max-passage-length>';
	 	}
	 	
	 	if ($this->options['max-text-length']) {
	 	    $request .= '<max-text-length>'.$this->options['max-text-length'].'</max-text-length>';
	 	}
	 	
	 	
	 	$request .= '</request>';
	 	
 	    $ch = curl_init();    
        curl_setopt($ch, CURLOPT_URL, "http://xmlsearch.yandex.ru/xmlsearch");
        curl_setopt($ch, CURLOPT_HTTPHEADER, Array("Content-Type: application/xml"));
        curl_setopt($ch, CURLOPT_HTTPHEADER, Array("Accept: application/xml"));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
        curl_setopt($ch, CURLOPT_POST, TRUE);    
        $data = curl_exec($ch);

        $this->result = new SimpleXMLElement($data);
        $this->checkErrors();
        
        return $this;
	 }
	 
	 /**
	  * checkErrors
	  *
	  * check response errors
	  *
	  * @author  dark
	  * @class   
	  * @access  public
	  * @param   type     $param  param_descr
	  * @return  rettype  return
	  */
	 protected function checkErrors() 
	 {
	 	// switch statement for $this->result->response->error
	 	switch (true) {	 	    
	 		case isset($this->result->response->error) && 
	 		    ($error = $this->result->response->error->attributes()->code || $this->result->response->error->attributes()->code === 0):
	 		     $error = (int)$error;
	 		    if (isset($this->errors[$error])) {
	 		        $this->error = $this->errors[$error];	 		        
	 		    } else {
 		            $this->error = $this->result->response->error;
 		        }
	 			break;
	 			
	 		case isset($this->result->response->error) && !empty($this->result->response->error):
	 			$this->error = $this->result->response->error;
	 			break;
	 	
	 		default:
	 		    $this->error = null;
	 			break;
	 	}
	 }
	 
	 /**
	  * total
	  *
	  * get total results
	  * 
	  * @access  public
	  * @return  integer
	  */
	 public function total() 
	 {
	     // FIXME: need fix?
	     if (empty($this->total)) {
	         $res = $this->result->xpath('response/found[attribute::priority="all"]');
	         $this->total = (int)$res[0];
	     }
	 	 return $this->total;
	 }
	 
	 /**
	  * pages
	  *
	  * get total pages
	  *
	  * @access  public
	  * @param   type     $param  param_descr
	  * @return  rettype  return
	  */
	 public function pages() 
	 {
	     if (empty($this->pages))
	 	     $this->pages = ceil($this->total() / $this->limit);
 	     return $this->pages;
	 }
	 
	 /**
	  * pageBar
	  *
	  * return pagebar
	  *
	  * @access  public
	  * @return  rettype  return
	  */
	 public function pageBar() 
	 {
	     // FIXME: not good
	 	$pages = $this->pages();
	 	
	 	if ($pages < 10) {
	 	    $pagebar = array_fill(0, $pages, array('type'=>'link', 'text'=>'%d'));
	 	} elseif ($pages >= 10 && $this->page < 9) {
	 	    $pagebar = array_fill(0, 10, array('type'=>'link', 'text'=>'%d'));
	 	    $pagebar[$this->page] = array('type'=>'current', 'text'=>'<b>%d</b>');
	 	} elseif ($pages >= 10 && $this->page >= 9) {
	 	    $pagebar = array_fill(0, 2, array('type'=>'link', 'text'=>'%d'));
	 	    $pagebar[] = array('type'=>'text', 'text'=>'..');
	 	    $pagebar += array_fill($this->page-2, 2, array('type'=>'link', 'text'=>'%d'));	
	 	    if ($pages > ($this->page+2))   
	 	        $pagebar += array_fill($this->page, 2, array('type'=>'link', 'text'=>'%d'));	 	    
	 	    $pagebar[$this->page] = array('type'=>'current', 'text'=>'<b>%d</b>');
	 	}	 	
	 	return $pagebar;
	 }
	 
	 
	 /**
	  * highlight
	  *
	  * highlight text
	  *
	  * @access  public
	  * @param   SimpleXML $text  
	  * @return  rettype   return
	  */	 
	 static function highlight($text) 
	 {
	     // FIXME: very strangely method
	     $xml = $text->asXML();
	     
	     $xml = str_replace('<hlword priority="strict">', '<b>', $xml);
	     $xml = str_replace('</hlword>', '</b>', $xml);
	     $xml = strip_tags($xml, '<b>');
	     
	     echo $xml;
	 }
}