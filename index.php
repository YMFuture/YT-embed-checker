<!DOCTYPE html>
<html>
   <body style="text-align:center;">
        <?php
            use PHPMailer\PHPMailer\PHPMailer;
            use PHPMailer\PHPMailer\Exception;

            require 'phpmailer/src/Exception.php';
            require 'phpmailer/src/PHPMailer.php';
            require 'phpmailer/src/SMTP.php';
            require 'credentials.php';

            $videosToCheck = [];

            //use YoutubeAPI to check if video is 
            echo('This Mailer tests if a youtube-Video is online and if it is embeddable'."\n");

            //random testvideo, is gonna be a list later
            $videoid_single = "9IQ_ldV9z_A"; 
            $videoidoffline_single = "6CQEZ_kas0I";

            $row = 1;
            echo "Die Liste der Videos, die beim Button-Klick von der Youtube-API gecheckt werden:";
            if (($myfile = fopen("videos.csv", "r")) !== FALSE){
                while (($data = fgetcsv($myfile, 50, ";")) !== FALSE) {
                    if($row != 1){
                        $num = count(array_filter($data));
                        for ($c=1; $c < $num; $c++) {
                            array_push($videosToCheck, $data[$c]);
                            echo "<br>".$data[$c];
                        }
                    }
                    $row++;
                }
            }
            
            if(array_key_exists('button1', $_POST) && count($videosToCheck)>0){        
                echo "<br><br>Liste der Videos, die online sind:<br>";
                foreach ($videosToCheck as &$videoID) {
                    $ytquery = "https://www.googleapis.com/youtube/v3/videos?id=".$videoID."&key=".constant('YTKEY')."&part=snippet,status";
                    $response_json=file_get_contents($ytquery);
                    $response=json_decode($response_json);
                    if($response){
                        if($response->pageInfo->totalResults != 0){
                            echo "<br>".$response->items[0]->snippet->title;
                            echo "<br>".'is embeddable: '.$response->items[0]->status->embeddable."<br>";
                        } else { 
                            echo "<br>VIDEO IST OFFLINE<br><br>";
                        }
                    }
                }
                
                //send mail
                $mail = new PHPMailer;

                //$mail->SMTPDebug = 3;                               // Enable verbose debug output
                $mail->isSMTP();                                      // Set mailer to use SMTP
                $mail->Host = 'smtp-mail.outlook.com';                // Specify main and backup SMTP servers
                $mail->SMTPAuth = true;                               // Enable SMTP authentication

                $mail->Username = constant('EMAIL');                  // SMTP username
                $mail->Password = constant('PASSWORD');               // SMTP password
                $mail->SMTPSecure = 'tls';
                $mail->Port = 587;

                $mail->setFrom(constant('EMAIL'));
                $mail->addAddress(constant('RECIPIENT'));             // Add a recipient

                $mail->isHTML(true);                                  // Set email format to HTML

                $mail->Subject = 'Email-Report Videos';
                $mail->Body    = 'Die Zahl der funktionerenden Videos ist: <b>'.count($videosToCheck).'!</b>';
                //$mail->Body    = 'Die Zahl der funktionerenden Videos ist: <b>'.count($videosToCheck).'!</b>';
                $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

                if(!$mail->send()) {
                    echo 'Message could not be sent.';
                    echo 'Mailer Error: ' . $mail->ErrorInfo;
                } else {
                    echo '<p style="color:green"><b>Message has been sent</b></p>';
                }
            }
        ?>

        <form style="margin-top:20px;" method="post">
            <input type="submit" name="button1"
                    class="button" value="Check Videos and send Mail" />
        </form>
    </body>
</html>