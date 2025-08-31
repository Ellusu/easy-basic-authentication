=== Easy Basic Authentication – Add basic auth to site or admin area ===
Contributors: matteoenna, ashkanahmadi
Tags: authentication, security, WordPress security, access control, login
Donate link: https://www.paypal.me/matteoedev/2.55
License URI: http://www.gnu.org/licenses/gpl.html
Requires at least: 5.0
Requires PHP: 7.2.5
Tested up to: 6.8.1
Stable tag: 3.8.0
License: GPLv2 or later

Secure your WordPress site with easy and effective basic authentication. Restrict access, monitor attempts, and enhance security.

== Description ==

The Easy Basic Authentication plugin provides a simple method to add basic authentication to your WordPress site. You can enable basic authentication for the entire site or only for the admin area by setting a custom username and password. Secure your site by restricting access only to authorized users.

**Try it on a free mock site: [click here](https://tastewp.org/plugins/easy-basic-authentication/)**

## Key Features

- **Simple Configuration:** With Easy Basic Authentication, you can easily set up basic authentication for your entire website or specifically for the admin area. Set a custom username and password to ensure secure access.

- **Admin Area Protection:** If you wish to restrict access to your WordPress admin area, Easy Basic Authentication allows you to do so quickly and effectively. Only users with the correct credentials will be able to access this critical part of your site.

- **Entire site protection:** If you wish, there is an option to extend the access limitation to the entire site and not just for your WordPress admin area, Easy Basic authentication allows you to do this quickly and effectively. Only users with the correct credentials will be able to access this critical part of your site.

- **Failed Access Logging:** The plugin keeps track of failed login attempts, helping you identify unauthorized access attempts. This is particularly useful for monitoring your site's security.

- **Access Log:** If you choose to enable this feature, Easy Basic Authentication allows you to log successful logins, providing a comprehensive overview of login activities on your site.

- **Easy Management:** The plugin's intuitive interface makes it simple to manage basic authentication settings. You can easily enable or disable basic authentication and adjust credentials to suit your needs.

- **Email Alert Functionality:** Easy Basic Authentication includes an email alert feature to notify you of unauthorized access attempts. You can receive email alerts when someone tries to access your site without proper credentials.

- **White List Functionality:** Easy Basic Authentication now includes a White List feature, allowing you to specify trusted IP addresses exempt from basic authentication. Configure this list to grant immediate access to known users or systems without requiring credentials, enhancing convenience while maintaining security.

Protect your WordPress site with basic authentication quickly and reliably. Easy Basic Authentication gives you control to ensure that only authorized users can access your online resources. Maintain your site's security and prevent unwanted access today with Easy Basic Authentication.


## Installation

1. Upload the Easy Basic Authentication plugin to your WordPress site.
2. Activate the plugin.
3. Configure the basic authentication settings from the WordPress admin panel.

## Usage

- Visit the plugin settings page to configure your desired basic authentication options.
- Choose whether to enable basic authentication for the entire site or just the admin area.
- Set a custom username and password for secure access.
- Monitor failed access attempts and access logs for added security.

## Troubleshooting: Resetting Basic Authentication

If you're having trouble logging in due to the basic authentication, you can reset it and regain access by following these steps:

1 **Connect to your website via FTP.**
2 **Navigate to the plugin directory:**

   <pre>wp-content/plugins/easy-basic-authentication/class/</pre>

3 **Locate the file:**

   <pre>easy-basic-authentication-class.php</pre>

4 **Find the following line:**

   <pre>add_action( 'init', array($this,'basic_auth_admin') );</pre>

5 **Comment out that line** by adding a `#` at the beginning:

   <pre>#add_action( 'init', array($this,'basic_auth_admin') );</pre>

6 **Save the file** and re-upload it to your server.

This will disable the basic authentication temporarily, allowing you to log in. Once logged in, you can adjust the plugin settings as needed.

If you need further assistance, feel free to reach out.

## GitHub Repository

You can find the source code and contribute to the project on GitHub: [Easy Basic Authentication on GitHub](https://github.com/Ellusu/easy-basic-authentication)
