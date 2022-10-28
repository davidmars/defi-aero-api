<?php


namespace Utils;
use Exception;
use PHPMailer\PHPMailer\PHPMailer;

class Mailer
{
    /**
     * Qui reçoit les mails par défaut
     * @var string
     */
    public $defaultMailTo="";
    /**
     * Specify main and backup SMTP servers
     * exemple : smtp1.example.com;smtp2.example.com
     * @var string
     */
    public $host = '';
    /**
     * Enable SMTP authentication
     * @var bool
     */
    public $SMTPAuth = true;
    /**
     * SMTP username
     * exemple : user@example.com
     * @var string
     */
    public $username = "";
    /**
     * Nom associé à l'adresse email
     * exemple : "noreply" ou "formulaire contact" ou encore "Nicolas Dupont"
     * @var string
     */
    public $displayName="noreply";
    /**
     * SMTP password
     * exemple
     * azertyuiop78:!%
     * @var string
     */
    public $password = 'secret';
    /**
     * Enable TLS encryption, `ssl` also accepted
     * exemple tls or ssl
     * @var string
     */
    public $SMTPSecure = 'tls';
    /**
     * TCP port to connect to
     * exemple 587
     * @var int
     */
    public $port = 587;


    /**
     * @param string|string[] $emailTo Email à qui envoyer le mail
     * @param string $subject Objet du mail
     * @param string $htmlMessage Message formaté en html
     * @param string[] $attachments Listes des fichiers joints
     * @return bool true si ça a marché
     * @throws \Exception Si le mail par défaut n'est pas configuré
     */
    public function sendMail($emailTo,$subject,$htmlMessage,$attachments=[],$replyToMail="",$replyToName=""){

        if(!$emailTo){
            $emailTo=$this->defaultMailTo;
        }

        if(!$this->host){
            throw new \Exception("Configuration manquante; Il faut configurer le mail système ");
        }else{
            $mail = new PHPMailer(true);                    // Passing `true` enables exceptions
            try {
                //Server settings
                $mail->SMTPDebug = 0;                                 // pas d'output
                $mail->isSMTP();                                      // Set mailer to use SMTP
                $mail->Host = $this->host;  // Specify main and backup SMTP servers
                $mail->SMTPAuth = $this->SMTPAuth;                               // Enable SMTP authentication
                $mail->Username = $this->username;                 // SMTP username
                $mail->Password = $this->password;                           // SMTP password
                $mail->SMTPSecure = $this->SMTPSecure;                            // Enable TLS encryption, `ssl` also accepted
                $mail->Port = $this->port;                                    // TCP port to connect to

                //Recipient(s)
                $mail->setFrom($this->username, $this->displayName);
                if(is_array($emailTo)){
                    foreach ($emailTo as $recipient){
                        $mail->addAddress($recipient, $recipient);     // Add a recipient
                    }
                }else{
                    $mail->addAddress($emailTo, $emailTo);     // Add a recipient
                }

                if($replyToMail){
                    if(!$replyToName){
                        $replyToName=$replyToMail;
                    }
                    $mail->addReplyTo($replyToMail, $replyToName); //le reply to met le message dans les spams
                }


                //Content
                $mail->isHTML(true);                                  // Set email format to HTML
                $mail->CharSet = "UTF-8";
                $mail->Subject = $subject;
                $mail->Body    = $htmlMessage;
                $mail->AltBody = strip_tags($htmlMessage);

                //Attachments
                foreach ($attachments as $file){
                    $mail->addAttachment($file);         // Add attachments
                }
                //send
                return $mail->send();

            } catch (Exception $e) {
                throw new Exception($e->getMessage());
            }
        }
    }
}