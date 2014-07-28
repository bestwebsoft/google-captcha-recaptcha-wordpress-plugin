=== Google Captcha (reCAPTCHA) ===
Contributors: bestwebsoft
Donate link: https://www.2checkout.com/checkout/purchase?sid=1430388&quantity=1&product_id=94
Tags: antispam, anti-spam, capcha, anti-spam security, arithmetic actions, captcha, captha, capcha, catcha, cpatcha, captcha theme, comment, digitize books, digitize newspapers, digitize radio shows, google, gogle, google captcha, login, lost password, re captcha, recaptcha, re-captcha, registration, shortcode, site keys, spam, text captcha.
Requires at least: 3.0
Tested up to: 3.9.1
Stable tag: 1.05
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

This plugin allows you to implement Google Captcha into web forms.

== Description ==

The Google Captcha plugin allows you to implement a super security captcha form into web forms. Google Captcha is a free CAPTCHA service that helps to digitize books, newspapers and old time radio shows.
This captcha can be used for login, registration, password recovery, comments forms.

http://www.youtube.com/watch?v=10ImOhmM0Cs

<a href="http://wordpress.org/plugins/google-captcha/faq/" target="_blank">FAQ</a>

<a href="http://support.bestwebsoft.com" target="_blank">Support</a>

= Copyrights for resources used in this plugin =

1. In Google Captcha plugin we used the "lib/recaptchalib.php" file. The Licence for this file is in the "lib/license.txt" file.
2. Everything else used in this plugin has been created by the Bestwebsoft team and is distributed under GPL license.

= Features =

* Actions: You can add Google Captcha to any standard form - login, comments etc.
* Actions: You can choose the users, for whom captcha will be hidden.
* Display: You can choose one of four standard Google Captcha themes.

= Recommended Plugins =

The author of the Google Captcha also recommends the following plugins:

* <a href="http://wordpress.org/plugins/updater/">Updater</a> - This plugin updates WordPress core and the plugins to the recent versions. You can also use the auto mode or manual mode for updating and set email notifications.
There is also a premium version of the plugin <a href="http://bestwebsoft.com/plugin/updater-pro/?k=f47f3eb3d739725d592249dbd129f7ff">Updater Pro</a> with more useful features available. It can make backup of all your files and database before updating. Also it can forbid some plugins or WordPress Core update.

= Translation =

* Brazilian Portuguese (pt_BR) (thanks to <a href="mailto:epeetz@gmail.com">Elton Fernandes Peetz Prado</a>)
* Polish (pl_PL) (thanks to <a href="mailto:ryszard.glegola@translanet.com">Ryszard Glegola</a>, www.translanet.com)
* Russian (ru_RU)
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

= How to use the other language files with CAPTCHA? = 

Here is an example for the German language files.

1. In order to use another language for WordPress it is necessary to set a WordPress version to the required language and in the configuration wp file - `wp-config.php` in the line `define('WPLANG', '');` you should enter `define('WPLANG', 'de_DE');`. If everything is done properly the admin panel will be in German.

2. Make sure the files `de_DE.po` and `de_DE.mo` are present in the plugin (the folder "Languages" in the plugin root).

3. If there are no such files you should copy the other files from this folder (for example, for Russian or Italian) and rename them (you should write `de_DE` instead of `ru_RU` in both files).

4. The files can be edited with the help of the program Poedit - http://www.poedit.net/download.php - please download this program, install it, open the file using this program (the required language file) and for each line in English you should write translation in German.

5. If everything is done properly all lines will be in German in the admin panel and in the front-end.

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
4. Lost password form with Google Captcha.
5. Registration form with Google Captcha.
6. Contact form with Google Captcha.

== Changelog ==

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
