<?php
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    require 'phpmailer/src/Exception.php';
    require 'phpmailer/src/PHPMailer.php';
    require 'phpmailer/src/SMTP.php';
    require 'credentials.php';

    //use YoutubeAPI to check if video is 
    echo('This Mailer tests if a youtube-Video is online and if it is embeddable'."<br>");

    //random testvideo, is gonna be a list later
    $videoid = "9IQ_ldV9z_A"; 
    $videoidoffline = "6CQEZ_kas0I";
    $ytquery = "https://www.googleapis.com/youtube/v3/videos?id=".$videoid."&key=".constant('YTKEY')."&part=snippet,status";
    echo $ytquery."\n";
    $response_json=file_get_contents($ytquery);
    $response=json_decode($response_json);
    if($response){
        echo "<br>Response ist angekommen:";
        if($response->pageInfo->totalResults != 0){
            echo "<br>".$response->items[0]->snippet->title;
            echo "<br>".'is embeddable: '.$response->items[0]->status->embeddable;
        } else {
            echo "<br>VIDEO IST OFFLINE";
        }
    }


    //flag for sending Mails
    $sendMail = false;

    if($sendMail){
        //send mail
        $mail = new PHPMailer;

        //$mail->SMTPDebug = 3;                               // Enable verbose debug output

        $mail->isSMTP();                                      // Set mailer to use SMTP
        $mail->Host = 'smpt.gmail.com';  // Specify main and backup SMTP servers
        $mail->SMTPAuth = true;                               // Enable SMTP authentication
        $mail->Username = 'EMAIL';                 // SMTP username
        $mail->Password = 'PASSWORD';                           // SMTP password
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('EMAIL', 'Yahya Orhan');
        $mail->addAddress('yahya.orhan@amc.or.at');     // Add a recipient

        $mail->isHTML(true);                                  // Set email format to HTML

        $mail->Subject = 'Here is the subject';
        $mail->Body    = 'This is the HTML message body <b>in bold!</b>';
        $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

        if(!$mail->send()) {
            echo 'Message could not be sent.';
            echo 'Mailer Error: ' . $mail->ErrorInfo;
        } else {
            echo 'Message has been sent';
        }
    }
?>