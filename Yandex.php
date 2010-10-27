<?php
/**
 * Class Yandex
 *
 * yandex search class
 *
 * @author   Anton Shevchuk <AntonShevchuk@gmail.com>
 * @link     http://anton.shevchuk.name
 * @link     http://yandex.hohli.com
 * @package  Yandex
 * @version  0.11.0
 * @created  Thu Aug 14 12:12:54 EEST 2008
 */
class Yandex
{
    /**
     * Response
     *
     * @see http://help.yandex.ru/xml/?id=362990
     * @var SimpleXML
     */
    public $response;

    /**
     * Wordstat array
     *
     * @var Array
     */
    public $wordstat = array();
    
    /**
     * Response in array
     *
     * @var Array
     */
    public $results = array();
    
    /**
     * Total results
     *
     * @var integer
     */
    public $total = null;
    
    /**
     * Total results in human form
     *
     * @var string
     */
    public $totalHuman = null;

    /**
     * User
     *
     * @var string
     */
    protected $user;

    /**
     * Key
     *
     * @var string
     */
    protected $key;

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
     * theme
     *
     * @see http://help.yandex.ru/site/?id=1111797
     * @var integer
     */
    protected $theme;

    /**
     * geo
     *
     * @see http://search.yaca.yandex.ru/geo.c2n
     * @var integer
     */
    protected $geo;

    /**
     * lr
     * 
     * @var integer
     */
    protected $lr;

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
     * __construct
     *
     * @return Yandex
     */
     public function __construct($user, $key)
     {
         if (empty($user) or empty($key)) {
             throw new Exception('Yandex: username and key is requeried');
         }
         $this->user = $user;
         $this->key  = $key;
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
     * theme
     *
     * @access  public
     * @param   integer   $theme
     * @return  Yandex
     */
    public function theme($theme)
    {
        $this->theme = $theme;
        return $this;
    }

    /**
     * getTheme
     *
     * @access  public
     * @return  integer
     */
    public function getTheme()
    {
        return $this->theme;
    }
    
    /**
     * lr
     *
     * @access  public
     * @param   integer   $lr
     * @return  Yandex
     */
    public function lr($lr)
    {
        $this->lr = $lr;
        return $this;
    }

    /**
     * getLr
     *
     * @access  public
     * @return  integer
     */
    public function getLr()
    {
        return $this->lr;
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
        if (empty($this->query)
            && empty($this->host)
            ) {
            throw new Exception('Yandex: Query is empty');
        }

        $xml = new SimpleXMLElement("<?xml version='1.0' encoding='utf-8'?><request></request>");

        // add query to request
        $query    = $this->query;

        // if isset "host"
        if ($this->host) {
            if (is_array($this->host)) {
                $host_query = '(host="'.join($this->host, '"|host="') .'")';
            } else {
                $host_query = 'host="'.$this->host.'"';
            }

            if (!empty($query) && $this->host) {
                $query .=  ' << '.$host_query;
            } elseif (empty($query) && $this->host) {
                $query .=  $host_query;
            }
        }

        // if isset "cat"
        if ($this->cat) {
            $query .=  ' << cat=('.($this->cat+9000000).')';
        }
        
        // if isset "theme"
        if ($this->theme) {
            $query .=  ' << cat=('.($this->theme+4000000).')';
        }

        // if isset "geo"
        if ($this->geo) {
            $query .=  ' << cat=('.($this->geo+11000000).')';
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

        $url = 'http://xmlsearch.yandex.ru/xmlsearch'
             . '?user='.$this->user
             . '&key='.$this->key;

        if ($this->lr) {
            $url .= '&lr='.$this->lr;
        }

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/xml"));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Accept: application/xml"));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml->asXML());
        curl_setopt($ch, CURLOPT_POST, true);
        $data = curl_exec($ch);

        $this->response = new SimpleXMLElement($data);
        $this->response = $this->response->response;
        if ($this->_checkErrors()) {
            $this->_bindData();
        }

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
    protected function _checkErrors()
    {
        // switch statement for $this->response->error
        switch (true) {
            case isset($this->response->error):
                // &&    ($error = $this->response->error->attributes()->code[0] || $this->response->error->attributes()->code[0] === 0):
                $error = (int)$this->response->error->attributes()->code[0];
                if (isset($this->errors[$error])) {
                    $this->error = $this->errors[$error];
                } else {
                    $this->error = $this->response->error;
                }
                break;

            case isset($this->response->error) && !empty($this->response->error):
                $this->error = $this->response->error;
                break;

            default:
                $this->error = null;
                break;
        }

        if ($this->error) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * bindData
     *
     * @return void
     */
     protected function _bindData()
     {
         $wordstat = split(',',$this->response->wordstat);
         $this->wordstat = array();
         foreach ($wordstat as $word) {
             list($word, $count) = split(":", $word);
             $this->wordstat[$word] = intval(trim($count));
         }
     }

    /**
     * get total results
     * 
     * @access  public
     * @return  integer
     */
    public function total()
    {
        if ($this->total === null) {
            $res = $this->response->xpath('found[attribute::priority="all"]');
            $this->total = (int)$res[0];
        }
        return $this->total;
    }

    /**
     * get total results in human form
     * 
     * @access  public
     * @return  integer
     */
    public function totalHuman()
    {
        if ($this->totalHuman === null) {
            $res = $this->response->xpath('found-human');
            $this->totalHuman = $res[0];
        }
        return $this->totalHuman;
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
        if (empty($this->pages)) {
            $this->pages = ceil($this->total() / $this->limit);
        }
        return $this->pages;
    }
    
    /**
     * return associated array of groups
     *
     * @return  array
     */
    public function results() 
    {
        if (empty($this->results)) {
            if (empty($this->error) && $this->response) {
                foreach ($this->response->results->grouping->group as $group) {
                    $res = new stdClass();
                    $res ->url       = $group->doc->url;
                    $res ->domain    = $group->doc->domain;
                    $res ->title     = isset($group->doc->title)?$group->doc->title:$group->doc->url;
                    $res ->headline  = isset($group->doc->headline)?$group->doc->headline:null;
                    $res ->passages  = isset($group->doc->passages->passage)?$group->doc->passages->passage:null;
                    $res ->sitelinks = isset($group->doc->snippets->sitelinks->link)?$group->doc->snippets->sitelinks->link:null;
                    
                    array_push($this->results, $res);
                }
            }
        }
        return $this->results;
    }

    /**
     * return pagebar array
     *
     * @access  public
     * @return  array
     */
    public function pageBar()
    {
        // FIXME: not good
        $pages = $this->pages();

        if ($pages < 10) {
            $pagebar = array_fill(0, $pages, array('type'=>'link', 'text'=>'%d'));
            $pagebar[$this->page] = array('type'=>'current', 'text'=>'<b>%d</b>');
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
     * highlight text
     *
     * @access  public
     * @param   SimpleXML $xml  
     * @return  rettype   return
     */     
    static public function highlight($xml)
    {
        // FIXME: very strangely method
        $text = $xml->asXML();

        $text = str_replace('<hlword>', '<strong>', $text);
        $text = str_replace('</hlword>', '</strong>', $text);
        $text = strip_tags($text, '<strong>');

        echo $text;
    }
}