<?php

class Syllabus_Admin_EmailManager
{
	private $app;
	private $ctrl;
	private $type;
	private $fromEmail;
	private $fromName;
	private $testEmail;
	private $testingOnly;
	private $subjectLine;
	private $attachments;
	private $ccRequest;
	private $emailLogId;
	private $templateInstance;

	private $schemas = array();

	public function __construct (Bss_Core_Application $app, $ctrl=null)
	{
		$this->app = $app;
		$this->ctrl = $ctrl;	// phasing this out...
		$this->fromEmail = $app->siteSettings->getProperty('email-default-address', 'ilearn@sfsu.edu');
		$this->fromName = "Syllabus";
		$this->testingOnly = $app->siteSettings->getProperty('email-testing-only', false);
		$this->testEmail = $app->siteSettings->getProperty('email-test-address', '');
		$this->subjectLine = "Syllabus Submission Reminder";
		$this->attachments = array();
		$this->ccRequest = false;
	}

	public function validEmailTypes ()
	{
		$types = array(
			'sendDueDateReminder',
		);

		return $types;
	}

	public function setTemplateInstance ($inst)
	{
		$this->templateInstance = $inst;
	}

 /**
	* Determines which email function to call.
	*
	* @param $type string to be used as name of function call (e.g. 'sendCourseRequested') 
	* @param $params array of variables needed by called function
	*/
	public function processEmail ($type, $params, $test=false)
	{	
		if (!in_array($type, $this->validEmailTypes()))
		{
			return false; 
			exit;
		}

		$this->type = $type;
		$fileType = lcfirst(str_replace('send', '', $type));
		$this->attachments = [];
		$this->ccRequest = false;

		$emailLog = $this->getSchema('Syllabus_Admin_EmailLog')->createInstance();
		$emailLog->type = ($test ? 'TEST: ' : '') . $type;
		$emailLog->creationDate = new DateTime;
		$emailLog->save();
		$this->emailLogId = $emailLog->id;

		// send email based on type
		$this->$type($params, $test);
	}

    public function getEmailAttachments ($emailType)
    {
        $attachments = array();
        $files = $this->getSchema('Syllabus_Files_File')->getAll();
        foreach ($files as $file)
        {
            if (in_array($emailType, $file->attachedEmailKeys))
            {
                $attachments[] = $file;
            }
        }

        return $attachments;
    }


	public function sendDueDateReminder ($data, $test)
	{
		$this->subjectLine = (!$test ? $data['email']->subject : 'Testing Syllabus Submission Reminder');
		$this->fromEmail = (!$test ? $data['email']->contactEmail : 'ilearn@sfsu.edu');
		$this->fromName = (!$test ? $data['email']->department->name . ' via Syllabus' : 'Syllabus');

		$params = [
			'|%FIRST_NAME%|' => $data['user']->firstName,
			'|%LAST_NAME%|' => $data['user']->lastName,
			'|%DUE_DATE%|' => (!$test ? $data['campaign']->dueDate : $data['reminder']->dueDate)->format('M j, Y g:ia'),
			'|%DEPARTMENT_NAME%|' => (!$test ? $data['email']->department->name : $data['reminder']->department),
			'|%SEMESTER%|' => (!$test ? $data['campaign']->semester->display : $data['reminder']->semester),
			'|%SUBMISSION_DESCRIPTION%|' => (!$test ? $data['campaign']->description : $data['reminder']->description),
			'signature' => (!$test ? $data['email']->signature : '<br>--'.$data['reminder']->department),
			'message_title' => 'Syllabus Submission Reminder',
		];

		$body = trim($data['email']->body);
		if ($this->hasContent($body))
		{
			$this->sendEmail($data['user'], $params, $body);	
		}
	}

	public function sendEmail($user, $params, $templateText, $templateFile=null)
	{
		if ($this->hasContent($templateText))
		{
			$messageTitle = $params['message_title'];
			$preppedText = strtr($templateText, $params);			
			$templateFileName = $templateFile ?? 'emailBody.email.tpl';
			$mail = ($this->templateInstance ? $this->createEmailMessage($templateFileName) : $this->ctrl->createEmailMessage($templateFileName));
			$mail->Subject = $this->subjectLine;

			$mail->set('From', $this->fromEmail);
			$mail->set('FromName', $this->fromName);
			$mail->set('Sender', $this->fromEmail);
			$mail->AddReplyTo($this->fromEmail, $this->fromName);

			$recipients = array();

			if ($this->testingOnly && $this->testEmail)
			{
				// send only to testing address
				$mail->AddAddress($this->testEmail, "Testing Syllabus");
				$recipients[] = -1;
			}
			elseif (is_array($user) && (count($user) > 1))
			{
				// send to multiple recipients
				foreach ($user as $recipient)
				{
					$recipient = is_array($recipient) ? array_shift($recipient) : $recipient;
					$mail->AddAddress($recipient->emailAddress, $recipient->fullName);
					$recipients[] = $recipient->id;
				}
			}
			else
			{
				// send to a single specified recipient
				if (is_array($user) && array_shift($user))
				{
					$user = array_shift($user);
				}				
				if ($user)
				{
					$email = $user->emailAddress ?? '';
					$name = ($user->fullName ?? $user->displayName) ?? (($user->firstName . ' ' . $user->lastName) ?? '');
					$id = $user->id;				
				}
				else
				{
					$email = '';
					$name = '';
					$id = -1;				
				}

				$mail->AddAddress($email, $name);
				$recipients[] = $id;
			}

			foreach ($this->attachments as $attachment)
			{
				$title = isset($attachment->title) ? $attachment->title : $attachment->remoteName;
				$mail->AddAttachment($attachment->getLocalFilename(true), $title);
			}
			if ($this->ccRequest && !$this->testingOnly && !$this->testEmail)
			{
				$mail->AddAddress($this->fromEmail, $this->fromName);
				$recipients[] = '[CC] ilearn@sfsu.edu';
			}

			$mail->getTemplate()->message = $preppedText;
			$mail->getTemplate()->messageTitle = $messageTitle;
			$mail->getTemplate()->signature = $params['signature'];
			
			$success = $mail->Send();
        
			// finish email log
			$emailLog = $this->getSchema('Syllabus_Admin_EmailLog')->get($this->emailLogId);
			$emailLog->recipients = implode(',', $recipients);
			$emailLog->subject = $this->subjectLine;
			$emailLog->body = $preppedText;
			$emailLog->attachments = $this->attachments;
			$emailLog->success = $success;
			$emailLog->save();
		}
	}

    public function createEmailTemplate ()
    {
        $template = $this->templateInstance;
        $template->setMasterTemplate(Bss_Core_PathUtils::path(dirname(__FILE__), 'resources', 'email.html.tpl'));
        return $template;
    }
    
    
    public function createEmailMessage ($contentTemplate = null)
    {
        $message = new Bss_Mailer_Message($this->app);

        if ($contentTemplate)
        {
            $tpl = $this->createEmailTemplate();
            if ($this->ctrl)
            {
            	$message->setTemplate($tpl, $this->ctrl->getModule()->getResource($contentTemplate));
            }
            else
            {
            	$message->setTemplate($tpl, $this->app->moduleManager->getModule('at:syllabus:master')->getResource($contentTemplate));
            }

        }
        
        return $message;
    }

	private function hasContent ($text)
	{
		return (strlen(strip_tags(trim($text))) > 1);
	}

	private function generateLink ($url='', $asAnchor=true, $linkText='')
	{
		$href = $this->app->baseUrl($url);
		if ($asAnchor)
		{
			$text = $linkText ?? $href;
			return '<a href="' . $href . '">' . $text . '</a>';
		}
		
		return $href;
	}

	private function getSchema($schemaName)
	{
		if (!isset($this->schemas[$schemaName]))
		{
			$schemaManager = $this->app->schemaManager;
			$this->schemas[$schemaName] = $schemaManager->getSchema($schemaName);
		}

		return $this->schemas[$schemaName];
	}


}