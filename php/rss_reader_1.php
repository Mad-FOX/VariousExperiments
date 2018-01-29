<?php
/**
 * a simple script to read RSS feed and download the articles/posts of interest (based on certain keywords).
 */

error_reporting(E_ERROR | E_PARSE);
$FILE_PATH=__DIR__;

$has_val = false;
$feed_url = '';
$feed_keywords = '';

if (!empty($_POST) && !empty($_POST['feed_url']) && !empty($_POST['feed_keywords']))
{
    $has_val = true;
    $feed_url = htmlspecialchars($_POST["feed_url"]);
    $feed_keywords = htmlspecialchars($_POST["feed_keywords"]);
}
?>

<form action=<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?> method="post">
        RSS Feed URL: <input type="text" name="feed_url" value=<?php echo $feed_url; ?>><br>
        Search Keywords: <input type="text" name="feed_keywords" value=<?php echo $feed_keywords; ?>><br>
        Files will be saved in: <?php echo $FILE_PATH; ?><br>
        <input type="submit">
</form>

<?php
if (!$has_val)
    exit;

//$feed_url = 'http://feeds.bbci.co.uk/bengali/rss.xml';
//$feed_keywords = "ধূমপান, নির্বাচন, বাংলাদেশ";

echo "<hr />";

$feed_keywords = preg_replace('/\s+/', ' ', $feed_keywords);
$feed_keywords = str_replace(',', '|', $feed_keywords);

$rss = simplexml_load_file($feed_url);

foreach($rss->channel->item as $feed_item)
{
    $feed_data = $feed_item->title." ".$feed_item->description;
    if(preg_match('('.$feed_keywords.')', $feed_data) === 1) 
    {
        $html = file_get_contents($feed_item->link);

        $hostname = parse_url($feed_item->link, PHP_URL_HOST);
        $article_id = end(explode('/', $feed_item->link));
        $filename = $FILE_PATH."/".$hostname."_".$article_id.".htm";
        
        file_put_contents($filename, $html);
        echo "File created as: ".$filename."<br />";
    }
}
