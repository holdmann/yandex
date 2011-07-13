<?php
session_start();
if (!isset($_SESSION['ttl'])) {
    $_SESSION['ttl'] = microtime(true);
}

require_once 'Yandex.php';

// get "query" and "page" from request
$query = isset($_REQUEST['query'])?$_REQUEST['query']:'';
$page  = isset($_REQUEST['page']) ?$_REQUEST['page']:0;
$host  = isset($_REQUEST['host']) ?$_REQUEST['host']:null;
$geo   = isset($_REQUEST['geo']) ?$_REQUEST['geo']:null;
$cat   = isset($_REQUEST['cat']) ?$_REQUEST['cat']:null;
$theme = isset($_REQUEST['theme']) ?$_REQUEST['theme']:null;

// small protection for example script
// only 2 seconds
if ($query && (microtime(true) - $_SESSION['ttl']) > 2) {
    // Your data http://xmlsearch.yandex.ru/xmlsearch?user=AntonShevchuk&key=03.28303679:b340c90e875df328e6e120986c837284
    $user = 'AntonShevchuk';
    $key  = '03.28303679:b340c90e875df328e6e120986c837284';

    // Create new instance of Yandex class
    $Yandex = new Yandex($user, $key);
    
    // Set Query
    $Yandex -> query($query)
            -> host($host)                      // set one host or multihost
            //-> host(array('anton.shevchuk.name','cotoha.info')) 
            //-> site(array('anton.shevchuk.name','cotoha.info')) 
            //-> domain(array('ru','org'))
            -> page($page)                      // set current page
            -> limit(10)                        // set page limit
            -> geo($geo)                        // set geo region - http://search.yaca.yandex.ru/geo.c2n
            -> cat($cat)                        // set category - http://search.yaca.yandex.ru/cat.c2n
            -> theme($theme)                    // set theme - http://help.yandex.ru/site/?id=1111797
            -> sortby(Yandex::SORT_RLV)
            -> groupby(Yandex::GROUP_DEFAULT)
            
            -> set('max-title-length',   160)   // set some options
            -> set('max-passage-length', 200)
            -> request()                        // send request
            ;

    // Debug request
    $request = $Yandex -> getRequest()->asXml();
}

// current URL
$server = "http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
$server = substr($server, 0, strpos($server, '?'));
$url = $server .'?query='.urlencode($query)
               .'&host='.urlencode($host)
               .'&geo='.urlencode($geo)
               .'&cat='.urlencode($cat)
               .'&theme='.urlencode($theme)
               ;
?>
<!DOCTYPE html>
<html dir="ltr" lang="en-US">
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <title>Yandex XML Search</title>
    <meta name="keywords" content="Yandex XML, PHP, PHP5" />
    <meta name="description" content="Yandex XML Search for PHP" />
    <link rel="profile" href="http://gmpg.org/xfn/11"/>
    <link rel="stylesheet" href="styles.css" type="text/css" />
    <script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-7269638-9']);
  _gaq.push(['_setDomainName', '.hohli.com']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
</head>
<body>
<div class="body">
    <div class="form">    
        <form>
            <fieldset class="box">
                &nbsp;&nbsp;&nbsp;
                <a href="http://www.yandex.ru/" title="Яндекс"><img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAGQAAAAsCAMAAACkN+1nAAABqlBMVEUAAAADAwMGBgYJCQkMDAwPDw8SEhIVFRUYGBgbGxseHh4hISEkJCQnJycqKiotLS0wMDAzMzM2NjY5OTk8PDw/Pz9CQkJFRUVISEhLS0tOTk5RUVFUVFRXV1daWlpdXV1gYGBjY2NmZmZpaWlsbGxvb29ycnJ4eHh7e3t+fn6BgYGEhISHh4eKioqNjY2QkJCTk5OWlpaZmZmcnJyfn5+ioqKlpaWoqKirq6uurq6xsbG0tLS3t7e6urq9vb3AwMDDw8PGxsbJycnMzMzPz8/S0tLV1dXY2Njb29ve3t7h4eHk5OTn5+fq6urt7e3w8PDz8/P29vb5+fn8/Pz/AAD/AwP/Bgb/CQn/DAz/Dw//EhL/FRX/GBj/Gxv/Hh7/ISH/JCT/Jyf/MDD/MzP/Pz//QkL/SEj/S0v/Tk7/UVH/YGD/Zmb/aWn/bGz/cnL/dXX/eHj/e3v/fn7/hIT/jY3/k5P/lpb/mZn/nJz/n5//paX/q6v/rq7/tLT/w8P/xsb/z8//1dX/2Nj/29v/3t7/4eH/5OT/7e3/8PD/8/P/9vb/+fn//Pz////dxpC1AAADeUlEQVR42u3V6VcTZxSA8WfIrlIbFCS0TSUUKBBToCGEpYhQCSKGhIyttZVaaWv3zbrQTW3rWu7/3DvJTAayDB7PmXP84PNlkpNkfufOO/MGqfb49vXtD0ztqvhQDblz2bT7yDfke9Pqs+0tH5HbKnzyxyORW/4hDz40zWsPRXxFfjTNi/fFX2RXF/1r8Rm5qyuy4zdyU5H7fiM/KPLUb+RbReQ5kPXsWLYo7drMj03kyy7SPMkURMUqBlNSLYXdCak1GgACWbEqB2BGj5UEdIrVfAwt/NaGjfykyN/7kQkIiVUIJqRaErvjUm2IWnOibQA5PeaAvGjTBtWMgo3s2HeXN/IGROLxsIPMAcPz3RAt70GOwjHRCkFILGQPMyw28kCRrw5EEli/SDpIHFIiF0Iw7iKzwKJofXBkU6Q0VnIQ+Vyf+LvPgIy6yDKwLNX3CRfpgj7RzgKzDdvKXzrK1j8HID1wykWGISbauxDYdJAFZw0G4ZA0ILp56clv/OuJHIOsi+i7XtFOAysO0gMDYnUY+psQ+c60a490wnwdqRgwJNoqMGsjSxA8L1oRmGhG5LcrjUhg0ipQ/3oEluvIOSAj2howZSN9MCJWi8BCC6S6S366veUidg5SBtbryHvAoSPuFywkBdGS2AtFoRXypWleeiS32iIrEJE6MouTiwQgVBSrDLDWAvlTB/lFFGl3uWagx0VyQG/qeLxavoakO+BtsRppjexeM83Lj73urlEYcpFpYE6c7DXph+C6jbS6XHpy84Z4IQnIucg8kG9ECgYMOpfrdBPyRO+tj594IZUQRtFF3gfeaUSsDzvO1/ZwJpuQn3WQX8ULWYRecZFNA5JNyIoB/faukmxEHl4yzStPPZEBmK4jWheENhoReR2MVR07CKFSA/KNDnJTvJByhM7KXiQDpBoQd4STwOB+5N5F3R//80TGISs2EhdtPQj0VxoQ6QPjrMgZtLHKXuQLHeSOeCClbj28GrcKo6+sm/cUWiSZ6nkltgdZAl6zRyH2Zqormq4h1nN4ddcLWWN/aXsmu4KLyAlgSaTcjZ2SGtf1L+t3qbWjz6RokxAVqzBMtkYkHQRrLuvkpQ6YEW0pDAlRZagDzeg/V0PkwCxEnAYcRErTmczcqrSpmEuPL26IPD/yzL1gyIUAR8VpBCPvA9LcS+Ql8gIh/wO4jFzH/PtOVAAAAABJRU5ErkJggg=="alt="Яндекс" width="100" border="0" height="44"></a>,&nbsp;<input type="text" name="query" class="txt" value="<?php echo $query;?>"/>, 
                <input type="submit" class="smb" name="search" value="Ищи!"/><br /><br />
                <span>Регион</span>:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<select name="geo">
                    <option value="">Все</option>
                    <optgroup label="Город">
                        <option value="213" <?php if ($geo == 213) echo 'selected="selected"'?>>Москва</option>
                        <option value="2"   <?php if ($geo == 2)   echo 'selected="selected"'?>>Санкт-Петербург</option>
                        <option value="143" <?php if ($geo == 143) echo 'selected="selected"'?>>Киев</option>
                        <option value="157" <?php if ($geo == 157) echo 'selected="selected"'?>>Минск</option>
                    </optgroup>
                    <optgroup label="Страна">
                        <option value="225" <?php if ($geo == 225) echo 'selected="selected"'?>>Россия</option>
                        <option value="187" <?php if ($geo == 187) echo 'selected="selected"'?>>Украина</option>
                        <option value="149" <?php if ($geo == 149) echo 'selected="selected"'?>>Беларусь</option>
                    </optgroup>
                </select><br />
                <span>Категория</span>:&nbsp;<select name="cat">
                    <option value="">Все</option>
                    <option value="5"    <?php if ($cat == 5) echo 'selected="selected"'?>>Интернет</option>
                    <option value="3795" <?php if ($cat == 3795) echo 'selected="selected"'?>>Кино</option>
                    <option value="3796" <?php if ($cat == 3796) echo 'selected="selected"'?>>Музыка</option>
                    <option value="3797" <?php if ($cat == 3797) echo 'selected="selected"'?>>Литература</option>
                    <option value="3798" <?php if ($cat == 3798) echo 'selected="selected"'?>>Фото</option>
                </select><br/>
            </fieldset>
            <input type="hidden" name="host"  value="<?php echo $host ?>"/>
            <input type="hidden" name="theme" value="<?php echo $theme ?>"/>
        </form>
    </div>
    <div class="data">
        <?php 
            // if $Yandex exists and don't have errors in response
            if (isset($Yandex) && empty($Yandex->error)) : 
        ?>
            <div class="result box">
                <p><?php echo $Yandex->totalHuman() ?></p>
            </div>
            <ol start="<?php echo $Yandex->getLimit()*$Yandex->getPage() + 1;?>">
            <?php foreach ($Yandex->results() as $result) :?>
                <?php
                    /*
                    $result is Object with next properties:
                        ->url
                        ->domain
                        ->title
                        ->headline
                        ->passages // array
                        ->sitelinks // array
                    */
                ?>
                <li class="box"><a href="<?php echo $result->url; ?>" title="<?php echo $result->url; ?>" class="title"><?php Yandex::highlight($result->title); ?></a>
                    <?php if ($result->headline) : ?>
                    <div class="headline">
                        <?php echo $result->headline; ?>
                    </div>
                    <?php endif; ?>
                    <?php if ($result->passages) : ?>
                    <ul class="passages">
                        <?php foreach ($result->passages as $passage) :?>
                        <li><?php Yandex::highlight($passage);?></li>                    
                        <?php endforeach;?>
                    </ul>
                    <?php endif; ?>
                    <a href="<?php echo $result->url; ?>" class="host" title="<?php echo $result->url; ?>"><?php echo $result->domain; ?></a> 
                    <a href="<?php echo $server .'?query='.urlencode($query).'&host='. urlencode($result->domain)?>" class="host" title="Поиск на сайте <?php echo $result->domain; ?>">ещё</a>
                </li>
            <?php endforeach;?>
            </ol>
            <div class="pagebar box">
            <p>
            <?php foreach ($Yandex->pageBar() as $page => $value) : ;?>
                <?php // switch statement for $value['type']
                switch ($value['type']) {
                	case 'link':
                		echo '<a href="'. $url .'&page='. $page .'" title="Page '. ($page+1) .'">'. sprintf($value['text'], $page+1) .'</a> | ';
                		break;
                	case 'current':
                		echo sprintf($value['text'], $page+1) .' | ';
                		break;
                	case 'text':
                		echo $value['text'] .' | ';
                		break;
                
                	default:
                		break;
                }
                ?>
            <?php endforeach;?>
            <?php /*if ($Yandex->pages() > 1 && $Yandex->getPage() != $Yandex->pages()-1) : ?>
                <?php if ($Yandex->getPage() == $Yandex->pages() - 2):?>
                    <a href="<?php echo $url;?>&page=<?php echo $Yandex->getPage()+1;?>" title="Next Page"><?php echo $Yandex->getPage()+2;?></a> 
                <?php elseif ($Yandex->getPage() < $Yandex->pages()):?> .. |
                    <a href="<?php echo $url;?>&page=<?php echo $Yandex->getPage()+1;?>" title="Next Page">&raquo;</a>
                <?php endif; ?>            
            <?php endif;*/ ?>            
            </p>
            </div>
        <?php 
            // Error in response
            elseif(isset($Yandex) && isset($Yandex->error)):
        ?>
            <div class="error"><?php echo $Yandex->error; ?></div>
        <?php endif; ?>
    </div>
    <div class="download">
        <p>
        Демонстрация работы PHP скрипта с поисковым сервисом Яндекс.XML.<br/>
        Последняя версия всегда доступна на страницах Code Google:<br/>
        <code>
            <a href="http://code.google.com/p/yandex/downloads/list">http://code.google.com/p/yandex/downloads/list</a>
        </code>
    
        Доступ к SVN репозиторию проекта:<br/>
        <code>
            svn checkout http://yandex.googlecode.com/svn/trunk/ yandex-read-only
        </code>
        
        Для организации поиска по регионам или категориям смотрите коды на следующих страницах:
        </p>
        <ul>
            <li><a href="http://search.yaca.yandex.ru/geo.c2n">Коды регионов</a></li>
            <li><a href="http://search.yaca.yandex.ru/cat.c2n">Коды рубрик</a></li>
        </ul>
    </div>
    <!--div class="request">
        <pre>
        <?php //echo htmlentities($request, ENT_QUOTES, "UTF-8") ?>
        </pre>
    </div>-->
    <div class="copyright">
        &copy; 2008-<?php echo date('Y') ?> <a href="http://anton.shevchuk.name" title="Anton Shevchuk">Anton Shevchuk</a><br/>
        Поиск реализован на основе <a href="http://xml.yandex.ru/" title="Яндекс.XML">Яндекс.XML</a>
    </div>
</div>
</body>
</html>