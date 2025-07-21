# XML Parser

XML parser is a simple CLI application which accepts XML files as input and writes its contents to Google Spreadsheet.

## Pre-installation

CLI uses Service Account credentials to interact with spreadsheets.
To obtain credentials, the following steps must be executed:
1. Create a project on https://console.developers.google.com/apis/dashboard
2. Enable the Google Sheets API
3. Create Service account credentials
4. For the created service account, create a key with the type JSON
5. Finally, edit the sharing permissions for the spreadsheet you want to interact with and share Edit access to the client_email address you can find in the JSON file.

## Downloading a version of the application
Select the packaged application or the source application below before following the setup steps.

### Installing the packaged application
To install the packaged application, the following three steps may be executed.
```
wget https://github.com/rihardsgrig/xml_processor/releases/download/latest/xml-processor.phar
mv xml-processor.phar /usr/local/bin/xml-processor
chmod +x /usr/local/bin/xml-processor
```

### Installing from source
To install from the source, clone the repository into a location on your computer with the following commands:
```
git clone git@github.com:rihardsgrig/xml_processor.git
cd xml_processor
composer install
```

## Setup
Choose the following method to finish setting up your installation.


### Manual installation/setup
Once you have downloaded the application using one of the above steps, follow the below steps for a manual installation.
1. Create `config.yml` file under `$HOME/.xml-processor/` directory.
2. Set contents of the `config.yml` file to match the structure of the example below
```
xml_processor:
  log_location: 'PATH/TO/LOG/FILE/app.log'
  google_api_creds: 'PATH/TO/CREDS/FILE/credentials.json'
```


### Environment Variables
Environment variables can be used to store and provide the CLI configuration, removing the need for configuration files. Refer to the values in the example above of how to configure these environment variables.
* `XML_PROCESSOR_LOG_LOCATION` The environment variable for the log file location
* `XML_PROCESSOR_API_CREDENTIALS` The environment variable for the Google Sheets API credentials file location

## Configuration
CLI application uses cascading configuration on the user's machine to allow global and per-project credentials and overrides as needed.

The application will load configuration in the following order, with each step overriding matching array keys in the step prior:

1. Firstly, if it exists, a global configuration from `$HOME/.xml-processor/config.yml` is loaded.
2. Next, if it exists, a `config.yml` file in the project root will be loaded. (Only if the application is installed from the source)
3. Lastly, environment variables take overall precedence; however other configs won't be overridden.

## Usage
CLI application comes with three commands:
````
# Convenience command to list all sheets from a specific spreadsheet
xml-processor xml:list-sheets SPREADSHEET_ID

# Convenience command to add a new sheet to a specific spreadsheet
xml-processor xml:add-sheet SPREADSHEET_ID NEW_SHEET_NAME

# Command to process passed file by file location and write file contents to a spreadsheet. 
# File location argument can be a path to local file or URL to a remote file. Examples: "/PATH/TO/FILE/file.xml", "ftp://username:password@example.com/file.xml"
xml-processor xml:process-file FILE_LOCATION SPREADSHEET_ID NEW_SHEET_NAME
````