# RaidBillBoard  
Billboard to show active raids sorted by which raids will end first with raid boss, raid level, geofence city, team control, ex-eligible raid filters.  

## Requirements  
* PHP >= 5.4+  
* Apache >= 2.4+ or Nginx >= 1.14.2  
* Apache `mod_rewrite` enabled.  
* Existing [RealDeviceMap](https://github.com/RealDeviceMap/RealDeviceMap) database.  

## Install  
1.) Clone the repository: `git clone https://github.com/versx/RealDeviceMap-RaidBillBoard raids` (change `raids` to your liking)  
2.) Change directory to the newly cloned folder: `cd raids`  
3.) Install Composer (https://getcomposer.org)  
4.) In the clone folder install the composer components required for the project: `composer install`  
5.) Copy the example configuration file: `cp config.example.php config.php`  

## Configuration  
**Time Zone**  
Execute the following on your SQL database to load the time zone tables in the mysql database in order for the time zone conversion to work.   
`mysql_tzinfo_to_sql /usr/share/zoneinfo`  

**config.php**  
_Core_  
1.) Set your time zone e.g. `America/New_York`  
2.) Set a startup location for any map objects to start at.  

_Database (db)_  
1.) Fill in your database IP or FQDN address.  
2.) Set your database username and password.  
3.) If you use a different database name than default, specify it.  

_Urls_  
1.) Set your pokemon images url including file extension and pokemon id placeholder in the url address.  
e.g. `http://example.com/images/pokemon/%s.png`, `http://example2.com/images/pokemon/%03d_000.png`, etc  
2.) Set your egg images url, same as pokemon, include the placeholder and file extension in the url address.  
e.g. `http://example.com/images/egg/%s.png`  

All other configuration options are default and optional at your discession.  

## Geofences  
Create or copy your existing geofences to the `geofences` folder. The following is the expected format:   
```
[City Name]  
0,0  
1,1  
2,2  
3,3  
```

## Thanks  
- Credit to Zyahko and his creditors for the base.  
