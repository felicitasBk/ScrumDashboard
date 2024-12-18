<?php 

// hier wird die Mail Funktion implementiert

function sendmails($antwort,$empfaenger,$betreff){
		require_once('../PHPMailerAutoload.php');
		require '../db_conn.php';

		$mail = new PHPMailer;
						
		//$mail->SMTPDebug = SMTP::DEBUG_SERVER;
		$mail->SMTPDebug = 0;     
		$mail->isSMTP();                                      // Set mailer to use SMTP
		$mail->Host = "smtp.gmail.com";  // Specify main and backup SMTP servers
		$mail->SMTPAuth = true;                               // Enable SMTP authentication
		$mail->Username = 'agileview.wip@gmail.com';                 // SMTP username
		$mail->Password = 'agileview';                           // SMTP password
		$mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
		$mail->Port = 587;                                    // TCP port to connect to

		$mail->setFrom('agileview.wip@gmail.com', 'AgileView');
		$mail->addAddress($empfaenger);     // Add a recipient

		//$mail->isHTML(true);                                  // Set email format to HTML

		$mail->Subject = $betreff;
		//$mail->Body    = '<div style="border:2px solid red;">This is the HTML message body <b>in bold!</b></div>';
		$mail->Body = $antwort;

		if(!$mail->send()) {
			//echo 'Message could not be sent.';
			// echo 'Mailer Error: '. $mail->ErrorInfo;
		} else {
			
		}
	}
