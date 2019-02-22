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

# Time Zone
mysql_tzinfo_to_sql /usr/share/zoneinfo | mysql -u root -p mysql_password  

# config.php  
Required: Configure the database variables to match what is needed to access your RealDeviceMap Database.  
Optionally: You can add your Google Analytics and Google Ad-Sense ids to the config file. You also have a few different table properties to customize to your liking.  

# Thanks  
- Credit to Zyahko and his creditors for the base.  
