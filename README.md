V.0.7

Run composer update

Run php artisan serve

go to http://127.0.0.1:8000/post-data/
You can use the file 'test.oa' in root for upload test

STATUS returned : 

'size_exceed_2MB' : File size uploaded exceed 2MB limit

'invalid_json' : File content is not a JSON

'invalid_user' : Can be of below reasons 
a. recipient name is empty
b. recipient email is empty
c. recipient email is not a valid email

'invalid_issuer' : Can be of below reasons
1. Issuer identity is missing
2. Fail to connect to identity location
3. Issuer identity is not found in identity location
 
'invalid_signature' : Signature does not match

Files : 
Controller : 
VerifyController.php : Handles the verification

Routes :
web.php

Models:
VerifyLog.php : Database log using sqlite

Views : 
postform.blade.php : Form to submit data
log.blade.php : Table `verify_logs` content

DB : Sqlite

Screenshot : 
screenshot.jpg

**Note
* Sorry I only had Saturday and Sunday night to do this 
* and I only managed to do the main code.
* TODO :
* - Move save to table verify_logs to model -> store
* - Better test coverage
* - Better documentation
