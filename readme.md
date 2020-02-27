
## GDPR Portal

1. Overview
2. Requirements
3. Installation
4. Database
5. Workflow
6. Plugins
7. User guide

## 1. Overview

The main objective of the GDPR portal is to intergrate existing systems GDPR dataextracts through a common tool.

## 2. Requirements
Requirements are best determined using Server Requirements page of corresponding Laravel 6 version
Currently used version is 6.5.1

PHP version 7.3.*

SSH access to the server

Composer

## 3. Installation

* Make sure that composer is installed globally or install it in place

* Clone the repository

* Move into the directory

* Install the dependencies. `composer install`

* Make sure that .env file is present and configured as needed (copy .env.example to .env)

* Make sure that `/systemconfig/gdpr.ini` file is present and configured with the configuration details for the server and your requirements (copy gdpr.ini.example to .ini and fill in with your data)

* Either create application key manually or do that with a command `php artisan key:generate`

* In the `.env` file, make sure that APP_ENV=production and APP_DEBUG=false for production environments (this should prevent unneeded error detailed data exposure)

* The redirect URL addresses are in the form BASE-URL/auth/PLUGIN-name/callback

* If you need to change the email configuration. Open the config/mail.php file and set the needed values within the from element

* Create the database with `php artisan migrate` (this should create database tables needed)

* Finally run `php artisan queue:work` OR any process monitor for your OS to start the processes for the pluginshandling.

## 4. Database

//TODO

### 4.1 Status flags

####  for each case

status_processed | .
-----------------|-------
number of plugins + 1 (notification) |

status_flag

value | state
------|------
0 | System error; One or more systems reported a 400 or 404
1 | Initial status
2 | User not found
3 | Request successful

registrar

value | state
------|------
0 | Downloaded zip has Not been sent to registrar
1 | Downloaded zip has been sent to registrera at date (see sent_registrar)

sent_registrar

value | state
------|------
date | date when zip file was sent to registrar

progress

value | state
------|------
0 | Progress of request has been completed
1 | Request is under progress

plugins_processed

plugins_processed | .
------------------|------
number of plugins that have been processed |

download_status | .
----------------|-----
total download in % | 

downloaded | .
-----------|-------
value | state 
0 | No
1| Yes

#### statuses
//TODO

#### plugin
//TODO


## 5. Workflow

![Workflow](./public/images/guide/workflow.png)

## 6. Plugins

### Plugin pattern/structure
The plugin (a kind of controller) handles connection to given systems to request GDPR extracts from various systems. The Plugin for each system consists of three files; A main file, a process file to dispatch the plugin to a que and a configurations file containing credential- and configuration information if needed. This structure will be reworked in the future to allow easier integration. All plugins are handled by the PluginController.
e.g.

(1) SciproPlugin.php (core-file)

(2) ProcessScipro.php (job handler)

(3) Services.php (configuration and credentials)

![Pluginstructure](./public/images/guide/flow.png)

### Plugin workflow
The Plugincontroller dispatches each plugin in order to a job process explicitly defining which queue it should be dispatched to, passing retrieved information entered by the user. A database table is used to hold the jobs processes and also failed processes (e.g. unable to connect to a system or server).
Through the Plugin a connection to external systems should be achieved and a zip file containing gdpr data should be retrieved. In able to access this file, the plugin in most cases must be authorized by the system to access that particular information (client id and a client secret has to be issued if required by the system). 
Once the zip file has been retrieved from the external system it is stored and unpacked on the server disc identified by its case id. Once the entire queue has been processed and all files have been stored and unpacked the entire retrieved data will be packed and ready for downloading by the user. A mail will be sent to notify the user that the download is ready or if an error has occurred a mail will be sent to notify the user about the current status.

## Implementing a new plugin
//TODO

### Installed Packages
The following client libraries are installed.

Guzzle 6: 	
        
    use GuzzleHttp\Client;
	use GuzzleHttp\HandlerStack;
		
Kamermans:	

    use kamermans\OAuth2\GrantType\ClientCredentials;
    use kamermans\OAuth2\OAuth2Middleware;

### Passing arguments/objects
The Plugin should receive information from the PluginController by passing arguments (a search array and the plugin-object) to the constructor and should return a zip-file to be processed. The configuration file for the plugin should contain necessary data for connecting to the server e.g. client_id, client_secret, authorization code, callback uri, auth url, endpoint url.

### Response Status Codes

Status code | Description
------------ | -------------
200 | OK - The request has succeeded. The client can read the result of the request in the body and the headers of the response.
202| Accepted - The request has been accepted for processing, but the processing has not been completed.
204 | User not Found - The requested user could not be found.
400 | Bad Request - The request could not be understood by the server due to malformed syntax.
401 | Unauthorized - The request requires user authentication or, if the request included authorization credentials, authorization has been refused for those credentials.
500 | Internal Server Error.

## 7. User guide
The trialversion (release candidate) of the portal can be found under the following url:

https://methone.dsv.su.se

For access to the page, you must first be authenticated by SU IdP, needing the necessary entitlements. You will be redirected automatically for authentication. The trial version does not require the user to have the entitlement gdpr yet but this will be added to the production version.

![Dashboard](./public/images/guide/login.png)

When logged in, you can access the dashboard.

![Dashboard](./public/images/guide/dashboard.png)

In the Dashboard there is a form and a table to show the status of cases. Searching for GDPR extracts are done by entering information into the form. Input is made with a swedish personal identity number (Personnummer) or a co-ordination number in the format YYMMDD-NNNN, and/or valid e-mail address and/or SU User ID. The more information that is entered, the greater the hit can be expected in the systems searched. Validation of the form (according to defined rules) is active to help the user to make as little errors as possible with entry.

![Dashboard](./public/images/guide/validation.png)

A search is started by submitting the form and a case is generated. This is shown in the dashboard. The status of each system being searched is also displayed.

![Dashboard](./public/images/guide/status.png)

When the search is completed, the user receives an email and can log in to the dashboard again. Some searches may take time and the system works in the background.

![Dashboard](./public/images/guide/mail_finished.png)


When the search is completed, the table shows the status of each system (and cases if more have been processed) that have been searched and the user has the opportunity to download a single file containing files from all searched systems for each individual case. The file can also be sent directly to the registrar for registration.

![Dashboard](./public/images/guide/finished_status.png)

If a user is not registered in a system, this is displayed in the status and the status indication shows the color yellow, user not found.

![Dashboard](./public/images/guide/user_not_found.png)

A system error is indicated in red and then the entire request must be performed again later after the issue has been resolved.

![Dashboard](./public/images/guide/error_status.png)

After the request is completed and downloaded, cases can be removed manually. After 30 days the case and all files concerning the case is automatically deleted by the system. (//TODO)
