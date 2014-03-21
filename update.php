<!DOCTYPE HTML>
<html>
    <body>
        <div style="text-align:center;margin:2em;">
            <form method="POST" action="update.php">
                <fieldset>
                    <?php
                    
                    /*  config.php contains these variables.
                        $email = "";
                        $passcode = "";
                        $authorName = "";
                        $mediaFolder = "";
                        $thumbNailImage = "";
                     */
                    include "config.php";
                    
                    if ($_SERVER['REQUEST_METHOD'] != "POST") {
                        ?>

                        <legend>RSS Feed</legend>
                        <span style="line-height: 200%;">File Name Format: <span style="color:red;">yyyy-mm-dd_lesson.mp3</span></span><br />
                        Sabbath School Lesson Title: <input type="text" name="title"><br />
                        Pass Code: <input type="text" name="passcode"><br />
                        <input type="submit" value="Update Audio Feed">

                        <?php
                       
                        
                    } else {

                        if (strlen(filter_input(INPUT_POST, 'passcode')) > 5 && md5(filter_input(INPUT_POST, 'passcode')) != $passcode) {
                            mail($email, "Lesson Cast", "Lesson Cast Login Failed.", "From: ".$email."\n");
                            echo "Authorization Denied";
                        } else {


                            include "id3.php";

                            $filename = date("Y-m-d_", strtotime('last Saturday')) . 'lesson.mp3';
                            echo "Filename: " . $filename;

                            $m = new mp3file('../media/' . $filename);
                            if (FALSE != $m->fd) {  // fopen return FALSE on error...
                                $a = $m->get_metadata();


                                $xmltitle = filter_input(INPUT_POST, 'title');
                                $xmlsubTitle = $authorName;
                                $xmlSummary = "<![CDATA[ ".$authorName." ]]>";
                                $xmlDescription = $authorName;
                                $xmlLink = $mediaFolder . $filename;
                                $xmlUrl = $mediaFolder . $filename;
                                $xmlLength = $a['Filesize'];
                                $xmlType = "audio/mpeg";
                                $xmlGuid = $mediaFolder . $filename;
                                $xmlContent = $mediaFolder . $filename;
                                $xmlThumbnail = $thumbNailImage;
                                $xmlDuration = $a['Length mm:ss'];
                                $xmlAuthor = $authorName;
                                $xmlPubDate = date(DateTime::RSS);





                                $dom = new DOMDocument();
                                $dom->load("../feed.xml");

                                $xpath = new DOMXPath($dom);
                                $items = $xpath->query('/rss/channel/item');

                                $item = $dom->createElement('item');

                                $title = $dom->createElement("title");
                                $textnode = $dom->createTextNode($xmltitle);
                                $title->appendChild($textnode);
                                $item->appendChild($title);

                                $subTitle = $dom->createElement("itunes:subtitle");
                                $textnode = $dom->createTextNode($xmlsubTitle);
                                $subTitle->appendChild($textnode);
                                $item->appendChild($subTitle);

                                $isummary = $dom->createElement("itunes:summary");
                                $textnode = $dom->createCDATASection($xmlSummary);
                                $isummary->appendChild($textnode);
                                $item->appendChild($isummary);

                                $description = $dom->createElement("description");
                                $textnode = $dom->createTextNode($xmlDescription);
                                $description->appendChild($textnode);
                                $item->appendChild($description);

                                $link = $dom->createElement("link");
                                $textnode = $dom->createTextNode($xmlLink);
                                $link->appendChild($textnode);
                                $item->appendChild($link);


                                $enclosure = $dom->createElement("enclosure");
                                $url = $dom->createAttribute("url");
                                $url->value = $xmlUrl;
                                $enclosure->appendChild($url);
                                $length = $dom->createAttribute("length");
                                $length->value = $xmlLength;
                                $enclosure->appendChild($length);
                                $type = $dom->createAttribute("type");
                                $type->value = $xmlType;
                                $enclosure->appendChild($type);
                                $item->appendChild($enclosure);


                                $guid = $dom->createElement("guid");
                                $textnode = $dom->createTextNode($xmlGuid);
                                $guid->appendChild($textnode);
                                $item->appendChild($guid);

                                $content = $dom->createElement("media:content");
                                $url = $dom->createAttribute("url");
                                $url->value = $xmlContent;
                                $content->appendChild($url);
                                $item->appendChild($content);

                                $thumbnail = $dom->createElement("media:thumbnail");
                                $url = $dom->createAttribute("url");
                                $url->value = $xmlThumbnail;
                                $thumbnail->appendChild($url);
                                $item->appendChild($thumbnail);

                                $iduration = $dom->createElement("itunes:duration");
                                $textnode = $dom->createTextNode($xmlDuration);
                                $iduration->appendChild($textnode);
                                $item->appendChild($iduration);

                                $author = $dom->createElement("author");
                                $textnode = $dom->createTextNode($xmlAuthor);
                                $author->appendChild($textnode);
                                $item->appendChild($author);

                                $iauthor = $dom->createElement("itunes:author");
                                $textnode = $dom->createTextNode($xmlAuthor);
                                $iauthor->appendChild($textnode);
                                $item->appendChild($iauthor);

                                $pubDate = $dom->createElement("pubDate");
                                $textnode = $dom->createTextNode($xmlPubDate);
                                $pubDate->appendChild($textnode);
                                $item->appendChild($pubDate);

                                $items->item(0)->parentNode->insertBefore($item, $items->item(0));


                                if ($dom->save("../feed.xml")) {
                                    echo "Title: " . $xmltitle . "<br />";
                                    echo "Filename: " . $filename . "<br />";
                                    echo "File Size: " . $xmlLength . "<br />";
                                    echo "Duration: " . $xmlDuration . "<br />";
                                } else
                                    echo "Error";
                            }
                            else{
                                
                                echo "Error: Failed to find mp3 file.";
                                
                            }
                        }
                    }
                    ?>
                </fieldset>
            </form>
        </div>
    </body>
</html>