# RaidBillBoard  
Billboard to show active raids sorted by which raids will end first with raid boss, raid level, geofence city, team control, ex-eligible raid filters.  

# Install  
```  
git clone https://github.com/versx/RealDeviceMap-RaidBillBoard raids (change `raids` to your liking)  
cd `raids`  
Install Composer (https://getcomposer.org/)  
composer install
```

# Geofences  
Create or copy your existing geofences to the `geofences` folder. The following is the expected format:   
```
[City Name]  
0,0  
1,1  
2,2  
3,3  
```

# Configuration  
**Time Zone**  
mysql_tzinfo_to_sql /usr/share/zoneinfo | mysql -u root -p mysql_password  

**config.php**  
_Core_  
1.) Set your time zone e.g. `America/New_York`  
2.) Set a startup location for any map objects to start at.  

_Database (db)_  
1.) Fill in your database IP or FQDN address.  
2.) Set your database username and password.  
3.) If you use a different database name than default, specify it.  
All other options are default and optional at your discession.  

_Urls_  
1.) Set your pokemon images url including file extension and pokemon id placeholder in the url address. e.g. http://example.com/images/pokemon/%s.png http://example2.com/images/pokemon/%03d_000.png  
2.) Set your egg images url, same as pokemon, include the placeholder and file extension in the url address. e.g. http://example.com/images/egg/%s.png  

# Thanks  
- Credit to Zyahko and his creditors for the base.  
