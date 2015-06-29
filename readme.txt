=== Google Captcha (reCAPTCHA) by BestWebSoft ===
Contributors: bestwebsoft
Donate link: http://bestwebsoft.com/donate/
Tags: antispam, anti-spam, capcha, anti-spam security, arithmetic actions, captcha, captha, capcha, catcha, cpatcha, captcha theme, comment, digitize books, digitize newspapers, digitize radio shows, google, gogle, google captcha, login, lost password, re captcha, recaptcha, re-captcha, registration, shortcode, site keys, spam, text captcha.
Requires at least: 3.0
Tested up to: 4.2.2
Stable tag: 1.17
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

This plugin allows you to implement Google Captcha into web forms.

== Description ==

The Google Captcha plugin allows you to implement a super security captcha form into web forms. Google Captcha is a free CAPTCHA service that helps to digitize books, newspapers and old time radio shows.
This captcha can be used for login, registration, password recovery, comments forms.

http://www.youtube.com/watch?v=10ImOhmM0Cs

<a href="http://www.youtube.com/watch?v=RUJ9VwZLFSY" target="_blank">Video instruction on Installation</a>

<a href="http://wordpress.org/plugins/google-captcha/faq/" target="_blank">FAQ</a>

<a href="http://support.bestwebsoft.com" target="_blank">Support</a>

= Copyrights for resources used in this plugin =

1. In Google Captcha plugin we used the "lib/recaptchalib.php" and "lib_v2/recaptchalib.php" file. The Licence for this file is in the "lib/license.txt" and "lib_v2/license.txt" file.
2. Everything else used in this plugin has been created by the Bestwebsoft team and is distributed under GPL license.

= Features =

* Actions: You can add Google Captcha to any standard form - login, comments etc.
* Actions: You can choose the users, for whom captcha will be hidden.
* Display: You can choose one of four standard Google Captcha themes.

= Recommended Plugins =

The author of the Google Captcha also recommends the following plugins:

* <a href="http://wordpress.org/plugins/updater/">Updater</a> - This plugin updates WordPress core and the plugins to the recent versions. You can also use the auto mode or manual mode for updating and set email notifications.
There is also a premium version of the plugin <a href="http://bestwebsoft.com/products/updater/?k=f47f3eb3d739725d592249dbd129f7ff">Updater Pro</a> with more useful features available. It can make backup of all your files and database before updating. Also it can forbid some plugins or WordPress Core update.

= Translation =

* Arabic (ar) (thanks to <a href="mailto:mor0cc0@live.com">SAID MOULLA</a>, www.aljoulane.ma)
* Brazilian Portuguese (pt_BR) (thanks to <a href="mailto:epeetz@gmail.com">Elton Fernandes Peetz Prado</a>)
* Bulgarian (bg_BG) (thanks to <a href="mailto:me@ygeorgiev.com">Yasen Georgiev</a>)
* Chinese Traditional (zh_TW) (thanks to <a href="mailto:nick20080808@gmail.com">Nick Lai</a>)
* German (de_DE) (thanks to <a href="mailto:fred.zimmer@medienconsulting.at">Fred Zimmer</a>,www.medienconsulting.at)
* Greek (el) (thanks to Dimitris Karantonis, www.soft4real.com/en-UK)
* Hindi (hi) (thanks to <a href="mailto:contact@developmentlogics.com">Development Logics Solutions Pvt Ltd</a>, www.developmentlogics.com)
* Italian (it_IT) (thanks to <a href="mailto:wart17@hotmail.com">Istvan</a>)
* Polish (pl_PL) (thanks to <a href="mailto:ryszard.glegola@translanet.com">Ryszard Glegola</a>, www.translanet.com)
* Russian (ru_RU)
* Spanish (es_ES) (thanks to <a href="mailto:cloudzeroxyz@gmail.com">Cloudzeroxyz</a>)
* Ukrainian (uk)

If you would like to create your own language pack or update the existing one, you can send <a href="http://codex.wordpress.org/Translating_WordPress" target="_blank">the text of PO and MO files</a> for <a href="http://support.bestwebsoft.com" target="_blank">BestWebSoft</a> and we'll add it to the plugin. You can download the latest version of the program for work with PO and MO files  <a href="http://www.poedit.net/download.php" target="_blank">Poedit</a>.

= Technical support =

Dear users, our plugins are available for free download. If you have any questions or recommendations regarding the functionality of our plugins (existing options, new options, current issues), please feel free to contact us. Please note that we accept requests in English only. All messages in another languages won't be accepted.

If you notice any bugs in the plugins, you can notify us about it and we'll investigate and fix the issue then. Your request should contain URL of the website, issues description and WordPress admin panel credentials.
Moreover we can customize the plugin according to your requirements. It's a paid service (as a rule it costs $40, but the price can vary depending on the amount of the necessary changes and their complexity). Please note that we could also include this or that feature (developed for you) in the next release and share with the other users then. 
We can fix some things for free for the users who provide translation of our plugin into their native language (this should be a new translation of a certain plugin, you can check available translations on the official plugin page).

== Installation ==

1. Upload the `google-captcha` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin via the 'Plugins' menu in WordPress.
3. Plugin settings are located in "BWS Plugins" > "Google Captcha".
4. Create a form in post and insert the shortcode [bws_google_captcha] into the form.

<a href="https://docs.google.com/document/d/1Nrccb-OLDN80yYjz_6-JPErdpZoslqfPV-g2IZ-GD0A/edit" target="_blank">View a Step-by-step Instruction on Google Captcha (reCAPTCHA) Installation</a>.

http://www.youtube.com/watch?v=RUJ9VwZLFSY

== Frequently Asked Questions ==

= How to get Google Captcha keys? =

You should go to the Settings page and click the 'here' link. Then you should enter your domain name in text field and click 'Create Key' button.
On the next screen you will see your public and private keys.

= How to hide Google Capthca for registered users? =

You should go to the Settings page and select the roles, for which you want to hide Google Captcha.
Then you must click 'Save Changes' button.

= How to chage Google Captcha style? =

You should go to the Settings page and select Theme from dropdown list.
Then you must click the 'Save Changes' button.

= Missing Google Captcha on the comment form? = 

You might have a theme where comments.php is not coded properly. 

Wopdpress version matters. 

(WP2 series) Your theme must have a tag `<?php do_action('comment_form', $post->ID); ?>` inside the file `/wp-content/themes/[your_theme]/comments.php`. 
Most WP2 themes already have it. The best place to put this tag is before the comment textarea, you can move it up if it is below the comment textarea.

(WP3 series) WP3 has a new function comment_form inside of `/wp-includes/comment-template.php`. 
Your theme is probably not up-to-date to call that function from comments.php.
WP3 theme does not need the code line `do_action('comment_form'`... inside of `/wp-content/themes/[your_theme]/comments.php`.
Instead it uses a new function call inside of comments.php: `<?php comment_form(); ?>`
If you have WP3 and captcha is still missing, make sure your theme has `<?php comment_form(); ?>`
inside of `/wp-content/themes/[your_theme]/comments.php` (please check the Twenty Ten theme's comments.php for proper example)

= How can I change the location of google captcha in the comments form? =

It depends on the comments form. If the hook call by means of which captcha works ('after_comment_field' or something like this) is present in the file comments.php, you can change captcha positioning by moving this hook call. Please find the file 'comments.php' in the theme and change position of the line

`do_action( 'comment_form_after_fields' );`

or any similar line - place it under the Submit button.

In case there is no such hook in the comments file of your theme, then, unfortunately, this option is not available.

= I would like to add Google Captcha to the custom form on my website. How can I do this? =

1. Install the Google Captcha plugin and activate it.
2. Open the file with the form (where you would like to add google captcha to).
3. Find a place to insert the code for the google captcha output.
4. Insert the necessary lines: 

`if( function_exists( 'gglcptch_display' ) ) { echo gglcptch_display(); } ;`

If the form is HTML you should insert the line with the PHP tags:

`<?php if( function_exists( 'gglcptch_display' ) ) { echo gglcptch_display(); } ; ?>`

= I have some problems with the plugin's work. What Information should I provide to receive proper support? =

Please make sure that the problem hasn't been discussed yet on our forum (<a href="http://support.bestwebsoft.com" target="_blank">http://support.bestwebsoft.com</a>). If no, please provide the following data along with your problem's description:

1. the link to the page where the problem occurs
2. the name of the plugin and its version. If you are using a pro version - your order number.
3. the version of your WordPress installation
4. copy and paste into the message your system status report. Please read more here: <a href="https://docs.google.com/document/d/1Wi2X8RdRGXk9kMszQy1xItJrpN0ncXgioH935MaBKtc/edit" target="_blank">Instuction on System Status</a>

== Screenshots ==

1. Google Captcha Settings page.
2. Comments form with Google Captcha.
3. Login form with Google Captcha.
4. Login form with Google Captcha version 2.
5. Lost password form with Google Captcha.
6. Registration form with Google Captcha.
7. Contact form with Google Captcha.

== Changelog ==

= V1.17 - 29.06.2015 =
* Bugfix : We fixed the bug with checking captcha in custom login form\register form\lost password form.

= V1.16 - 18.05.2015 =
* Bugfix : We fixed the bug with checking captcha when deleted 'recaptcha_widget_div'.
* Bugfix : We fixed the bug with using deprecated jQuery methods (thanks to Junio Vitorino, github.com/juniovitorino).
* NEW : The Arabic language file is added.
* NEW : The German language file is added.
* NEW : The Hindi language file is added.
* Update : We updated all functionality for wordpress 4.2.2.

= V1.15 - 09.04.2015 =
* Bugfix : We fixed the bug with captcha check for users from the list of exceptions.

= V1.14 - 07.04.2015 =
* Bugfix : Captcha work with comments forms with disabled javascript was fixed.
* Bugfix : Check reCaptcha v2 in PHP version 5.6 and above was fixed.
* NEW : The Italian language file is added.

= V1.13 - 13.02.2015 =
* Bugfix : We fixed the vulnerability when entering the dashboard.
* NEW : The Greek language file is added.

= V1.12 - 20.01.2015 =
* NEW : The Bulgarian language file is added.
* Update : We added the check of the "allow_url_fopen" option in PHP settings.
* Update : We added style for forms that use captcha v2.

= V1.11 - 30.12.2014 =
* Update : New Google Captcha version is added.
* Bugfix : We fixed the bug with displaying Google Captcha on the multisite register form.
* Bugfix : We fixed the bug with multilanguage plugin.
* Update : We updated all functionality for wordpress 4.1.

= V1.10 - 26.11.2014 =
* Update : We updated url and key names for Google Api.

= V1.09 - 14.11.2014 =
* Bugfix : We fixed the bug with joint displaying Google reCaptcha and Captcha.
* Bugfix : We fixed the bug with wrong answers in custom forms.
* Bugfix : We fixed the bug with login redirect.

= V1.08 - 14.10.2014 =
* New : The Spanish language file is added.
* Bugfix : Bug with multisite was fixed.
* Bugfix : Bug with user`s login was fixed.
* Bugfix : Bug when Contact Form submit was fixed.

= V1.07 - 02.09.2014 =
* New : The Chinese (Traditional) language file is added.

= V1.06 - 07.08.2014 =
* Bugfix : Security Exploit was fixed.
* Bugfix : The display of private key in the front-end was removed.

= V1.05 - 21.07.2014 =
* Bugfix : Problem with submitting form with Google Captcha is fixed.

= V1.04 - 18.07.2014 =
* Bugfix : Problem with displaying Google Captcha in Contact Form Pro (by BestWebSoft) is fixed.
* New : The Brazilian Portuguese language file is added.

= V1.03 - 06.06.2014 =
* New : The Polish language file is added.
* New : The Ukrainian language file is added.
* New : Renew captcha automaticly if was entered wrong value (thanks to Yaroslav Rogoza, github.com/rogyar).
* Update : We updated all functionality for wordpress 3.9.1.
* Bugfix : Problem with checking captcha for sites with https was fixed.

= V1.02 - 03.04.2014 =
* Update : Screenshots are updated.
* Update : BWS plugins section is updated.
* Bugfix : Plugin optimization is done.

= V1.01 - 05.02.2014 =
* Bugfix : The bug with adding comments from admin panel was fixed.
* NEW : "Settings", "FAQ", "Support" links were added to the plugin action page.
* NEW : Links on the plugins page were added.

= V1.0 =
* NEW : Ability to add Google Captcha into standard forms was added.

== Upgrade Notice ==

= V1.17 =
We fixed the bug with checking captcha in custom login form\register form\lost password form.

= V1.16 =
We fixed the bug with checking captcha when deleted 'recaptcha_widget_div'. We fixed the bug with using deprecated jQuery methods (thanks to Junio Vitorino, github.com/juniovitorino). The Arabic language file is added. The German language file is added. The Hindi language file is added. We updated all functionality for wordpress 4.2.2.

= V1.15 =
We fixed the bug with captcha check for users from the list of exceptions.

= V1.14 =
Captcha work with comments forms with disabled javascript was fixed. Check reCaptcha v2 in PHP version 5.6 and above was fixed. The Italian language file is added.

= V1.13 =
We fixed the vulnerability when entering the dashboard. The Greek language file is added.

= V1.12 =
The Bulgarian language file is added. We added the check of the "allow_url_fopen" option in PHP settings. We added style for forms that use captcha v2.

= V1.11 =
New Google Captcha version is added. We fixed the bug with displaying Google Captcha on the multisite register form. We fixed the bug with multilanguage plugin. We updated all functionality for wordpress 4.1.

= V1.10 =
We updated url and key names for Google Api.

= V1.09 =
We fixed the bug with joint displaying Google reCaptcha and Captcha. We fixed the bug with wrong answers in custom forms. We fixed the bug with login redirect.

= V1.08 =
The Spanish language file is added. Bug with multisite was fixed. Bug with user`s login was fixed. Bug when Contact Form submit was fixed.

= V1.07 =
The Chinese (Traditional) language file is added.

= V1.06 =
Security Exploit was fixed. The display of private key in the front-end was removed.

= V1.05 =
Problem with submitting form with Google Captcha is fixed.

= V1.04 =
Problem with displaying Google Captcha in Contact Form Pro (by BestWebSoft) is fixed. The Brazilian Portuguese language file is added.

= V1.03 =
The Polish language file is added. The Ukrainian language file is added. Renew captcha automaticly if was entered wrong value (thanks to Yaroslav Rogoza, github.com/rogyar). We updated all functionality for wordpress 3.9.1. Problem with checking captcha for sites with https was fixed.

= V1.02 =
Screenshots are updated. BWS plugins section is updated. Plugin optimization is done.

= V1.01 =
"Settings", "FAQ", "Support" links were added to the plugin action page. The links were added on the plugins page.
The bug with adding comments from admin panel was fixed.

= V1.0 =
Ability to add Google Captcha into standard forms was added.
