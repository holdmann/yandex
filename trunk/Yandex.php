<?php
/**
 * Class Yandex
 *
 * yandex search class
 *
 * @author   Anton Shevchuk <AntonShevchuk@gmail.com>
 * @link     http://anton.shevchuk.name
 * @access   public
 * @package  Yandex
 * @version  0.6
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
     * Request
     *
     * @var string
     */
    protected $request;
    
    /**
     * Host
     *
     * @var string
     */
    protected $host;
    
    /**
     * cat
     *
     * @see http://search.yaca.yandex.ru/cat.c2n
     * @var integer
     */
    protected $cat;
    
    /**
     * geo
     *
     * @see http://search.yaca.yandex.ru/geo.c2n
     * @var integer
     */
    protected $geo;
    
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
     * Sort By   'rlv' || 'tm'
     * 
     * @see http://help.yandex.ru/xml/?id=316625#sort
     * @var string
     */
    const SORT_RLV = 'rlv'; // relevation
    const SORT_TM  = 'tm';  // time modification
    
    protected $sortby = 'rlv';
    
    /**
     * Group By  '' || 'd'
     *
     * @see http://help.yandex.ru/xml/?id=316625#group
     * @var string
     */
    const GROUP_DEFAULT = '';
    const GROUP_SITE    = 'd'; // group by site
    protected $groupby = '';
    
    /**
     * Group mode   'flat' || 'deep' || 'wide'
     *
     * @var string
     */
    const GROUP_MODE_FLAT = 'flat';
    const GROUP_MODE_DEEP = 'deep';
    const GROUP_MODE_WIDE = 'wide';
    protected $groupby_mode = 'flat';
    
    
    /**
     * Options of search
     *
     * @var array
     */
    protected $options = array(
        'maxpassages'           => 2 ,    // from 2 to 5
        'max-title-length'      => 160 , // 
        'max-headline-length'   => 160 , // 
        'max-passage-length'    => 160 , // 
        'max-text-length'       => 640 , // 
    
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
       15 => 'Искомая комбинация слов нигде не встречается',
       18 => 'Ошибка в XML-запросе — проверьте валидность отправляемого XML и корректность параметров',
       19 => 'Заданы несовместимые параметры запроса — проверьте корректность группировочных атрибутов',
       20 => 'Неизвестная ошибка — при повторяемости ошибки обратитесь к разработчикам с описанием проблемы',
    );
        
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
      * @param   string   $host
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
      * cat
      *
      * @access  public
      * @param   integer   $cat
      * @return  Yandex
      */
     public function cat($cat) 
     {
        $this->cat = $cat;
         return $this;
     }
     
     /**
      * getCat
      *
      * @access  public
      * @return  integer
      */
     public function getCat() 
     {
        return $this->cat;
     }
     
     /**
      * geo
      *
      * @access  public
      * @param   integer   $geo
      * @return  Yandex
      */
     public function geo($geo) 
     {
        $this->geo = $geo;
         return $this;
     }
     
     /**
      * getGeo
      *
      * @access  public
      * @return  integer
      */
     public function getGeo() 
     {
        return $this->geo;
     }
     
     /**
      * sortby
      *
      * @access  public
      * @param   string   $sortby
      * @return  Yandex
      */
     public function sortby($sortby) 
     {
        if ($sortby == Yandex::SORT_RLV || $sortby == Yandex::SORT_TM)
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
      * groupby
      *
      * @access  public
      * @param   string   $groupby
      * @return  Yandex
      */
     public function groupby($groupby, $mode = Yandex::GROUP_MODE_FLAT) 
     {
        if ($groupby == Yandex::GROUP_DEFAULT || $groupby == Yandex::GROUP_SITE) {
            $this->groupby = $groupby;
            if ($groupby == Yandex::GROUP_DEFAULT) {
                $this->groupby_mode = Yandex::GROUP_MODE_FLAT;
            } else {
                $this->groupby_mode = $mode;
            }
        }
        return $this;
     }
     
     /**
      * getGroupby
      *
      * @access  public
      * @return  string
      */
     public function getGroupby() 
     {
        return $this->groupby;
     }
     
     /**
      * getGroupbyMode
      *
      * @access  public
      * @return  string
      */
     public function getGroupbyMode() 
     {
        return $this->groupby_mode;
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
              
        $xml = new SimpleXMLElement("<?xml version='1.0' encoding='utf-8'?><request></request>");
             
         // add query to request
         $query    = $this->query;
         
         // if isset "host"
         if ($this->host) {
             $query .=  '<< host="'.$this->host.'"';
         }
         
         // if isset "cat"
         if ($this->cat) {
             $query .=  '<< cat=('.($this->cat+9000000).')';
         }
         
         // if isset "geo"
         if ($this->geo) {
             $query .=  '<< cat=('.($this->geo+11000000).')';
         }
         
         $xml -> addChild('query', $query);
         $xml -> addChild('page',  $this->page);
             $groupings = $xml -> addChild('groupings');
             $groupby   = $groupings -> addChild('groupby');
             $groupby->addAttribute('attr', $this->groupby);
             $groupby->addAttribute('mode', $this->groupby_mode);
             $groupby->addAttribute('groups-on-page', $this->limit);
             $groupby->addAttribute('docs-in-group',  1);
             $groupby->addAttribute('curcateg',  -1);

         $xml -> addChild('maxpassages', $this->options['maxpassages']);
         $xml -> addChild('max-title-length', $this->options['max-title-length']);
         $xml -> addChild('max-headline-length', $this->options['max-headline-length']);
         $xml -> addChild('max-passage-length', $this->options['max-passage-length']);
         $xml -> addChild('max-text-length', $this->options['max-text-length']);
         
        $this->request = $xml;
        
        $ch = curl_init();    
        curl_setopt($ch, CURLOPT_URL, "http://xmlsearch.yandex.ru/xmlsearch");
        curl_setopt($ch, CURLOPT_HTTPHEADER, Array("Content-Type: application/xml"));
        curl_setopt($ch, CURLOPT_HTTPHEADER, Array("Accept: application/xml"));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml->asXML());
        curl_setopt($ch, CURLOPT_POST, TRUE);    
        $data = curl_exec($ch);

        $this->result = new SimpleXMLElement($data);
        $this->checkErrors();
        
        return $this;
     }
     
     /**
      * Get request
      *
      * return last request as string
      * 
      * @param string $request
      */
     public function getRequest()
     {
        return $this->request;
     }
     
     /**
      * checkErrors
      *
      * check response errors
      *
      * @access  public
      * @return  void
      */
     protected function checkErrors() 
     {
         // switch statement for $this->result->response->error
         switch (true) {             
             case isset($this->result->response->error):
                 // &&    ($error = $this->result->response->error->attributes()->code[0] || $this->result->response->error->attributes()->code[0] === 0):
                 $error = (int)$this->result->response->error->attributes()->code[0];
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