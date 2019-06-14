-- TABLE OF CONTENTS --

Summary
Installation
Testing
Templates
API
Examples
Contacts


-- SUMMARY --

Antispam module by CleanTalk for protecting Bitrix sites from spambots' registratons
 and spam comments' publication.

Key features.
* No need in CAPTCHA, etc.
* Protection from spambots and manual spam comments.
* Automoderation - automatic publication of relevant comments.
* API - public methods allowing you to embed CleanTalk protection into your
   modules and templates.

What CleanTalk is.
 CleanTalk is a SaaS spam protection service for Web-sites.
 CleanTalk uses protection methods which are invisible for site visitors.
 Using CleanTalk eliminates needs in CAPTCHA, questions and answers, and other
  methods of protection, complicating the exchange of information on the site.

How it works.
 Comments and registration requests are sent to the CleanTalk cloud, data is
  tested with several methods on the cloud, then the site receives a response
  to approve or deny the message/registration.


-- INSTALLATION --

1. Get access key on http://CleanTalk.org.
2. Install CleanTalk module as usual and put access key into it's settings.


-- TESTING --

* Try to register account with "stop_email@example.com" as email address.
* Try to put comment with "stop_word" in it's body.

-- TEMPLATES --

Note: Module makes additions in next templates:
/bitrix/templates/.default/components/bitrix/system.auth.registration/.default/template.php
/bitrix/templates/.default/components/bitrix/blog/.default/bitrix/blog.post.comment/.default/template.php
/bitrix/templates/.default/components/bitrix/forum/.default/bitrix/forum.post_form/.default/template.php
Additions in these templates are needed for correct JavaScript detection.

All CleanTalk additions are between 'CLEANTALK template addon' tags and they will
 be removed correctly during deinstallation. See these templates for details.

It's recommended to insert CleanTalk addons into other templates you want to
 protect. You should use API method to do this.


-- API --

There are 5 API methods of CleanTalk class.

1. FormAddon($sType)
 @param string Type of form - 'register' or 'comment' only
 @return string Template addon text
 Universal method for template modification - adds some hidden fields to form.
 Use it in templates you want to protect.

2. CheckAllBefore(&$arEntity, $bSendEmail = FALSE)
 @param &array Entity to check (comment or new user)
 @param boolean Notify admin about errors by email or not (default FALSE)
 @return array|null Checking result or NULL when bad params
 Universal method for checking comment or new user for spam. It makes checking
  itself and sends errors notification to site admin if any (it sends one email
  per 15 minutes to avoid flood).

 $arEntity fields depends on $arEntity['type'] value:
  $arEntity['type'] = 'comment' defines the comment checking
  $arEntity['type'] = 'register' defines the new user checking

 So $arEntity fields must be at least:

  $arEntity['type'] = 'comment';
  $arEntity['sender_email'];
  $arEntity['sender_nickname'];
  $arEntity['sender_ip'];
  $arEntity['message_title']; // title of comment to check if any
  $arEntity['message_body'];  //  body of comment to check
  $arEntity['example_title']; // title of commented article if any
  $arEntity['example_body'];  //  body of commented article
  $arEntity['example_comments']; // titles and bodies of no more than 10 last
                                 //  approved comments concatenated by "\n\n".
  or

  $arEntity['type'] = 'register';
  $arEntity['sender_email'];
  $arEntity['sender_nickname'];
  $arEntity['sender_ip'];

 Method takes all other needed information automatically from form addon
  fields and so on.

 See code for details.
 Use this method in modules.
 You must call it from OnBefore* events.

3. CheckCommentAfter($module, $cid, $log_event = '')
 @param string Name of event generated module ('blog', 'forum', etc.)
 @param int ID of added entity (comment, message, etc)
 @param string System log event prefix, for logging
 Addon to CheckAllBefore method after comments/messages checking. It fills
  inner CleanTalk tables according to checking result for better spam
  accounting and logs CleanTalk events in system log.
 Use this method in modules.
 You must call it from OnAfter* events in comment/messages checking only, don't
  call it in new users checking.

4. SendFeedback($module, $id, $feedback)
 @param string Name of module that generated event ('blog', 'forum', etc.)
 @param int ID of added entity (comment, message, etc)
 @param string Feedback type - 'Y' or 'N' only
 Sending of manual moderation result to CleanTalk server. It makes CleanTalk
  service better.
 It's very important to inform CleanTalk server about manual moderation result.
  Please don't forget to do this.
 Use it in modules.

5. GetCleanTalkResume($module, $id)
 @param string Name of module that generated event ('blog', 'forum', etc.)
 @param int ID of entity (comment, message, etc)
 @return string|boolean Text of CleanTalk resume if any or FALSE if not
 Informational method. It returns CleanTalk resume of spam detection by
  comments or message ID.
 Use it in modules/templates, see example.


-- EXAMPLES --

/components/bitrix/forum.message.template/templates/.default
This folder contains example of forum meessage template that displays
 CleanTalk resume directly below message body to moderator. It's convenient for
 moderator, IMHO.


-- CONTACTS --

Feel free to contact us at https://cleantalk.org/contacts
