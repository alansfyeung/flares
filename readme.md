![Flares logo](https://www.206acu.org.au/flares/assets/img/flareslogo.png)

# Flares Member Management System

Since 2017, Flares has primarily been used as a ribbon/decoration management systemm, and in late 2018 it became 
exclusively for ribbon management. Originally an acryonym for "Falcon - Leave Awards and REporting System", it is now stylised as 'Flares' in proper case. 

## Adding admin users to Flares

Admin users are able to access Flares to perform administrative tasks. The personnel records with Flares, on the other 
hand, are called 'members'. To add a new admin user directly (where they will login via the Flares webapp _/login_ page), 
use the CLI command: 

```
 PS C:\inetpub\206flares> php artisan users:create

 New username:
 >

```


## Registering admin users through Forums

The Forums Flares plugin contains the ability to register a new Flares admin user by linking that user to a Forums 
account. When a forums-linked admin user needs to access Flares, they must do so via SSO from the Forums. 

